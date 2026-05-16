<?php

namespace Database\Seeders;

use App\Enums\CouponType;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Database\Seeder;

class OthbarProductSeeder extends Seeder
{
    public function run(): void
    {
        $farms = [
            'Paro Valley Farm' => 'paro-valley-farm',
            'Trongsa Highland' => 'trongsa-highland',
            'Bumthang Organic' => 'bumthang-organic',
            'Othbar Community Farm' => 'othbar-community-farm',
            'Haa Valley Collective' => 'haa-valley-collective',
        ];

        $brandModels = [];
        foreach ($farms as $name => $slug) {
            $brandModels[$name] = Brand::query()->firstOrCreate(
                ['slug' => $slug],
                ['name' => $name, 'is_enabled' => true]
            );
        }

        $categories = [
            ['name' => 'Heritage Grains', 'slug' => 'heritage-grains', 'description' => 'Ancient grain varieties cultivated in Bhutan\'s high-altitude valleys'],
            ['name' => 'Fresh Vegetables', 'slug' => 'fresh-vegetables', 'description' => 'Seasonal organic produce from our mountain farms'],
            ['name' => 'Wild Honey', 'slug' => 'wild-honey', 'description' => 'Forest-gathered cliff honey from traditional hunters'],
            ['name' => 'Himalayan Herbs', 'slug' => 'himalayan-herbs', 'description' => 'Medicinal and culinary herbs from pristine highland meadows'],
            ['name' => 'Preserved Foods', 'slug' => 'preserved-foods', 'description' => 'Fermented, dried, and preserved Bhutanese specialities'],
            ['name' => 'Chili & Spices', 'slug' => 'chili-spices', 'description' => 'The backbone of Bhutanese cuisine — ema datshi and beyond'],
        ];

        $categoryModels = [];
        foreach ($categories as $cat) {
            $categoryModels[$cat['slug']] = Category::query()->firstOrCreate(
                ['slug' => $cat['slug']],
                [
                    'name' => $cat['name'],
                    'description' => $cat['description'],
                    'is_enabled' => true,
                ]
            );
        }

        $collections = [
            ['name' => 'Valley Harvest', 'slug' => 'valley-harvest', 'description' => 'Seasonal picks from western Bhutan — red rice, highland vegetables, and small-batch preserves.'],
            ['name' => 'Forest & Cliff', 'slug' => 'forest-cliff', 'description' => 'Wild honey, medicinal herbs, and forest aromatics gathered by Bhutanese harvesters.'],
            ['name' => 'Staples & Spices', 'slug' => 'staples-spices', 'description' => 'Grains, noodles, ema, and timur — the everyday heart of Bhutanese cooking.'],
        ];

        $collectionModels = [];
        foreach ($collections as $col) {
            $collectionModels[$col['slug']] = Collection::query()->firstOrCreate(
                ['slug' => $col['slug']],
                ['name' => $col['name'], 'description' => $col['description']]
            );
        }

        $products = [
            [
                'name' => 'Bhutanese Red Rice',
                'slug' => 'bhutanese-red-rice',
                'summary' => 'A nutritious, nutty-flavoured medium-grain rice with a distinctive reddish-brown colour, cultivated in the terraced fields of Paro Valley for over a millennium.',
                'description' => '<p>Bhutanese Red Rice is a cultural treasure — a medium-grain rice with a beautiful deep red colour and a rich, nutty flavour. Cultivated in the traditional terraced fields of Paro Valley, it has sustained Bhutanese families for over a thousand years.</p><p>Rich in antioxidants, fibre, manganese, and magnesium, this rice retains its full bran layer, making it far more nutritious than polished white rice. The high-altitude clay soils and pure glacial water of Paro impart a subtle minerality and sweetness that cannot be replicated elsewhere.</p>',
                'price' => 28000,
                'brand' => 'Paro Valley Farm',
                'categories' => ['heritage-grains'],
                'collections' => ['valley-harvest', 'staples-spices'],
                'is_visible' => true,
            ],
            [
                'name' => 'Wild Forest Honey',
                'slug' => 'wild-forest-honey',
                'summary' => 'Rare cliff honey collected by traditional hunters from wild hives in the old-growth forests of Trongsa. Dark, complex, and intensely aromatic.',
                'description' => '<p>Harvested once a year by traditional cliff hunters using bamboo ropes and smoke in the old-growth forests of Trongsa, this wild honey is unlike any commercial product. The bees forage across vast, pristine highland meadows and rhododendron forests, producing a honey of extraordinary depth and complexity.</p><p>Dark amber in colour with notes of wildflower, beeswax, and subtle smokiness, each batch is unique to its harvest season. Raw, unfiltered, and never heated.</p>',
                'price' => 65000,
                'brand' => 'Trongsa Highland',
                'categories' => ['wild-honey'],
                'collections' => ['forest-cliff'],
                'is_visible' => true,
                'quantity' => 80,
            ],
            [
                'name' => 'Highland Buckwheat Flour',
                'slug' => 'highland-buckwheat-flour',
                'summary' => 'Stone-ground from ancient buckwheat varieties grown in the cool plateau valleys of Bumthang at 2,700m. The foundation of traditional Bhutanese pancakes and noodles.',
                'description' => '<p>Bumthang buckwheat has been the backbone of highland Bhutanese cuisine for centuries. Grown at 2,700 metres in the Bumthang plateau — one of the highest cultivated valleys in the Himalayas — these ancient varieties develop exceptional flavour intensity in the short, cool growing season.</p><p>Stone-milled to order in small batches, this flour retains its full nutritional profile and distinctive earthy, slightly bitter character. Use it for traditional Bhutanese buckwheat pancakes (<em>khuli</em>) or pasta.</p>',
                'price' => 18000,
                'brand' => 'Bumthang Organic',
                'categories' => ['heritage-grains'],
                'collections' => ['staples-spices'],
                'is_visible' => true,
                'quantity' => 300,
            ],
            [
                'name' => 'Sun-Dried Ema (Chili Peppers)',
                'slug' => 'sun-dried-ema',
                'summary' => 'The essential ingredient of Bhutan\'s national dish. Whole dried chilies from our farm, sun-dried on bamboo mats to preserve their smoky heat and fruity depth.',
                'description' => '<p>In Bhutan, chili is not a condiment — it is a vegetable, a staple, and the heart of the cuisine. Ema datshi (chili and cheese stew) is the national dish, and the quality of the chili determines everything.</p><p>Our ema are grown from traditional Bhutanese varieties, harvested ripe, and sun-dried on bamboo mats over two to three weeks. The result is a deeply aromatic dried chili with smoky, fruity heat and a complexity that fresh chilies cannot match. Essential for ema datshi, shakam paa, and Bhutanese meat dishes.</p>',
                'price' => 22000,
                'brand' => 'Othbar Community Farm',
                'categories' => ['chili-spices'],
                'collections' => ['staples-spices'],
                'is_visible' => true,
                'quantity' => 400,
            ],
            [
                'name' => 'Himalayan Nettle Tea',
                'slug' => 'himalayan-nettle-tea',
                'summary' => 'Hand-picked young nettle leaves from wild highland meadows in Haa Valley, gently dried to preserve their rich mineral content and deep, earthy flavour.',
                'description' => '<p>Stinging nettle has been used in Bhutanese traditional medicine for centuries. The young spring leaves, gathered from wild meadows above 3,000 metres in Haa Valley, are at their most potent and flavourful before the plant flowers.</p><p>Our harvest team hand-picks only the top two leaves of each plant in April and May, then gently air-dries them in shade to preserve colour, aroma, and nutritional content. The resulting tea is deep green, rich in iron and vitamins, with a clean, slightly herbaceous flavour. Consumed daily by many Bhutanese for general vitality.</p>',
                'price' => 34000,
                'brand' => 'Haa Valley Collective',
                'categories' => ['himalayan-herbs'],
                'collections' => ['forest-cliff'],
                'is_visible' => true,
                'quantity' => 150,
            ],
            [
                'name' => 'Handmade Buckwheat Noodles',
                'slug' => 'buckwheat-noodles',
                'summary' => 'Traditional Bhutanese noodles made by hand from our Bumthang buckwheat flour. Nutty, earthy, and satisfying — a highland pantry staple.',
                'description' => '<p>Made by hand in small batches by cooperative members in Bumthang, these noodles represent a living culinary tradition. The dough is made from nothing but our stone-ground buckwheat flour and water, pressed and cut by hand, then sun-dried on wooden racks.</p><p>They cook in 4–5 minutes and have a pleasantly chewy texture with a robust, earthy flavour that pairs beautifully with Bhutanese curries, simple miso broth, or stir-fried with highland vegetables.</p>',
                'price' => 19500,
                'brand' => 'Bumthang Organic',
                'categories' => ['heritage-grains', 'preserved-foods'],
                'collections' => ['staples-spices', 'valley-harvest'],
                'is_visible' => true,
                'quantity' => 200,
            ],
            [
                'name' => 'Organic Asparagus (Seasonal)',
                'slug' => 'organic-asparagus',
                'summary' => 'Tender asparagus spears grown without pesticides in the fertile alluvial soils of the Punakha valley. Available March through May.',
                'description' => '<p>Asparagus thrives in the deep, rich alluvial soils deposited by the Pho Chhu and Mo Chhu rivers in Punakha. Our variety produces thick, tender spears with exceptional sweetness, due to the cool nights and warm days of the lower Himalayan foothills during spring.</p><p>Harvested each morning before 6am and packed immediately. Available only during the March–May season — order early, as quantities are strictly limited by the natural growing cycle.</p>',
                'price' => 16000,
                'brand' => 'Othbar Community Farm',
                'categories' => ['fresh-vegetables'],
                'collections' => ['valley-harvest'],
                'is_visible' => true,
                'quantity' => 100,
            ],
            [
                'name' => 'Sichuan Pepper (Timur)',
                'slug' => 'timur-sichuan-pepper',
                'summary' => 'Wild-harvested timur (Sichuan pepper) from the forests of Eastern Bhutan. Intensely aromatic with a distinctive citrusy tingle that numbs the palate.',
                'description' => '<p>Known as <em>timur</em> in Bhutan, this is the wild-harvested Zanthoxylum species that grows in the subtropical forests of the eastern districts. It is related to Sichuan pepper but has its own distinct aromatic profile — more citrusy, more floral, and intensely aromatic when fresh.</p><p>Hand-picked and sun-dried, then lightly toasted in our kitchen before packing. A small pinch transforms any dish. Essential in Bhutanese ezay (chili sauce), shakam datshi, and meat preparations. Also extraordinary with scrambled eggs, pasta, or grilled fish.</p>',
                'price' => 29000,
                'brand' => 'Trongsa Highland',
                'categories' => ['chili-spices'],
                'collections' => ['staples-spices', 'forest-cliff'],
                'is_visible' => true,
                'quantity' => 120,
            ],
        ];

        foreach ($products as $productData) {
            $brandName = $productData['brand'];
            $brand = $brandModels[$brandName] ?? null;
            $productCategories = $productData['categories'];
            $productCollections = $productData['collections'] ?? [];
            $price = $productData['price'];
            $qty = $productData['quantity'] ?? 999;

            unset($productData['brand'], $productData['categories'], $productData['collections'], $productData['price'], $productData['quantity']);

            $product = Product::query()->firstOrCreate(
                ['slug' => $productData['slug']],
                array_merge($productData, [
                    'brand_id' => $brand?->id,
                    'stock_quantity' => $qty,
                    'allow_backorder' => false,
                    'is_visible' => true,
                    'price_minor' => $price,
                    'currency_code' => 'BTN',
                ])
            );

            $catIds = [];
            foreach ($productCategories as $catSlug) {
                if (isset($categoryModels[$catSlug])) {
                    $catIds[] = $categoryModels[$catSlug]->id;
                }
            }
            $product->categories()->sync($catIds);

            $collIds = [];
            foreach ($productCollections as $cSlug) {
                if (isset($collectionModels[$cSlug])) {
                    $collIds[] = $collectionModels[$cSlug]->id;
                }
            }
            $product->collections()->sync($collIds);
        }

        Coupon::query()->firstOrCreate(
            ['code' => 'WELCOME10'],
            [
                'type' => CouponType::Percent,
                'value' => 10,
                'starts_at' => now()->subDay(),
                'ends_at' => null,
                'max_uses' => null,
                'uses_count' => 0,
                'is_active' => true,
            ]
        );

        if ($this->command !== null) {
            $this->command->info('Othbar products, collections, and sample coupon seeded successfully!');
        }
    }
}
