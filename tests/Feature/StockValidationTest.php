<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StockValidationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function cannot_add_to_cart_when_stock_is_zero()
    {
        $product = Product::create([
            'name' => 'Out of Stock',
            'price' => 99.99,
            'stock' => 0,
        ]);

        $response = $this->post(route('cart.add', $product->id));

        $response->assertSessionHas('error', 'Produit en rupture de stock!');
        $cart = session('cart', []);
        $this->assertEmpty($cart);
    }

    /** @test */
    public function cannot_increment_when_cart_quantity_equals_stock()
    {
        $product = Product::create([
            'name' => 'Limited Product',
            'price' => 50.00,
            'stock' => 3,
        ]);

        session(['cart' => [$product->id => 3]]);

        $response = $this->post(route('cart.increment', $product->id));

        $response->assertSessionHas('error', 'Stock insuffisant!');
        $cart = session('cart');
        $this->assertEquals(3, $cart[$product->id]);
    }

    /** @test */
    public function can_increment_when_cart_quantity_is_less_than_stock()
    {
        $product = Product::create([
            'name' => 'Product',
            'price' => 50.00,
            'stock' => 10,
        ]);

        session(['cart' => [$product->id => 5]]);

        $response = $this->post(route('cart.increment', $product->id));

        $response->assertSessionHas('success');
        $cart = session('cart');
        $this->assertEquals(6, $cart[$product->id]);
    }

    /** @test */
    public function stock_limit_is_respected_when_adding_multiple_times()
    {
        $product = Product::create([
            'name' => 'Limited Stock',
            'price' => 25.00,
            'stock' => 5,
        ]);

        // Ajouter 10 fois (mais seulement 5 devraient être ajoutés)
        for ($i = 0; $i < 10; $i++) {
            $this->post(route('cart.add', $product->id));
        }

        $cart = session('cart');
        $this->assertEquals(5, $cart[$product->id]);
    }

    /** @test */
    public function adding_product_with_existing_cart_items_respects_stock()
    {
        $product = Product::create([
            'name' => 'Product',
            'price' => 30.00,
            'stock' => 5,
        ]);

        // Déjà 4 dans le panier
        session(['cart' => [$product->id => 4]]);

        // Essayer d'en ajouter 2 de plus
        $this->post(route('cart.add', $product->id));
        $this->post(route('cart.add', $product->id));

        $cart = session('cart');
        $this->assertEquals(5, $cart[$product->id]); // Should only be 5
    }

    /** @test */
    public function product_with_stock_one_can_be_added_once()
    {
        $product = Product::create([
            'name' => 'Last One',
            'price' => 100.00,
            'stock' => 1,
        ]);

        $this->post(route('cart.add', $product->id));
        $this->post(route('cart.add', $product->id)); // Should not add again

        $cart = session('cart');
        $this->assertEquals(1, $cart[$product->id]);
    }

    /** @test */
    public function decrementing_removes_product_when_quantity_is_one()
    {
        $product = Product::create([
            'name' => 'Product',
            'price' => 50.00,
            'stock' => 10,
        ]);

        session(['cart' => [$product->id => 1]]);

        $this->post(route('cart.decrement', $product->id));

        $cart = session('cart');
        $this->assertArrayNotHasKey($product->id, $cart);
    }

    /** @test */
    public function decrementing_reduces_quantity_when_greater_than_one()
    {
        $product = Product::create([
            'name' => 'Product',
            'price' => 50.00,
            'stock' => 10,
        ]);

        session(['cart' => [$product->id => 5]]);

        $this->post(route('cart.decrement', $product->id));

        $cart = session('cart');
        $this->assertEquals(4, $cart[$product->id]);
    }

    /** @test */
    public function stock_warning_displays_when_stock_is_low()
    {
        $product = Product::create([
            'name' => 'Low Stock Product',
            'price' => 40.00,
            'stock' => 3,
        ]);

        session(['cart' => [$product->id => 1]]);

        $response = $this->get(route('cart.index'));

        $response->assertSee('Stock faible');
    }

    /** @test */
    public function stock_depleted_message_displays_when_all_in_cart()
    {
        $product = Product::create([
            'name' => 'Product',
            'price' => 40.00,
            'stock' => 5,
        ]);

        session(['cart' => [$product->id => 5]]);

        $response = $this->get(route('cart.index'));

        $response->assertSee('Stock épuisé');
    }

    /** @test */
    public function cannot_add_nonexistent_product_to_cart()
    {
        $response = $this->post(route('cart.add', 999999));

        $response->assertStatus(404);
    }

    /** @test */
    public function cannot_increment_nonexistent_product()
    {
        $response = $this->post(route('cart.increment', 999999));

        $response->assertStatus(404);
    }

    /** @test */
    public function high_stock_products_show_in_stock_message()
    {
        $product = Product::create([
            'name' => 'High Stock Product',
            'price' => 30.00,
            'stock' => 100,
        ]);

        session(['cart' => [$product->id => 10]]);

        $response = $this->get(route('cart.index'));

        $response->assertSee('En stock');
    }

    /** @test */
    public function products_page_shows_stock_maximum_reached_when_all_in_cart()
    {
        $product = Product::create([
            'name' => 'Product',
            'price' => 50.00,
            'stock' => 5,
        ]);

        session(['cart' => [$product->id => 5]]);

        $response = $this->get(route('products.index'));

        $response->assertSee('Stock maximum atteint');
    }

    /** @test */
    public function products_page_shows_add_button_when_stock_available()
    {
        $product = Product::create([
            'name' => 'Available Product',
            'price' => 60.00,
            'stock' => 10,
        ]);

        $response = $this->get(route('products.index'));

        $response->assertSee('Ajouter au panier');
    }
}
