<?php
namespace Sphere\Neos\Eel;

/*                                                                                                  *
 *                                                                                                  */

use Sphere\Core\Client;
use Sphere\Core\Model\Product\Product;
use Sphere\Neos\Domain\Service\CartService;
use Sphere\Neos\Domain\Service\ProductService;
use TYPO3\Eel\ProtectedContextAwareInterface;
use TYPO3\Flow\Annotations as Flow;

class ProductsHelper implements ProtectedContextAwareInterface {

	/**
	 * @Flow\Inject
	 * @var ProductService
	 */
	protected $productService;

	/**
	 * @Flow\Inject
	 * @var CartService
	 */
	protected $cartService;
	/**
	 *
	 *
	 * @param string $sku
	 * @return Product
	 */
	public function findProductBySku($sku) {
		return $this->productService->findProductBySku($sku);
	}

	/**
	 *
	 *
	 * @param string $slug
	 * @return Product
	 */
	public function findProductBySlug($slug) {
		return $this->productService->findProductBySlug($slug);
	}

	/**
	 * @param string $search
	 * @return array
	 */
	public function findProducts($search = null)
	{
		return $this->productService->findProducts($search);
	}

	public function getAttributes($product)
	{
		return $this->productService->getAttributes($product);
	}

	public function getCart()
	{
		return $this->cartService->getCart();
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
