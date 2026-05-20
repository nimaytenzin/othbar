<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Tests\TestCase;

class ShopPageTest extends TestCase
{
    public function test_shop_sorts_by_price_within_category(): void
    {
        $category = Category::query()->create([
            'name' => 'Chili & Spices',
            'slug' => 'chili-spices',
            'is_enabled' => true,
        ]);

        $cheap = Product::query()->create([
            'name' => 'Cheap Spice',
            'slug' => 'cheap-spice',
            'is_visible' => true,
            'price_minor' => 10_000,
        ]);

        $expensive = Product::query()->create([
            'name' => 'Expensive Spice',
            'slug' => 'expensive-spice',
            'is_visible' => true,
            'price_minor' => 50_000,
        ]);

        Product::query()->create([
            'name' => 'Other Product',
            'slug' => 'other-product',
            'is_visible' => true,
            'price_minor' => 500,
        ]);

        $category->products()->attach([$cheap->id, $expensive->id]);

        $response = $this->get(route('shop', [
            'category' => 'chili-spices',
            'sort' => 'price_asc',
        ]));

        $response->assertOk();
        $content = $response->getContent();

        $this->assertStringContainsString('Cheap Spice', $content);
        $this->assertStringContainsString('Expensive Spice', $content);
        $this->assertStringNotContainsString('Other Product', $content);
        $this->assertLessThan(
            strpos($content, 'Expensive Spice'),
            strpos($content, 'Cheap Spice'),
        );
    }

    public function test_shop_sort_select_preserves_category_in_form(): void
    {
        $response = $this->get(route('shop', ['category' => 'chili-spices']));

        $response->assertOk();
        $response->assertSee('name="category" value="chili-spices"', false);
        $response->assertSee('value="price_asc"', false);
    }
}
