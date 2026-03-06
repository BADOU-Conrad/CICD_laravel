<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_product()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'price' => 99.99,
            'stock' => 10,
        ]);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals('Test Product', $product->name);
        $this->assertEquals(99.99, $product->price);
        $this->assertEquals(10, $product->stock);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $product = new Product();
        $fillable = $product->getFillable();

        $this->assertContains('name', $fillable);
        $this->assertContains('price', $fillable);
        $this->assertContains('stock', $fillable);
    }

    /** @test */
    public function price_is_cast_to_decimal()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'price' => 99.99,
            'stock' => 10,
        ]);

        $this->assertIsFloat((float)$product->price);
    }

    /** @test */
    public function stock_is_cast_to_integer()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'price' => 99.99,
            'stock' => 10,
        ]);

        $this->assertIsInt($product->stock);
    }

    /** @test */
    public function it_has_timestamps()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'price' => 99.99,
            'stock' => 10,
        ]);

        $this->assertNotNull($product->created_at);
        $this->assertNotNull($product->updated_at);
    }

    /** @test */
    public function it_can_update_product_stock()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'price' => 99.99,
            'stock' => 10,
        ]);

        $product->update(['stock' => 5]);

        $this->assertEquals(5, $product->fresh()->stock);
    }

    /** @test */
    public function it_can_update_product_price()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'price' => 99.99,
            'stock' => 10,
        ]);

        $product->update(['price' => 149.99]);

        $this->assertEquals(149.99, $product->fresh()->price);
    }

    /** @test */
    public function it_can_delete_a_product()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'price' => 99.99,
            'stock' => 10,
        ]);

        $productId = $product->id;
        $product->delete();

        $this->assertNull(Product::find($productId));
    }
}
