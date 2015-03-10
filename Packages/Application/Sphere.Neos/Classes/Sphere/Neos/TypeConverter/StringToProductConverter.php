<?php
namespace Sphere\Neos\TypeConverter;

/*                                                                        *
 * This script belongs to the Neos package "Sphere.Neos".                 *
 *                                                                        */

use Sphere\Core\Model\Product\ProductProjection;
use Sphere\Neos\Domain\Repository\ProductRepository;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Property\Exception\TargetNotFoundException;
use TYPO3\Flow\Property\PropertyMappingConfigurationInterface;
use TYPO3\Flow\Property\TypeConverter\AbstractTypeConverter;
use TYPO3\Flow\Property\TypeConverter\Error\TargetNotFoundError;

/**
 *
 * @Flow\Scope("singleton")
 */
class StringToProductConverter extends AbstractTypeConverter {

	/**
	 * @var array<string>
	 */
	protected $sourceTypes = array('string');

	/**
	 * @var string
	 */
	protected $targetType = 'Sphere\Core\Model\Product\ProductProjection';

	/**
	 * @var integer
	 */
	protected $priority = 100;

	/**
	 * @Flow\Inject
	 * @var ProductRepository
	 */
	protected $productRepository;

	/**
	 * Converts a string (product id) to a ProductProjection object
	 *
	 * @param mixed $source
	 * @param string $targetType
	 * @param array $convertedChildProperties
	 * @param PropertyMappingConfigurationInterface $configuration
	 * @return mixed|\TYPO3\Flow\Error\Error the target type, or an error object if a user-error occurred
	 * @throws TargetNotFoundException
	 */
	public function convertFrom($source, $targetType, array $convertedChildProperties = array(), PropertyMappingConfigurationInterface $configuration = NULL) {
		$productProjection = $this->productRepository->findOneById($source);
		if (!$productProjection instanceof ProductProjection) {
			return new TargetNotFoundError(sprintf('Could not find a product with identifier %s.', $source), 1425908555);
		}
		return $productProjection;
	}

}
