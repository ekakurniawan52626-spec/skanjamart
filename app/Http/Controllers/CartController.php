<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    private function currentCart(): Cart
    {
        return Cart::firstOrCreate(['user_id' => auth()->id()]);
    }

    public function index()
    {
        $cart = $this->currentCart()->load('items.product');

        return view('cart.index', compact('cart'));
    }

    public function store(Request $request, Product $product)
    {
        $request->validate(['quantity' => 'nullable|integer|min:1']);
        $quantity = $request->integer('quantity', 1);

        $cart = $this->currentCart();
        $item = $cart->items()->firstOrNew(['product_id' => $product->id]);
        $item->quantity = ($item->exists ? $item->quantity : 0) + $quantity;
        $item->save();

        return back()->with('success', 'Produk ditambahkan ke keranjang.');
    }

    public function update(Request $request, $itemId)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);

        $item = $this->currentCart()->items()->findOrFail($itemId);
        $item->update(['quantity' => $request->integer('quantity')]);

        return back()->with('success', 'Keranjang diperbarui.');
    }

    public function destroy($itemId)
    {
        $this->currentCart()->items()->where('id', $itemId)->delete();

        return back()->with('success', 'Produk dihapus dari keranjang.');
    }
}
