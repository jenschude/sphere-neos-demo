<?php
namespace Sphere\Neos\Eel;

/*                                                                                                  *
 *                                                                                                  */

use Sphere\Core\Client;
use Sphere\Core\Model\Common\LocalizedString;
use Sphere\Core\Model\Product\Product;
use Sphere\Core\Request\Products\ProductFetchByIdRequest;
use TYPO3\Eel\ProtectedContextAwareInterface;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Resource\ResourceManager;
use TYPO3\Media\Domain\Repository\ImageRepository;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;

/**
 * ProductHelper
 */
class ProductsHelper implements ProtectedContextAwareInterface {

	/**
	 * @Flow\Inject
	 * @var ResourceManager
	 */
	protected $resourceManager;

	/**
	 * @Flow\Inject
	 * @var ImageRepository
	 */
	protected $imageRepository;

	/**
	 * @Flow\InjectConfiguration
	 * @var array
	 */
	protected $settings;

	/**
	 * @var \Sphere\Core\Client;
	 */
	protected $client;

	/**
	 * @return void
	 */
	public function initializeObject() {
		LocalizedString::setDefaultLanguage('en');

		$this->client = new Client($this->settings['client']);
	}

	/**
	 *
	 *
	 * @param \TYPO3\TYPO3CR\Domain\Model\NodeInterface $node
	 * @param string $productId
	 * @return object
	 * @throws \Exception
	 */
	public function findProduct(NodeInterface $node, $productId) {
		$response = $this->client->execute(new ProductFetchByIdRequest($productId));

		$product = $response->json();
		return Product::fromArray($product['masterData']['current']);
	}

	/**
	 * All methods are considered safe
	 *
	 * @param string $methodName
	 * @return boolean
	 */
	public function allowsCallOfMethod($methodName) {
		return TRUE;
	}
}