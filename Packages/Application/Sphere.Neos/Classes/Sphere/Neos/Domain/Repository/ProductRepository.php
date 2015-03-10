<?php
namespace Sphere\Neos\Domain\Repository;

/*                                                                        *
 * This script belongs to the Neos package "Sphere.Neos".                 *
 *                                                                        */

use Sphere\Core\Model\Product\ProductProjection;
use Sphere\Core\Model\Product\ProductProjectionCollection;
use Sphere\Core\Request\Products\ProductProjectionFetchByIdRequest;
use Sphere\Core\Request\Products\ProductProjectionFetchBySkuRequest;
use Sphere\Core\Request\Products\ProductProjectionFetchBySlugRequest;
use Sphere\Core\Request\Products\ProductsSearchRequest;
use Sphere\Neos\Client;
use TYPO3\Flow\Annotations as Flow;

/**
 *
 * @Flow\Scope("singleton")
 */
class ProductRepository {

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
	 * A cache for ProductionProjections which have been fetched earlier
	 *
	 * @var array
	 */
	protected $productProjectionsBySlug = array();

	/**
	 * A cache for ProductionProjections which have been fetched earlier
	 *
	 * @var array
	 */
	protected $productProjectionsBySku = array();

	/**
	 * A cache for ProductionProjections which have been fetched earlier
	 *
	 * @var array
	 */
	protected $productProjectionsById = array();

	/**
	 * FIXME: Not tested yet
	 *
	 * @param string $query
	 * @return
	 */
	public function findByQuery($query = NULL) {
		// FIXME: Implement language
		$language = 'en';

		$request = new ProductsSearchRequest($this->client->getContext());
		if (!is_null($query)) {
			$request->addParam('text.' . $language, $query);
		}

		$response = $this->client->execute(new ProductsSearchRequest());

		$productProjectionCollection = $response->toObject();

		if (!$productProjectionCollection instanceof ProductProjectionCollection) {
			return NULL;
		}
		return $productProjectionCollection;
	}

	/**
	 * Finds a ProductProjection by searching for a product (variant) with the given slug
	 *
	 * @param string $slug The slug, for example "long-sleeve-shirt-xl"
	 * @return ProductProjection The found product or NULL
	 */
	public function findOneBySlug($slug) {
		if ($slug == '') {
			return NULL;
		}
		if (!isset($this->productProjectionsBySlug[$slug])) {
			$response = $this->client->execute(new ProductProjectionFetchBySlugRequest($slug, $this->client->getContext()));
			$productProjection = $response->toObject();
			if (!$productProjection instanceof ProductProjection) {
				return NULL;
			}
			$this->productProjectionsById[$productProjection->getId()] = $productProjection;
			$this->productProjectionsBySlug[$productProjection->getSlug()->__toString()] = $productProjection;
		}
		return $this->productProjectionsBySlug[$slug];
	}

	/**
	 * Finds a ProductProjection by searching for a product (variant) sku
	 *
	 * @param string $sku The SKU, for example "rocket-r58-mark2"
	 * @return ProductProjection The found product or NULL
	 */
	public function findOneBySku($sku) {
		if ($sku == '') {
			return NULL;
		}
		if (!isset($this->productProjectionsBySku[$sku])) {
			$response = $this->client->execute(new ProductProjectionFetchBySkuRequest($sku, $this->client->getContext()));
			$productProjection = $response->toObject();
			if (!$productProjection instanceof ProductProjection) {
				return NULL;
			}
			$this->productProjectionsById[$productProjection->getId()] = $productProjection;
			$this->productProjectionsBySku[$sku] = $productProjection;
			$this->productProjectionsBySlug[$productProjection->getSlug()->__toString()] = $productProjection;
		}
		return $this->productProjectionsBySku[$sku];
	}

	/**
	 * Finds a ProductProjection by searching for the given (internal) identifier
	 *
	 * @param string $id The id, for example "308913e8-2d04-4468-938b-ecb8a51dac4e"
	 * @return ProductProjection The found product or NULL
	 */
	public function findOneById($id) {
		if ($id == '') {
			return NULL;
		}
		if (!isset($this->productProjectionsById[$id])) {
			$response = $this->client->execute(new ProductProjectionFetchByIdRequest($id, $this->client->getContext()));
			$productProjection = $response->toObject();
			if (!$productProjection instanceof ProductProjection) {
				return NULL;
			}
			$this->productProjectionsById[$id] = $productProjection;
			$this->productProjectionsBySlug[$productProjection->getSlug()->__toString()] = $productProjection;
		}
		return $this->productProjectionsById[$id];
	}

}
