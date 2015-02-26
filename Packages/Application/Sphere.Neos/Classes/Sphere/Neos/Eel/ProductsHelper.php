<?php
namespace Sphere\Neos\Eel;

/*                                                                                                  *
 *                                                                                                  */

use Sphere\Core\Client;
use Sphere\Core\Model\Product\Product;
use Sphere\Neos\Domain\Service\ProductService;
use TYPO3\Eel\ProtectedContextAwareInterface;
use TYPO3\Flow\Annotations as Flow;

/**
 * ProductHelper
 */
class ProductsHelper implements ProtectedContextAwareInterface {

	/**
	 * @Flow\Inject
	 * @var ProductService
	 */
	protected $productService;

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
