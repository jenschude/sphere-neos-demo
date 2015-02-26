<?php
namespace Sphere\Neos\Domain\Service;

/*                                                                                                  *
 *                                                                                                  */

use Sphere\Core\Client;
use Sphere\Core\Config;
use Sphere\Core\Model\Common\Context;
use Sphere\Core\Model\Product\Product;
use Sphere\Core\Request\Products\ProductProjectionFetchBySkuRequest;
use Sphere\Core\Request\Products\ProductProjectionFetchBySlugRequest;
use Sphere\Core\Request\Products\ProductsSearchRequest;
use Sphere\Core\Response\SingleResourceResponse;
use TYPO3\Flow\Annotations as Flow;

/**
 * Product Service
 */
class ProductService{

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
	 * @Flow\Inject
	 * @var \TYPO3\Flow\I18n\Service
	 */
	protected $i18nService;

	protected $context;

	protected $map;

	/**
	 * @return Context
	 */
	protected function getContext()
	{
		if (is_null($this->context)) {
			$fallBack = $this->i18nService->getConfiguration()->getFallbackRule()['order'];
			$languages = array_reverse($fallBack);
			$this->context = new Context();
			$this->context->setLanguages($languages);
		}

		return $this->context;
	}

	/**
	 * @return void
	 */
	public function initializeObject() {
		$config = new Config();
		$config->fromArray($this->settings['client'])->setContext($this->getContext());
		$this->client = new Client($config);
	}

	/**
	 *
	 *
	 * @param string $sku
	 * @return Product
	 */
	public function findProductBySku($sku) {
		if ($sku == '') {
			return NULL;
		}
		if (!isset($this->map[$sku])) {
			$response = $this->client->execute(new ProductProjectionFetchBySkuRequest($sku));
			$this->map[$sku] = $response->toObject();
		}
		/** @var SingleResourceResponse $response*/
		return $this->map[$sku];
	}

	/**
	 *
	 *
	 * @param string $slug
	 * @return Product
	 */
	public function findProductBySlug($slug) {
		if ($slug == '') {
			return NULL;
		}
		if (!isset($this->map[$slug])) {
			$response = $this->client->execute(new ProductProjectionFetchBySlugRequest($slug, $this->getContext()));
			$this->map[$slug] = $response->toObject();
		}
		/** @var SingleResourceResponse $response*/
		return $this->map[$slug];
	}

	/**
	 * @param string $search
	 * @return array
	 */
	public function findProducts($search = null)
	{
		if (!isset($this->map[$search])) {
			$request = new ProductsSearchRequest();
			$request->addParam('text.en', $search);

			$response = $this->client->execute($request);

			$this->map[$search] = $response->toObject();
		}

		return $this->map[$search];
	}
}
