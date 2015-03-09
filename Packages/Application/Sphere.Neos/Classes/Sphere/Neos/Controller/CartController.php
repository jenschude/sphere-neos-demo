<?php
namespace Sphere\Neos\Controller;

/*                                                                        *
 * This script belongs to the Neos package "Sphere.Neos".                 *
 *                                                                        */

use Sphere\Core\Model\Product\ProductProjection;
use Sphere\Neos\Domain\Model\Cart;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Mvc\Controller\ActionController;

class CartController extends ActionController {

	/**
	 * @Flow\Inject
	 * @var Cart
	 */
	protected $cart;

	/**
	 * Adds the given product to the cart
	 *
	 * @param ProductProjection $product
	 * @param integer $amount
	 * @return void
	 */
	public function addProductAction(ProductProjection $product, $amount = 1) {
		$this->cart->addProduct($product, $amount);
		// FIXME: redirect should use node:
		$this->redirectToUri('/en/cart.html');
	}

	/**
	 * Removes the given line item from the cart
	 *
	 * @param string $itemId
	 * @return void
	 */
	public function removeItemAction($itemId) {
		$this->cart->removeItem($itemId);
		// FIXME: redirect should use node:
		$this->redirectToUri('/en/cart.html');
	}

	/**
	 * Updates the quantity of items which are already present in the cart
	 *
	 * @param array $quantities
	 * @return void
	 */
	public function updateQuantitiesAction($quantities) {
		$this->cart->updateQuantities($quantities);
		// FIXME: redirect should use node:
		$this->redirectToUri('/en/cart.html');
	}

}
