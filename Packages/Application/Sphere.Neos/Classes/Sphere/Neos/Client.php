<?php
namespace Sphere\Neos;

/*                                                                        *
 * This script belongs to the Neos package "Sphere.Neos".                 *
 *                                                                        */

use Psr\Log\LoggerInterface;
use Sphere\Core\Client as OriginalClient;
use Sphere\Core\Client\OAuth\Manager;
use Sphere\Core\Config;
use Sphere\Core\Model\Common\Context;
use Sphere\Core\Request\ClientRequestInterface;
use TYPO3\Flow\Annotations as Flow;

/**
 * Wrapper for the Sphere Client which uses Flow settings for its context.
 *
 * @Flow\Scope("singleton")
 */
class Client extends OriginalClient {

	/**
	 * @Flow\InjectConfiguration
	 * @var array
	 */
	protected $settings;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\I18n\Service
	 */
	protected $i18nService;

	/**
	 * @var OriginalClient
	 */
	protected $originalClient;

	/**
	 * @var Context
	 */
	protected $context;

	/**
	 * @var array|Config
	 */
	protected $config;

	/**
	 * @var null
	 */
	protected $cache;

	/**
	 * @var LoggerInterface
	 */
	protected $logger;

	/**
	 * @var null
	 */
	protected $logFormat;

	/**
	 * Constructor
	 *
	 * @param array|\Sphere\Core\Config $config
	 * @param null $cache
	 * @param LoggerInterface $logger
	 * @param null $logFormat
	 */
	public function __construct($config, $cache = NULL, LoggerInterface $logger = NULL, $logFormat = NULL) {
		$this->config = $config;
		$this->cache = $cache;
		$this->logger = $logger;
		$this->logFormat = $logFormat;
	}

	/**
	 * Initializes the original client
	 *
	 * @return void
	 */
	public function initializeObject() {
		$languages = array('en', 'de');

		$this->context = new Context();
		$this->context->setGraceful(TRUE)
			->setLanguages($languages)
			->setLocale((string)$this->i18nService->getConfiguration()->getCurrentLocale());

		$config = new Config();
		$config->fromArray($this->settings['client'])->setContext($this->context);
		$this->originalClient = new OriginalClient($config);
	}


	/**
	 * Returns the current context
	 *
	 * @return Context
	 */
	public function getContext() {
		return $this->context;
	}

	/**
	 * @return Manager
	 */
	public function getOauthManager() {
		return $this->originalClient->getOauthManager();
	}

	/**
	 * @param Manager $oauthManager
	 * @return void
	 */
	protected function setOauthManager(Manager $oauthManager) {
		$this->setOauthManager($oauthManager);
	}

	/**
	 * @param LoggerInterface $logger
	 * @param null $format
	 * @return void
	 */
	protected function setLogger(LoggerInterface $logger = NULL, $format = NULL) {
		$this->originalClient->setLogger($logger, $format);
	}

	/**
	 * @return string
	 */
	protected function getBaseUrl() {
		return $this->originalClient->getBaseUrl();
	}

	/**
	 * @param ClientRequestInterface $request
	 * @return \Sphere\Core\Response\ApiResponseInterface
	 */
	public function execute(ClientRequestInterface $request) {
		return $this->originalClient->execute($request);
	}

	/**
	 * @param ClientRequestInterface $request
	 * @return \GuzzleHttp\Message\RequestInterface
	 */
	protected function createHttpRequest(ClientRequestInterface $request) {
		return $this->createHttpRequest($request);
	}

	/**
	 * @return \Sphere\Core\Response\ApiResponseInterface[]
	 */
	public function executeBatch() {
		return $this->executeBatch();
	}

	/**
	 * @return array
	 */
	protected function getBatchHttpRequests() {
		return $this->getBatchHttpRequests();
	}

	/**
	 * @param ClientRequestInterface $request
	 * @return void
	 */
	public function addBatchRequest(ClientRequestInterface $request) {
		$this->originalClient->addBatchRequest($request);
	}

}
