<?php
/**
 * @author @ct-jensschulze <jens.schulze@commercetools.de>
 */
namespace Sphere\Neos\Controller;

use Sphere\Neos\Domain\Service\CartService;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Mvc\Controller\ActionController;

class CartController extends ActionController
{
    /**
     * @Flow\Inject
     * @var CartService
     */
    protected $cartService;

    public function addItemAction()
    {
        if ($this->request->hasArgument('productId')) {
            $productId = $this->request->getArgument('productId');
            $this->cartService->addLineItem($productId, 1, 1);
        }
        $this->redirectToUri('/en/cart.html');
    }

    public function removeItemAction()
    {
        if ($this->request->hasArgument('itemId')) {
            $itemId = $this->request->getArgument('itemId');
            $this->cartService->removeLineItem($itemId);
        }
        $this->redirectToUri('/en/cart.html');
    }

    public function updateCartAction()
    {
        if ($this->request->hasArgument('items')) {
            $items = $this->request->getArgument('items');
            $this->cartService->updateQuantity($items);
        }
        $this->redirectToUri('/en/cart.html');
    }
}
