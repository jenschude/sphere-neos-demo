<?php
namespace Sphere\Neos\Domain\Repository;

/*                                                                        *
 * This script belongs to the Neos package "Sphere.Neos".                 *
 *                                                                        */

use Sphere\Core\Model\Product\ProductProjection;
use Sphere\Core\Model\Product\ProductProjectionCollection;
use Sphere\Core\Request\Products\ProductProjectionByIdGetRequest;
use Sphere\Core\Request\Products\ProductProjectionBySkuGetRequest;
use Sphere\Core\Request\Products\ProductProjectionBySlugGetRequest;
use Sphere\Core\Request\Products\ProductProjectionSearchRequest;
use Sphere\Neos\Client;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\TYPO3CR\Domain\Model\Node;

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
	public function findByQuery($query = NULL, $defaultQuery = null) {
		// FIXME: Implement language
		$language = 'en';

		if (empty($query)) {
			$query = $defaultQuery;
		}

		$request = ProductProjectionSearchRequest::of($this->client->getContext());
		if (!empty($query)) {
			$request->addParam('text.' . $language, $query);
		}

		$response = $this->client->execute($request);

		$productProjectionCollection = $response->toObject();

		if (!$productProjectionCollection instanceof ProductProjectionCollection) {
			return NULL;
		}
		return $productProjectionCollection;
	}

	protected function getContext(Node $node = null)
	{
		$context = $this->client->getContext();
		if (!is_null($node)) {
			$context->setLanguages($node->getContext()->getDimensions()['language']);
		}

		return $context;
	}

	/**
	 * Finds a ProductProjection by searching for a product (variant) with the given slug
	 *
	 * @param string $slug The slug, for example "long-sleeve-shirt-xl"
	 * @return ProductProjection The found product or NULL
	 */
	public function findOneBySlug($slug, Node $node = null) {
		if ($slug == '') {
			return NULL;
		}
		if (isset($this->productProjectionsBySlug[$slug])) {
			$this->productProjectionsBySlug[$slug]->setContext($this->getContext($node));
			return $this->productProjectionsBySlug[$slug];
		}
		if (isset($this->productProjectionsById[$slug])) {
			$this->productProjectionsById[$slug]->setContext($this->getContext($node));
			return $this->productProjectionsById[$slug];
		}

		$request = ProductProjectionBySlugGetRequest::ofSlugAndContext($slug, $this->getContext($node));
		$request->expand('productType');
		$response = $this->client->execute($request);
		$productProjection = $response->toObject();
		if (!$productProjection instanceof ProductProjection) {
			return NULL;
		}
		$this->productProjectionsById[$productProjection->getId()] = $productProjection;
		$this->productProjectionsBySlug[$productProjection->getSlug()->__toString()] = $productProjection;

		return $productProjection;
	}

	/**
	 * Finds a ProductProjection by searching for a product (variant) sku
	 *
	 * @param string $sku The SKU, for example "rocket-r58-mark2"
	 * @return ProductProjection The found product or NULL
	 */
	public function findOneBySku($sku, Node $node = null) {
		if ($sku == '') {
			return NULL;
		}
		if (!isset($this->productProjectionsBySku[$sku])) {
			$request = ProductProjectionBySkuGetRequest::ofSku($sku, $this->getContext($node));
			$request->expand('productType');
			$response = $this->client->execute($request);
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
	public function findOneById($id, Node $node = null) {
		if ($id == '') {
			return NULL;
		}
		if (!isset($this->productProjectionsById[$id])) {
			$request = ProductProjectionByIdGetRequest::ofId($id, $this->getContext($node));
			$request->expand('productType');
			$response = $this->client->execute($request);
			$productProjection = $response->toObject();
			if (!$productProjection instanceof ProductProjection) {
				return NULL;
			}
			$this->productProjectionsById[$id] = $productProjection;
			$this->productProjectionsBySlug[$productProjection->getSlug()->__toString()] = $productProjection;
		}
		$this->productProjectionsById[$id]->setContext($this->getContext($node));

		return $this->productProjectionsById[$id];
	}

}
