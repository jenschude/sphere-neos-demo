<?php
namespace Sphere\Neos\Eel;

/*                                                                        *
 * This script belongs to the Neos package "Sphere.Neos".                 *
 *                                                                        */

use Sphere\Core\Model\Common\LocalizedString;
use Sphere\Core\Model\Product\Product;
use Sphere\Core\Model\Product\ProductProjection;
use Sphere\Neos\Client;
use Sphere\Neos\Domain\Model\Cart;
use Sphere\Neos\Domain\Repository\ProductRepository;
use TYPO3\Eel\ProtectedContextAwareInterface;
use TYPO3\Flow\Annotations as Flow;

class ProductsHelper implements ProtectedContextAwareInterface {

	/**
	 * @Flow\Inject
	 * @var ProductRepository
	 */
	protected $productRepository;

	/**
	 * @Flow\Inject
	 * @var Cart
	 */
	protected $cart;

	/**
	 * @Flow\Inject
	 * @var Client
	 */
	protected $client;

	/**
	 * Find a product by the given SKU
	 *
	 * @param string $sku
	 * @return Product
	 */
	public function findProductBySku($sku) {
		return $this->productRepository->findOneBySku($sku);
	}

	/**
	 * Find a product by the given slug
	 *
	 * @param string $slug
	 * @return Product
	 */
	public function findProductBySlug($slug) {
		return $this->productRepository->findOneBySlug($slug);
	}

	/**
	 * Find products by the given query string
	 *
	 * @param string $query
	 * @return array
	 */
	public function findProducts($query = NULL, $defaultQuery = NULL) {
		return $this->productRepository->findByQuery($query, $defaultQuery);
	}

	/**
	 *
	 *
	 * @param ProductProjection $product
	 * @return array
	 */
	public function getProductAttributes(ProductProjection $product) {
		$productType = $product->getProductType()->getObj();
		if ($productType === NULL) {
			return array();
		}

		$labels = [];
		foreach ($productType['attributes'] as $attribute) {
			$labels[$attribute['name']] = new LocalizedString($attribute['label'], $this->client->getContext());
		}
		$attributes = [];
		foreach ($product->getMasterVariant()->getAttributes() as $attribute) {
			$data = [];
			$data['name'] = $attribute->getName();
			$data['value'] = $attribute->getValue();
			$data['label'] = $labels[$data['name']];
			$attributes[] = $data;
		}

		return $attributes;
	}

	/**
	 * Retrieve the current cart
	 *
	 * @return Cart
	 */

	public function getCart() {
		return $this->cart;
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
