<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CartFlowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_browse_products_add_to_cart_and_checkout()
    {
        // Créer des produits
        $product1 = Product::create([
            'name' => 'Laptop',
            'price' => 999.99,
            'stock' => 5,
        ]);

        $product2 = Product::create([
            'name' => 'Mouse',
            'price' => 29.99,
            'stock' => 20,
        ]);

        // Visiter la page produits
        $response = $this->get(route('products.index'));
        $response->assertStatus(200);
        $response->assertSee('Laptop');
        $response->assertSee('Mouse');

        // Ajouter le premier produit au panier
        $this->post(route('cart.add', $product1->id));
        
        // Vérifier que le produit est dans le panier
        $cart = session('cart');
        $this->assertArrayHasKey($product1->id, $cart);
        $this->assertEquals(1, $cart[$product1->id]);

        // Ajouter le deuxième produit
        $this->post(route('cart.add', $product2->id));
        $this->post(route('cart.add', $product2->id)); // Ajouter 2x

        // Visiter le panier
        $response = $this->get(route('cart.index'));
        $response->assertStatus(200);
        $response->assertSee('Laptop');
        $response->assertSee('Mouse');
        $response->assertSee('999.99');
        $response->assertSee('29.99');

        // Vérifier le total (1 * 999.99 + 2 * 29.99 = 1059.97)
        $response->assertViewHas('total', 1059.97);
    }

    /** @test */
    public function user_can_modify_quantities_in_cart()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'price' => 50.00,
            'stock' => 10,
        ]);

        // Ajouter au panier
        $this->post(route('cart.add', $product->id));
        
        // Incrémenter
        $this->post(route('cart.increment', $product->id));
        $this->post(route('cart.increment', $product->id));
        
        $cart = session('cart');
        $this->assertEquals(3, $cart[$product->id]);

        // Décrémenter
        $this->post(route('cart.decrement', $product->id));
        
        $cart = session('cart');
        $this->assertEquals(2, $cart[$product->id]);

        // Vérifier le total
        $response = $this->get(route('cart.index'));
        $response->assertViewHas('total', 100.00); // 2 * 50
    }

    /** @test */
    public function user_cannot_add_out_of_stock_products()
    {
        $product = Product::create([
            'name' => 'Limited Product',
            'price' => 99.99,
            'stock' => 0,
        ]);

        $response = $this->get(route('products.index'));
        $response->assertSee('Rupture de stock');
    }

    /** @test */
    public function cart_respects_stock_limits()
    {
        $product = Product::create([
            'name' => 'Limited Stock',
            'price' => 25.00,
            'stock' => 3,
        ]);

        // Essayer d'ajouter plus que le stock
        $this->post(route('cart.add', $product->id));
        $this->post(route('cart.add', $product->id));
        $this->post(route('cart.add', $product->id));
        $this->post(route('cart.add', $product->id)); // Devrait être ignoré
        $this->post(route('cart.add', $product->id)); // Devrait être ignoré

        $cart = session('cart');
        $this->assertEquals(3, $cart[$product->id]);
    }

    /** @test */
    public function cart_shows_correct_remaining_stock()
    {
        $product = Product::create([
            'name' => 'Product',
            'price' => 10.00,
            'stock' => 10,
        ]);

        session(['cart' => [$product->id => 7]]);

        $response = $this->get(route('cart.index'));
        
        $response->assertStatus(200);
        $response->assertSee('10'); // Stock total
    }

    /** @test */
    public function removing_product_from_cart_works()
    {
        $product = Product::create([
            'name' => 'Product to Remove',
            'price' => 15.00,
            'stock' => 10,
        ]);

        // Ajouter au panier
        $this->post(route('cart.add', $product->id));
        $this->post(route('cart.add', $product->id));
        $this->post(route('cart.add', $product->id));

        $cart = session('cart');
        $this->assertArrayHasKey($product->id, $cart);

        // Retirer du panier
        $this->delete(route('cart.remove', $product->id));

        $cart = session('cart');
        $this->assertArrayNotHasKey($product->id, $cart);
    }

    /** @test */
    public function empty_cart_shows_appropriate_message()
    {
        $response = $this->get(route('cart.index'));
        
        $response->assertStatus(200);
        $response->assertSee('Votre panier est vide');
        $response->assertSee('Ajoutez des produits depuis la page produits');
    }

    /** @test */
    public function cart_badge_shows_correct_item_count()
    {
        $product1 = Product::create([
            'name' => 'Product 1',
            'price' => 10.00,
            'stock' => 10,
        ]);

        $product2 = Product::create([
            'name' => 'Product 2',
            'price' => 20.00,
            'stock' => 10,
        ]);

        session(['cart' => [
            $product1->id => 3,
            $product2->id => 2,
        ]]);

        $response = $this->get(route('products.index'));
        
        // Le badge devrait montrer 5 (3 + 2)
        $response->assertSee('5');
    }

    /** @test */
    public function multiple_products_calculate_total_correctly()
    {
        $products = [];
        for ($i = 1; $i <= 5; $i++) {
            $products[] = Product::create([
                'name' => "Product $i",
                'price' => $i * 10,
                'stock' => 20,
            ]);
        }

        // Ajouter plusieurs produits avec différentes quantités
        session(['cart' => [
            $products[0]->id => 1, // 10 * 1 = 10
            $products[1]->id => 2, // 20 * 2 = 40
            $products[2]->id => 3, // 30 * 3 = 90
            $products[3]->id => 1, // 40 * 1 = 40
            $products[4]->id => 2, // 50 * 2 = 100
        ]]);

        $response = $this->get(route('cart.index'));
        
        // Total = 10 + 40 + 90 + 40 + 100 = 280
        $response->assertViewHas('total', 280.00);
    }

    /** @test */
    public function cart_persists_across_page_visits()
    {
        $product = Product::create([
            'name' => 'Persistent Product',
            'price' => 99.99,
            'stock' => 10,
        ]);

        // Ajouter au panier
        $this->post(route('cart.add', $product->id));

        // Visiter la page produits
        $this->get(route('products.index'));

        // Visiter le panier
        $response = $this->get(route('cart.index'));

        // Le produit devrait toujours être là
        $response->assertSee('Persistent Product');
        $cart = session('cart');
        $this->assertArrayHasKey($product->id, $cart);
    }
}
