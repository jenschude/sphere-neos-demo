<?php
namespace Sphere\Neos\Domain\Service;

/*                                                                                                  *
 *                                                                                                  */

use Sphere\Core\Client;
use Sphere\Core\Model\Common\Context;
use Sphere\Core\Model\Common\LocalizedString;
use Sphere\Core\Model\Product\Product;
use Sphere\Core\Model\Product\ProductProjection;
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
	 * @var ClientService
	 */
	protected $clientService;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\I18n\Service
	 */
	protected $i18nService;

	protected $map;

	protected $context;

	/**
	 * @return Context
	 */
	protected function getContext()
	{
		return $this->clientService->getContext();
	}

	/**
	 * @return Client
	 */
	protected function getClient()
	{
		return $this->clientService->getClient();
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
			$response = $this->getClient()->execute(new ProductProjectionFetchBySkuRequest($sku));
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
			$request = new ProductProjectionFetchBySlugRequest($slug, $this->getContext());
			$request->expand('productType');
			$response = $this->getClient()->execute($request);
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

			$language = $this->i18nService->getConfiguration()->getCurrentLocale()->getLanguage();
			$request->addParam('text.' . $language, $search);

			$response = $this->getClient()->execute($request);

			$this->map[$search] = $response->toObject();
		}

		return $this->map[$search];
	}

	public function getAttributes($product)
	{
		/**
		 * @var ProductProjection $product
		 */
		$productType = $product->getProductType()->getObj();

		$labels = [];
		foreach ($productType['attributes'] as $attribute) {
			$labels[$attribute['name']] = new LocalizedString($attribute['label'], $this->getContext());
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
}
