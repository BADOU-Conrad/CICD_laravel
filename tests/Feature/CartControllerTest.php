<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CartControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_displays_cart_page()
    {
        $response = $this->get(route('cart.index'));

        $response->assertStatus(200);
        $response->assertViewIs('cart.index');
    }

    /** @test */
    public function it_shows_empty_cart_message_when_cart_is_empty()
    {
        $response = $this->get(route('cart.index'));

        $response->assertSee('Votre panier est vide');
    }

    /** @test */
    public function it_can_add_product_to_cart()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'price' => 99.99,
            'stock' => 10,
        ]);

        $response = $this->post(route('cart.add', $product->id));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Produit ajouté au panier!');
        
        $cart = session('cart');
        $this->assertArrayHasKey($product->id, $cart);
        $this->assertEquals(1, $cart[$product->id]);
    }

    /** @test */
    public function it_increments_quantity_when_adding_same_product_twice()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'price' => 99.99,
            'stock' => 10,
        ]);

        $this->post(route('cart.add', $product->id));
        $this->post(route('cart.add', $product->id));

        $cart = session('cart');
        $this->assertEquals(2, $cart[$product->id]);
    }

    /** @test */
    public function it_cannot_add_more_than_stock_quantity()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'price' => 99.99,
            'stock' => 2,
        ]);

        $this->post(route('cart.add', $product->id));
        $this->post(route('cart.add', $product->id));
        $this->post(route('cart.add', $product->id)); // Should not add

        $cart = session('cart');
        $this->assertEquals(2, $cart[$product->id]);
    }

    /** @test */
    public function it_can_increment_product_quantity_in_cart()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'price' => 99.99,
            'stock' => 10,
        ]);

        session(['cart' => [$product->id => 1]]);

        $response = $this->post(route('cart.increment', $product->id));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Quantité mise à jour!');
        
        $cart = session('cart');
        $this->assertEquals(2, $cart[$product->id]);
    }

    /** @test */
    public function it_cannot_increment_beyond_stock()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'price' => 99.99,
            'stock' => 2,
        ]);

        session(['cart' => [$product->id => 2]]);

        $response = $this->post(route('cart.increment', $product->id));

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Stock insuffisant!');
        
        $cart = session('cart');
        $this->assertEquals(2, $cart[$product->id]);
    }

    /** @test */
    public function it_can_decrement_product_quantity_in_cart()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'price' => 99.99,
            'stock' => 10,
        ]);

        session(['cart' => [$product->id => 3]]);

        $response = $this->post(route('cart.decrement', $product->id));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Quantité mise à jour!');
        
        $cart = session('cart');
        $this->assertEquals(2, $cart[$product->id]);
    }

    /** @test */
    public function it_removes_product_when_decrementing_to_zero()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'price' => 99.99,
            'stock' => 10,
        ]);

        session(['cart' => [$product->id => 1]]);

        $response = $this->post(route('cart.decrement', $product->id));

        $cart = session('cart');
        $this->assertArrayNotHasKey($product->id, $cart);
    }

    /** @test */
    public function it_can_remove_product_from_cart()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'price' => 99.99,
            'stock' => 10,
        ]);

        session(['cart' => [$product->id => 5]]);

        $response = $this->delete(route('cart.remove', $product->id));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Produit retiré du panier!');
        
        $cart = session('cart');
        $this->assertArrayNotHasKey($product->id, $cart);
    }

    /** @test */
    public function it_calculates_cart_total_correctly()
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
            $product1->id => 2, // 2 * 10 = 20
            $product2->id => 3, // 3 * 20 = 60
        ]]);

        $response = $this->get(route('cart.index'));

        $response->assertViewHas('total', 80.00);
    }

    /** @test */
    public function it_displays_cart_products_with_details()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'price' => 99.99,
            'stock' => 10,
        ]);

        session(['cart' => [$product->id => 2]]);

        $response = $this->get(route('cart.index'));

        $response->assertSee('Test Product');
        $response->assertSee('99.99');
        $response->assertSee('2'); // quantity
        $response->assertSee('10'); // stock
    }

    /** @test */
    public function it_handles_deleted_products_in_cart_gracefully()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'price' => 99.99,
            'stock' => 10,
        ]);

        session(['cart' => [$product->id => 2]]);
        
        $product->delete();

        $response = $this->get(route('cart.index'));

        $response->assertStatus(200);
        $response->assertViewHas('total', 0);
    }
}
