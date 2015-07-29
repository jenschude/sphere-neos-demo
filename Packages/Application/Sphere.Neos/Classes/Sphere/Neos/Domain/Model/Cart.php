<?php
namespace Sphere\Neos\Domain\Model;

/*                                                                        *
 * This script belongs to the Neos package "Sphere.Neos".                 *
 *                                                                        */

use Sphere\Core\Model\Cart\CartDraft;
use Sphere\Core\Model\Cart\LineItemCollection;
use Sphere\Core\Model\Common\Money;
use Sphere\Core\Model\Product\ProductProjection;
use Sphere\Core\Model\Product\ProductProjectionCollection;
use Sphere\Core\Request\Carts\CartByIdGetRequest;
use Sphere\Core\Request\Carts\CartCreateRequest;
use Sphere\Core\Request\Carts\CartUpdateRequest;
use Sphere\Core\Request\Carts\Command\CartAddLineItemAction;
use Sphere\Core\Request\Carts\Command\CartChangeLineItemQuantityAction;
use Sphere\Core\Request\Carts\Command\CartRecalculateAction;
use Sphere\Core\Request\Carts\Command\CartRemoveLineItemAction;
use Sphere\Core\Request\Products\ProductProjectionQueryRequest;
use Sphere\Neos\Client;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Log\SystemLoggerInterface;
use TYPO3\Flow\Object\ObjectManagerInterface;
use TYPO3\Flow\Session\SessionInterface;

/**
 *
 * @Flow\Scope("session")
 */
class Cart {

	/**
	 * The cart ID as it is used in the SPHERE.IO service
	 *
	 * @var string
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $currency = 'EUR';

	protected $country = 'DE';

	/**
	 * @Flow\InjectConfiguration
	 * @var array
	 */
	protected $settings;

	/**
	 * @Flow\Inject
	 * @var Client
	 */
	protected $client;

	/**
	 * @Flow\Transient
	 * @var \Sphere\Core\Model\Cart\Cart
	 */
	protected $remoteCart;

	/**
	 * @Flow\Inject
	 * @var SystemLoggerInterface
	 */
	protected $systemLogger;

	/**
	 * @Flow\Inject
	 * @var SessionInterface
	 */
	protected $session;

	/**
	 * Refreshes the remoteCart object if a cart already exists in the current session
	 *
	 * @param integer $reason
	 * @return void
	 */
	protected function initializeObject($reason) {
		$this->country = $this->settings['project']['country'];
		$this->currency = $this->settings['project']['currency'];
		if ($reason === ObjectManagerInterface::INITIALIZATIONCAUSE_RECREATED && !is_null($this->id)) {
			$request = $request = CartByIdGetRequest::ofId($this->id);
			$response = $this->client->execute($request);
			if (!$response->isError()) {
				$this->remoteCart = $response->toObject();
				$this->systemLogger->log(sprintf('Found existing cart "%s" for Neos session %s.', $this->id, $this->session->getId()), LOG_DEBUG);
			} else {
				$this->systemLogger->log(sprintf('Cart "%s" not found for Neos session %s.', $this->id, $this->session->getId()), LOG_DEBUG);
				$this->id = null;
				$this->remoteCart = null;
			}
		}
	}

	/**
	 * Returns id
	 *
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Adds a product to this cart
	 *
	 * @param ProductProjection $product
	 * @param integer $quantity
	 * @return void
	 */
	public function addProduct(ProductProjection $product, $quantity = 1) {
		$this->createNewCartIfNecessary();

		$updateItemRequest = CartUpdateRequest::ofIdAndVersion($this->remoteCart->getId(), $this->remoteCart->getVersion());
		$updateItemRequest->addAction(CartAddLineItemAction::ofProductIdVariantIdAndQuantity($product->getId(), $product->getMasterVariant()->getId(), $quantity));

		$response = $this->client->execute($updateItemRequest);
		if ($response->isError()) {
			$this->systemLogger->log(sprintf('Error while trying to add product "%s" (%s) to cart #%s: %s', $product->getName(), $product->getId(), $this->remoteCart->getId(), $response->getResponse()->getBody()), LOG_ERR);
		} else {
			$this->remoteCart = $response->toObject();
			$this->systemLogger->log(sprintf('Added product "%s" (%s) to cart #%s.', $product->getName(), $product->getId(), $this->remoteCart->getId()), LOG_DEBUG);
		}
	}

