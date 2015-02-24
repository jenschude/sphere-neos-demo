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
		$response = $this->client->execute(new ProductProjectionFetchBySkuRequest($sku));
		/** @var SingleResourceResponse $response*/
		return $response->toObject();
	}

	/**
	 *
	 *
	 * @param string $slug
	 * @return Product
	 */
	public function findProductBySlug($slug) {

		$response = $this->client->execute(new ProductProjectionFetchBySlugRequest($slug, $this->getContext()));
		return $response->toObject();
	}
}
