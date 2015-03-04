<?php
/**
 * @author @ct-jensschulze <jens.schulze@commercetools.de>
 */

namespace Sphere\Neos\Domain\Service;

use Sphere\Core\Model\Cart\LineItem;
use Sphere\Core\Model\Common\LocalizedString;
use TYPO3\Flow\Annotations as Flow;
use Sphere\Core\Model\Cart\Cart as SphereCart;
use Sphere\Core\Model\Common\Money;
use Sphere\Core\Request\Carts\CartUpdateRequest;
use Sphere\Core\Request\Carts\Command\CartAddLineItemAction;
use Sphere\Neos\Domain\Model\Cart;
use Sphere\Core\Model\Cart\CartDraft;
use Sphere\Core\Request\Carts\CartCreateRequest;
use Sphere\Core\Request\Carts\CartFetchByIdRequest;

/**
 * Class CartService
 */
class CartService
{
    /**
     * @Flow\Inject
     * @var \Sphere\Neos\Domain\Service\ClientService
     */
    protected $clientService;

    /**
     * @Flow\Inject
     * @var Cart
     */
    protected $cartModel;

    /**
     * @var SphereCart
     */
    protected $cart;

    /**
     * @return CartCreateRequest
     */
    public function getCreateCartRequest()
    {
        $cartDraft = new CartDraft('EUR');

        $request = new CartCreateRequest($cartDraft);

        return $request;
    }

    /**
     * @return CartFetchByIdRequest
     */
    public function getCartRequest()
    {
        $request = new CartFetchByIdRequest($this->cartModel->getCartId());

        return $request;
    }

    /**
     * @return SphereCart
     */
    public function getOrCreateCart()
    {
        if (is_null($this->cart)) {
            if (is_null($this->cartModel->getCartId())) {
                $request = $this->getCreateCartRequest();
            } else {
                $request = $this->getCartRequest();
            }
            $this->cart = $this->clientService->getClient()->execute($request)->toObject();
            $this->cartModel->setCartId($this->cart->getId());
        }

        return $this->cart;
    }

    /**
     * @return SphereCart
     */
    public function getCart()
    {
        if (!is_null($this->cartModel->getCartId())) {
            return $this->getOrCreateCart();
        }

        $this->cart = new SphereCart([], $this->clientService->getContext());

        // @todo remove hardcoded line item stuff
        $total = new Money('EUR', 0);
        $this->cart->setTotalPrice($total);
        $lineItem = new LineItem([], $this->clientService->getContext());
        $lineItem->setProductId('1234');
        $lineItem->setName(LocalizedString::of(['en' => 'Test']));
        $this->cart->getLineItems()->setAt(null, $lineItem);

        return $this->cart;
    }

    public function addLineItem($productId, $variantId, $quantity)
    {
        $cart = $this->getOrCreateCart();

        $addItemRequest = new CartUpdateRequest($cart->getId(), $cart->getVersion());
        $addItemRequest->addAction(new CartAddLineItemAction($productId, $variantId, $quantity));

        $this->cart = $this->clientService->getClient()->execute($addItemRequest)->toObject();
    }
}