	/**
	 * Removes a line item from this cart
	 *
	 * @param string $itemId
	 * @return void
	 */
	public function removeItem($itemId) {
		if ($this->remoteCart instanceof \Sphere\Core\Model\Cart\Cart) {
			$updateItemRequest = CartUpdateRequest::ofIdAndVersion($this->remoteCart->getId(), $this->remoteCart->getVersion());
			$updateItemRequest->addAction(CartRemoveLineItemAction::ofLineItemId($itemId));

			$response = $this->client->execute($updateItemRequest);
			if ($response->isError()) {
				$this->systemLogger->log(sprintf('Error while trying to remove line item "%s" from cart #%s: %s', $itemId, $this->remoteCart->getId(), $response->getResponse()->getBody()), LOG_ERR);
			} else {
				$this->remoteCart = $response->toObject();
				$this->systemLogger->log(sprintf('Removed line item "%s" from cart #%s.', $itemId, $this->remoteCart->getId()), LOG_DEBUG);
			}
		}
	}

	/**
	 * Updates the quantities of the given line items
	 *
	 * @param array $quantities An array of the new quantities, indexed by the respective line item id
	 * @return void
	 */
	public function updateQuantities(array $quantities) {
		if ($this->remoteCart instanceof \Sphere\Core\Model\Cart\Cart) {
			$updateItemRequest = CartUpdateRequest::ofIdAndVersion($this->remoteCart->getId(), $this->remoteCart->getVersion());
			foreach ($quantities as $itemId => $quantity) {
				$updateItemRequest->addAction(CartChangeLineItemQuantityAction::ofLineItemIdAndQuantity($itemId, (integer)$quantity));
			}
            $updateItemRequest->addAction(CartRecalculateAction::of());
			$response = $this->client->execute($updateItemRequest);
			if ($response->isError()) {
				$this->systemLogger->log(sprintf('Error while trying to update quantities for cart #%s: %s', $this->remoteCart->getId(), $response->getResponse()->getBody()), LOG_ERR);
			} else {
				$this->remoteCart = $response->toObject();
				$this->systemLogger->log(sprintf('Updated quantities for cart #%s.', $this->remoteCart->getId()), LOG_DEBUG);
			}
		}
	}

	/**
	 * Returns line items of this cart, if any
	 *
	 * @return LineItemCollection
	 */
	public function getLineItems() {
		if ($this->remoteCart instanceof \Sphere\Core\Model\Cart\Cart) {
			return $this->remoteCart->getLineItems();
		} else {
			return LineItemCollection::of();
		}
	}

	public function getTotalPrice()
	{
		if ($this->remoteCart instanceof \Sphere\Core\Model\Cart\Cart) {
			return $this->remoteCart->getTotalPrice();
		} else {
			return Money::of($this->client->getContext())->setCurrencyCode($this->currency)->setCentAmount(0);
		}
	}
	/**
	 * Returns TRUE if this cart contains any items
	 *
	 * @return boolean
	 */
	public function hasLineItems() {
		if ($this->remoteCart instanceof \Sphere\Core\Model\Cart\Cart) {
			return count($this->remoteCart->getLineItems()->toArray()) > 0;
		} else {
			return FALSE;
		}
	}


	/**
	 * Creates a new Cart via the SPHERE.IO API
	 *
	 * @return \Sphere\Core\Model\Cart\Cart
	 * @Flow\Session(autoStart = true)
	 */
	protected function createNewCartIfNecessary() {
		if ($this->id !== NULL) {
			return null;
		}

		$cartDraft = CartDraft::ofCurrency($this->currency);
		$cartDraft->setCountry($this->country);
		$request = CartCreateRequest::ofDraft($cartDraft);

		$this->remoteCart = $this->client->execute($request)->toObject();
		$this->id = $this->remoteCart->getId();
		$this->systemLogger->log(sprintf('Created a new cart "%s" for Neos session %s.', $this->id, $this->session->getId()), LOG_DEBUG);
	}
}
