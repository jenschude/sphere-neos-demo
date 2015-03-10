<?php
/**
 * @author @ct-jensschulze <jens.schulze@commercetools.de>
 */

namespace Sphere\Neos\TypoScript;

use TYPO3\Neos\TypoScript\DimensionMenuImplementation;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;

class SphereDimensionMenuImplementation extends DimensionMenuImplementation
{
    protected function isNodeHidden(NodeInterface $node)
    {
        return false;
    }
}
