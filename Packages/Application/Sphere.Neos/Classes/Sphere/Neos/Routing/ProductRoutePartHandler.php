<?php
namespace Sphere\Neos\Routing;

/*                                                                        *
 * This script belongs to the Neos package "Sphere.Neos".                 *
 *                                                                        */

use Sphere\Core\Model\Product\ProductProjection;
use Sphere\Neos\Domain\Repository\ProductRepository;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Log\SystemLoggerInterface;
use TYPO3\Flow\Mvc\Routing\DynamicRoutePart;

/**
 * A route part handler for finding products by slug.
 */
class ProductRoutePartHandler extends DynamicRoutePart {

	/**
	 * @Flow\Inject
	 * @var SystemLoggerInterface
	 */
	protected $systemLogger;

	/**
	 * @Flow\Inject
	 * @var ProductRepository
	 */
	protected $productService;

	/**
	 * Matches the given request path (ie. a segment of the URL used in the HTTP request) and checks if a product with
	 * the corresponding slug exists.
	 *
	 * @param string $requestPath Part of the full request path, ie. the slug of the product
	 * @return bool TRUE if the $requestPath could be matched, otherwise FALSE
	 */
	protected function matchValue($requestPath) {
		$this->systemLogger->log(sprintf('Trying to find product "%s".', $requestPath), LOG_DEBUG);
		$product = $this->productService->findOneBySlug($requestPath);
		if ($product instanceof ProductProjection) {
			$this->systemLogger->log(sprintf('SPHERE.IO product "%s" (%s) matched route part in "%s".', $product->getName(), $product->getId(), $this->name), LOG_DEBUG);
			$this->value = $requestPath;
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * Takes the given product slug and assumes that it is a valid one (without further checking if it actually exists)
	 *
	 * @param string $productSlug
	 * @return boolean TRUE if value could be resolved successfully, otherwise FALSE.
	 */
	protected function resolveValue($productSlug) {
		if (is_string($productSlug) && $productSlug !== '') {
			$this->systemLogger->log(sprintf('Resolved route "%s" for SPHERE.IO product "%s".', $this->name, $productSlug), LOG_DEBUG);
			$this->value = $productSlug;
			return TRUE;
		} else {
			return FALSE;
		}
	}

}
