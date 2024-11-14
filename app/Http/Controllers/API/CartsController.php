<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Carts\StoreCartRequest;
use App\Services\CartsService;
use Illuminate\Http\Request;

class CartsController extends Controller
{
    protected $cartsService;

    public function __construct(CartsService $cartsService)
    {
        $this->cartsService = $cartsService;
    }

    /**
     * Add an item to the cart.
     *
     * @param \App\Http\Requests\Carts\StoreCartRequest $request
     * @return \Illuminate\Http\Response
     */
    public function addToCart(StoreCartRequest $request)
    {
        // Use the authenticated user's ID and validated request data
        $cart = $this->cartsService->addToCart($request->validated());

        return response()->json(['cart' => $cart], 200);
    }

    /**
     * Get all cart items for the authenticated user.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCartItems()
    {
        $cartItems = $this->cartsService->getCartItems();

        return response()->json([
            'status' => 200,
            'message' => 'Cart Items Fetched Successfully',
            'cart_items' => $cartItems
        ], 200);
    }

    /**
     * Remove an item from the cart.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function removeFromCart($id)
    {
        $success = $this->cartsService->removeFromCart($id);

        if ($success) {
            return response()->json(['message' => 'Item removed from cart'], 200);
        }

        return response()->json(['message' => 'Item not found in cart'], 404);
    }
}
