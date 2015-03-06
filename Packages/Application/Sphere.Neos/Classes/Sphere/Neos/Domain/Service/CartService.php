<?php
/**
 * @author @ct-jensschulze <jens.schulze@commercetools.de>
 */

namespace Sphere\Neos\Domain\Service;

use Sphere\Core\Model\Cart\LineItem;
use Sphere\Core\Model\Common\LocalizedString;
use Sphere\Core\Request\Carts\Command\CartChangeLineItemQuantityAction;
use Sphere\Core\Request\Carts\Command\CartRemoveLineItemAction;
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
    protected $currency = 'EUR';

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
        $cartDraft = new CartDraft($this->currency);

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
        $this->cart->setTotalPrice(new Money($this->currency, 0));

        return $this->cart;
    }

    public function addLineItem($productId, $variantId, $quantity)
    {
        $cart = $this->getOrCreateCart();

        $updateItemRequest = new CartUpdateRequest($cart->getId(), $cart->getVersion());
        $updateItemRequest->addAction(new CartAddLineItemAction($productId, (int)$variantId, (int)$quantity));

        $this->cart = $this->clientService->getClient()->execute($updateItemRequest)->toObject();
    }

    public function removeLineItem($itemId)
    {
        $cart = $this->getOrCreateCart();

        $updateItemRequest = new CartUpdateRequest($cart->getId(), $cart->getVersion());
        $updateItemRequest->addAction(new CartRemoveLineItemAction($itemId));

        $this->cart = $this->clientService->getClient()->execute($updateItemRequest)->toObject();
    }

    public function updateQuantity($items)
    {
        $cart = $this->getOrCreateCart();

        $updateItemRequest = new CartUpdateRequest($cart->getId(), $cart->getVersion());
        foreach ($items as $itemId => $quantity) {
            $updateItemRequest->addAction(new CartChangeLineItemQuantityAction($itemId, (int)$quantity));
        }
        $this->cart = $this->clientService->getClient()->execute($updateItemRequest)->toObject();
    }
}
