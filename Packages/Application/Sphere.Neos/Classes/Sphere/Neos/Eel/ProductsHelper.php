<?php
namespace Sphere\Neos\Eel;

/*                                                                                                  *
 *                                                                                                  */

use Sphere\Core\Client;
use Sphere\Core\Model\Common\Context;
use Sphere\Core\Model\Product\Product;
use Sphere\Core\Model\Product\ProductProjection;
use Sphere\Core\Request\Products\ProductProjectionFetchBySkuRequest;
use Sphere\Core\Response\PagedQueryResponse;
use TYPO3\Eel\ProtectedContextAwareInterface;
use TYPO3\Flow\Annotations as Flow;

/**
 * ProductHelper
 */
class ProductsHelper implements ProtectedContextAwareInterface {

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
	public function findProduct($sku) {
		if ($sku == '') {
			return NULL;
		}
#		$context = new Context();
#		$context->setLanguages(array('en', 'de'));

		$response = $this->client->execute(new ProductProjectionFetchBySkuRequest($sku));
		/** @var PagedQueryResponse $response*/

		return ProductProjection::fromArray($response[0]);
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