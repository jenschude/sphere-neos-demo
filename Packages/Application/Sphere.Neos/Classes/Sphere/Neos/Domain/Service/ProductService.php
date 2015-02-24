<?php
namespace Sphere\Neos\Domain\Service;

/*                                                                                                  *
 *                                                                                                  */

use Sphere\Core\Client;
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
	 * @return void
	 */
	public function initializeObject() {
		$this->client = new Client($this->settings['client']);
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

		$context = new Context();
		$context->setLanguages(array('en', 'de'));
		$response = $this->client->execute(new ProductProjectionFetchBySkuRequest($sku, $context));
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
		$context = new Context();
		$context->setLanguages(array('en', 'de'));

		$response = $this->client->execute(new ProductProjectionFetchBySlugRequest($slug, $context));
		return $response->toObject();
	}
}