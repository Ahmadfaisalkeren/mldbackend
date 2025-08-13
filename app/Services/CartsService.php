<?php

namespace App\Services;

use App\Models\Cart;
use Illuminate\Support\Facades\Auth;

class CartsService
{
    public function addToCart(array $cartData)
    {
        $userId = Auth::id();
        $itemId = $cartData['item_id'];
        $quantity = $cartData['quantity'];

        $cartItem = Cart::where('user_id', $userId)
            ->where('item_id', $itemId)
            ->first();

        if ($cartItem) {
            $cartItem->quantity += $quantity;
            $cartItem->save();
            return $cartItem;
        }

        return Cart::create([
            'user_id' => $userId,
            'item_id' => $itemId,
            'quantity' => $quantity,
        ]);
    }

    /**
     * Get the user's cart items.
     *
     * @param  int  $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCartItems()
    {
        $id = Auth::id();

        return Cart::with('item')->where('user_id', $id)
            ->with('item')
            ->get();
    }

    public function removeFromCart($id)
    {
        $userId = Auth::id();

        // Look for the cart entry by its primary id and the user_id
        $cartItem = Cart::where('user_id', $userId)
            ->where('id', $id)
            ->first();

        if ($cartItem) {
            $cartItem->delete();
            return response()->json(['message' => 'Item removed from cart'], 200);
        }

        return response()->json(['message' => 'Item not found in cart'], 404);
    }
}
