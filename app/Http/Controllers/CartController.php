<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        $request->validate([
            'id_product' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1'
        ]);

        $product = Product::findOrFail($request->id_product);

        $quantity = 1;

        // cek apakah product sudah ada di cart user ini
        $cartItem = Cart::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            // sudah ada → update qty & subtotal
            $cartItem->quantity += $quantity;
            $cartItem->subtotal = $cartItem->quantity * $product->price;
            $cartItem->save();
        } else {
            // belum ada → buat baru
            $cartItem = Cart::create([
                'user_id' => auth()->id(),
                'product_id' => $product->id,
                'quantity' => $quantity,
                'subtotal' => $quantity * $product->price,
            ]);
        }

        return Session::flash('message', 'Produk berhasil ditambahkan.');
    }


    public function reduceQuantity(Request $request)
    {
        $request->validate([
            'id_cart' => ['required', 'exists:carts,id']
        ]);

        $cart = Cart::find($request->id_cart);


        if ($cart->quantity > 1) {
            $cart->quantity -= 1;
            $cart->subtotal = $cart->quantity * $cart->product->price;
            $cart->save();
        } else {
            return Session::flash('message', 'Jumlah sudah tidak dapat dikurangi.');
        }
    }

    public function addQuantity(Request $request)
    {
        $request->validate([
            'id_cart' => ['required', 'exists:carts,id']
        ]);

        $cart = Cart::find($request->id_cart);

        if ($cart->quantity < $cart->product->stock) {
            $cart->quantity += 1;
            $cart->subtotal = $cart->quantity * $cart->product->price;
            $cart->save();
        } else {
            return Session::flash('message', 'Jumlah sudah tidak dapat ditambah.');
        }
    }

    public function deleteCartItem(Request $request)
    {
        $request->validate([
            'id_cart' => ['required', 'exists:carts,id']
        ]);


        $cart = Cart::find($request->id_cart);

        $cart->delete();
    }
}
