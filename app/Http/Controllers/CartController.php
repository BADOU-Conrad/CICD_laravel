<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        $products = [];
        $total = 0;

        foreach ($cart as $productId => $quantity) {
            $product = Product::find($productId);
            if ($product) {
                $products[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $quantity,
                    'stock' => $product->stock,
                    'subtotal' => $product->price * $quantity,
                ];
                $total += $product->price * $quantity;
            }
        }

        return view('cart.index', compact('products', 'total'));
    }

    public function add(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $cart = session()->get('cart', []);

        // Vérifier si le produit est en stock
        if ($product->stock == 0) {
            return redirect()->back()->with('error', 'Produit en rupture de stock!');
        }

        if (isset($cart[$id])) {
            if ($cart[$id] < $product->stock) {
                $cart[$id]++;
            }
        } else {
            $cart[$id] = 1;
        }

        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Produit ajouté au panier!');
    }

    public function increment($id)
    {
        $product = Product::findOrFail($id);
        $cart = session()->get('cart', []);

        if (isset($cart[$id]) && $cart[$id] < $product->stock) {
            $cart[$id]++;
            session()->put('cart', $cart);
            return redirect()->back()->with('success', 'Quantité mise à jour!');
        }

        return redirect()->back()->with('error', 'Stock insuffisant!');
    }

    public function decrement($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            if ($cart[$id] > 1) {
                $cart[$id]--;
            } else {
                unset($cart[$id]);
            }
            session()->put('cart', $cart);
        }

        return redirect()->back()->with('success', 'Quantité mise à jour!');
    }

    public function remove($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return redirect()->back()->with('success', 'Produit retiré du panier!');
    }
}
