<?php
/**
 * @author @ct-jensschulze <jens.schulze@commercetools.de>
 */

namespace Sphere\Neos\Domain\Model;

use Sphere\Core\Model\Cart\LineItem as OriginalLineItem;
use Sphere\Core\Model\Common\LocalizedString;

/**
 * Class LineItem
 * @package Sphere\Neos\Domain\Model
 * @method LocalizedString getSlug()
 * @method LineItem setSlug(LocalizedString $slug)
 */
class LineItem extends OriginalLineItem
{
    public function getFields()
    {
        $fields = parent::getFields();
        $fields['slug'] = [static::TYPE => '\Sphere\Core\Model\Common\LocalizedString'];

        return $fields;
    }
}
