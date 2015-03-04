<?php
/**
 * @author @ct-jensschulze <jens.schulze@commercetools.de>
 */
namespace Sphere\Neos\Domain\Model;

use TYPO3\Flow\Annotations as Flow;

/**
 * @Flow\Scope("session")
 */
class Cart
{
    /**
     * @var string
     */
    protected $cartId;

    /**
     * @return string
     */
    public function getCartId()
    {
        return $this->cartId;
    }

    /**
     * @Flow\Session(autoStart = TRUE)
     */
    public function setCartId($cartId)
    {
        $this->cartId = $cartId;
    }
}
