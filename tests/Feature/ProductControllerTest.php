<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_displays_products_page()
    {
        $response = $this->get(route('products.index'));

        $response->assertStatus(200);
        $response->assertViewIs('products.index');
    }

    /** @test */
    public function it_shows_all_products_on_index_page()
    {
        $product1 = Product::create([
            'name' => 'Product 1',
            'price' => 99.99,
            'stock' => 10,
        ]);

        $product2 = Product::create([
            'name' => 'Product 2',
            'price' => 149.99,
            'stock' => 5,
        ]);

        $response = $this->get(route('products.index'));

        $response->assertSee('Product 1');
        $response->assertSee('Product 2');
        $response->assertSee('99.99');
        $response->assertSee('149.99');
    }

    /** @test */
    public function it_displays_product_stock_information()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'price' => 99.99,
            'stock' => 15,
        ]);

        $response = $this->get(route('products.index'));

        $response->assertSee('Test Product');
        $response->assertSee('15');
    }

    /** @test */
    public function it_passes_cart_data_to_view()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'price' => 99.99,
            'stock' => 10,
        ]);

        // Simuler un panier en session
        session(['cart' => [$product->id => 2]]);

        $response = $this->get(route('products.index'));

        $response->assertStatus(200);
        $response->assertViewHas('cart');
        $response->assertViewHas('products');
    }

    /** @test */
    public function it_shows_empty_message_when_no_products()
    {
        $response = $this->get(route('products.index'));

        $response->assertStatus(200);
        $response->assertSee('Aucun produit disponible');
    }

    /** @test */
    public function it_displays_product_creation_date()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'price' => 99.99,
            'stock' => 10,
        ]);

        $response = $this->get(route('products.index'));

        $formattedDate = $product->created_at->format('d/m/Y');
        $response->assertSee($formattedDate);
    }
}
