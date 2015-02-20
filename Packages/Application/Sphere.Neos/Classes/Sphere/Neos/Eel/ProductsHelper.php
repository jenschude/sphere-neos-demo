<?php
namespace Sphere\Neos\Eel;

/*                                                                                                  *
 *                                                                                                  */

use Sphere\Core\Client;
use Sphere\Core\Model\Common\Context;
use Sphere\Core\Model\Common\LocalizedString;
use Sphere\Core\Model\Product\Product;
use Sphere\Core\Request\Products\ProductProjectionFetchBySkuRequest;
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
		LocalizedString::setDefaultLanguage('en');

		$this->client = new Client($this->settings['client']);
	}

	/**
	 *
	 *
	 * @param string $sku
	 * @return object
	 * @throws \Exception
	 */
	public function findProduct($sku) {
#		$context = new Context();
#		$context->setLanguages(array('en', 'de'));

		$response = $this->client->execute(new ProductProjectionFetchBySkuRequest($sku));

#		$product = $response->toArray();
#		\TYPO3\Flow\var_dump($product);
		return;
		return Product::fromArray($product['masterData']['current']);
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