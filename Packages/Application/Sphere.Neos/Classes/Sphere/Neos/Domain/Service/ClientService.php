<?php
/**
 * @author @ct-jensschulze <jens.schulze@commercetools.de>
 */

namespace Sphere\Neos\Domain\Service;


use TYPO3\Flow\Annotations as Flow;
use Sphere\Core\Client;
use Sphere\Core\Config;
use Sphere\Core\Model\Common\Context;

/**
 * Class ClientService
 */
class ClientService
{
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
     * @var Context
     */
    protected $context;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @return void
     */
    public function initializeObject() {
        $config = new Config();
        $config->fromArray($this->settings['client'])->setContext($this->getContext());
        $this->client = new Client($config);
    }

    public function getClient()
    {
        return $this->client;
    }

    /**
     * @return Context
     */
    public function getContext()
    {
        if (is_null($this->context)) {
            $fallBack = $this->i18nService->getConfiguration()->getFallbackRule()['order'];
            $languages = array_reverse($fallBack);
            $this->context = new Context();
            $this->context->setGraceful(true)
                ->setLanguages($languages)
                ->setLocale((string)$this->i18nService->getConfiguration()->getCurrentLocale());
        }

        return $this->context;
    }
}
