<?php
namespace Sphere\Neos\Routing;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Sphere.Neos".           *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        */

use Sphere\Core\Model\Product\ProductProjection;
use Sphere\Neos\Domain\Service\ProductService;
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
	 * @var ProductService
	 */
	protected $productService;

	/**
	 *
	 *
	 * @param string $requestPath Part of the full request path, ie. the slug of the product
	 * @return bool TRUE if the $requestPath could be matched, otherwise FALSE
	 */
	protected function matchValue($requestPath) {
		$this->systemLogger->log(sprintf('Trying to find product "%s".', $requestPath), LOG_DEBUG);
		$product = $this->productService->findProductBySlug($requestPath);
		if ($product instanceof ProductProjection) {
			$this->systemLogger->log(sprintf('SPHERE.IO product "%s" matched route part in "%s".', $requestPath, $this->name), LOG_DEBUG);
			$this->value = $requestPath;
		}
		return TRUE;
	}

	/**
	 *
	 *
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
