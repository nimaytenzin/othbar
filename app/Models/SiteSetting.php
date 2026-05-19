<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = [
        'company_name',
        'company_subtitle',
        'announcement_text',
        'footer_about',
        'contact_address',
        'contact_phone',
        'contact_email',
        'hero_badge',
        'hero_line1',
        'hero_emphasis',
        'hero_line2',
        'hero_description',
        'hero_cta_primary',
        'hero_cta_secondary',
        'home_categories_label',
        'home_categories_title',
        'home_featured_label',
        'home_featured_title',
        'home_story_label',
        'home_story_title',
        'home_story_paragraph_1',
        'home_story_paragraph_2',
        'home_story_media_title',
        'home_story_media_subtitle',
        'home_story_stat_value',
        'home_story_stat_label',
        'home_testimonials_label',
        'home_testimonials_title',
        'newsletter_label',
        'newsletter_title',
        'newsletter_description',
        'story_hero_label',
        'story_hero_title',
        'story_hero_intro',
        'story_origin_label',
        'story_origin_title',
        'story_origin_paragraphs',
        'story_origin_media_title',
        'story_origin_media_subtitle',
        'story_principles_label',
        'story_principles_title',
        'story_team_label',
        'story_team_title',
        'story_cta_title',
        'story_cta_body',
        'provenance_items',
        'stats',
        'testimonials',
        'principles',
        'team_members',
        'payment_channels',
        'payment_merchant_account',
        'pickup_address_label',
        'gst_percentage',
    ];

    protected function casts(): array
    {
        return [
            'gst_percentage' => 'decimal:2',
            'story_origin_paragraphs' => 'array',
            'provenance_items' => 'array',
            'stats' => 'array',
            'testimonials' => 'array',
            'principles' => 'array',
            'team_members' => 'array',
            'payment_channels' => 'array',
            'payment_merchant_account' => 'array',
        ];
    }

    public static function current(): self
    {
        return Cache::rememberForever('site_settings', function () {
            return static::query()->firstOrCreate(
                ['id' => 1],
                static::defaults()
            );
        });
    }

    public static function clearCache(): void
    {
        Cache::forget('site_settings');
    }

    protected static function booted(): void
    {
        static::saved(fn () => static::clearCache());
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaults(): array
    {
        return [
            'company_name' => 'OTHBAR',
            'company_subtitle' => 'Horticulture • Bhutan',
            'announcement_text' => 'Free delivery within Thimphu • Certified Organic • Grown at 2,400m elevation',
            'footer_about' => 'Nestled in the sacred valleys of Bhutan at 2,400 metres, we cultivate organic food with reverence for the land, guided by ancient Bhutanese agricultural wisdom.',
            'contact_address' => "Othbar Valley\nPunakha Dzongkhag\nBhutan",
            'contact_phone' => '+975 02 123 456',
            'contact_email' => 'hello@othbar.bt',
            'hero_badge' => 'Est. 2018 • Certified Organic • Punakha, Bhutan',
            'hero_line1' => 'From the',
            'hero_emphasis' => "Dragon Kingdom's",
            'hero_line2' => 'own earth',
            'hero_description' => "High-altitude organic farming practiced with Gross National Happiness at its core. Every harvest carries the spirit of Bhutan's pristine valleys.",
            'hero_cta_primary' => 'Explore the Harvest',
            'hero_cta_secondary' => 'Our Story',
            'home_categories_label' => 'What we grow',
            'home_categories_title' => 'Categories of the harvest',
            'home_featured_label' => 'Latest harvest',
            'home_featured_title' => 'Featured products',
            'home_story_label' => 'The Othbar way',
            'home_story_title' => 'Farming guided by Gross National Happiness',
            'home_story_paragraph_1' => "In the verdant valleys of Punakha, our farmers cultivate with a philosophy rooted in Bhutan's unique vision — that happiness and ecological balance are inseparable. No chemical inputs. No shortcuts.",
            'home_story_paragraph_2' => 'We grow heirloom varieties that have fed Bhutanese families for centuries — red rice from Paro, buckwheat from Bumthang, wild cliff honey collected by traditional hunters in Trongsa.',
            'home_story_media_title' => 'Punakha Valley',
            'home_story_media_subtitle' => '2,400 metres above sea level',
            'home_story_stat_value' => '6+',
            'home_story_stat_label' => "Years of\ncultivation",
            'home_testimonials_label' => 'What people say',
            'home_testimonials_title' => 'From our customers',
            'newsletter_label' => 'Stay connected',
            'newsletter_title' => 'Seasonal harvest updates',
            'newsletter_description' => 'Be the first to know when new products arrive, learn about our farming practices, and receive exclusive offers.',
            'story_hero_label' => 'Who we are',
            'story_hero_title' => 'Rooted in the earth of the Last Shangri-La',
            'story_hero_intro' => "Founded in 2018 by a collective of 47 farming families in Punakha, Othbar exists to share Bhutan's extraordinary organic heritage with the world — without compromising the land that makes it possible.",
            'story_origin_label' => 'The beginning',
            'story_origin_title' => 'How Othbar came to be',
            'story_origin_paragraphs' => [
                ['body' => "The name Othbar comes from an ancient Dzongkha word for the high-altitude terraced fields where our founders' grandparents first cultivated red rice. When the youngest generation began returning to these valleys after studying modern agriculture, they brought with them a question: How do we honour what our ancestors knew while building something that can sustain our community's future?"],
                ['body' => 'The answer was a cooperative. Forty-seven families pooling their land, knowledge, and labour — certified organic from day one, committed to zero synthetic inputs, and guided by Bhutan\'s own framework of Gross National Happiness.'],
                ['body' => 'Today we cultivate 120 acres across Punakha and Paro, growing 28 varieties of heritage crops. We sell directly to homes across Bhutan and to a small number of international partners who share our values.'],
            ],
            'story_origin_media_title' => 'Punakha Valley',
            'story_origin_media_subtitle' => 'Est. 2018',
            'story_principles_label' => 'What drives us',
            'story_principles_title' => 'Our principles',
            'story_team_label' => 'The people',
            'story_team_title' => 'Our farming families',
            'story_cta_title' => 'Taste the difference',
            'story_cta_body' => 'Every purchase supports our farming families directly and funds the regeneration of traditional Bhutanese agriculture.',
            'provenance_items' => [
                ['icon' => '🏔', 'text' => 'Grown at 2,400m'],
                ['icon' => '🌱', 'text' => 'Zero Pesticides'],
                ['icon' => '🌿', 'text' => 'Heirloom Varieties'],
                ['icon' => '♻', 'text' => 'Carbon Neutral'],
                ['icon' => '🤝', 'text' => 'Community Owned'],
                ['icon' => '🧡', 'text' => 'GNH Certified'],
            ],
            'stats' => [
                ['value' => '47', 'unit' => 'Farmer families', 'description' => 'community owners'],
                ['value' => '120', 'unit' => 'Acres', 'description' => 'of certified organic land'],
                ['value' => '28', 'unit' => 'Varieties', 'description' => 'of heirloom crops'],
                ['value' => '100%', 'unit' => 'Organic', 'description' => 'zero synthetic inputs'],
            ],
            'testimonials' => [
                [
                    'quote' => 'The red rice from Othbar has completely transformed our family meals. You can taste the difference — nutty, complex, and deeply satisfying. Nothing like what you find in supermarkets.',
                    'name' => 'Karma Wangchuk',
                    'location' => 'Thimphu',
                    'rating' => 5,
                ],
                [
                    'quote' => 'Their wild honey is extraordinary. I have tried honey from across Asia, but the depth of flavour from the Trongsa cliff honey is unlike anything I have experienced. A true treasure of Bhutan.',
                    'name' => 'Dr. Tshering Pem',
                    'location' => 'Paro',
                    'rating' => 5,
                ],
                [
                    'quote' => 'Ordering from Othbar feels like a direct connection to the land. The packaging is beautiful, the produce is impeccable, and knowing the farmers are part of the cooperative makes it meaningful.',
                    'name' => 'Sonam Dorji',
                    'location' => 'Punakha',
                    'rating' => 5,
                ],
            ],
            'principles' => [
                [
                    'number' => '01',
                    'title' => 'Earth before profit',
                    'body' => 'Every farming decision is evaluated first by its impact on the soil, water, and biodiversity of the Punakha and Paro valleys. Profitability follows ecological health, never leads it.',
                ],
                [
                    'number' => '02',
                    'title' => 'Ancient knowledge, modern rigour',
                    'body' => 'We combine the intergenerational farming wisdom of our cooperative members with contemporary organic certification standards and sustainable agriculture research.',
                ],
                [
                    'number' => '03',
                    'title' => 'Community ownership',
                    'body' => 'Othbar is collectively owned by all 47 member families. Decisions are made by consensus. Profits are distributed equally. No investor holds a stake in our land.',
                ],
            ],
            'team_members' => [
                ['name' => 'Tshering Lhamo', 'role' => 'Lead farmer, red rice', 'valley' => 'Paro Valley'],
                ['name' => 'Karma Wangdi', 'role' => 'Honey cooperative head', 'valley' => 'Trongsa'],
                ['name' => 'Sonam Choki', 'role' => 'Herb cultivation', 'valley' => 'Haa Valley'],
                ['name' => 'Jigme Dorji', 'role' => 'Cooperative director', 'valley' => 'Punakha'],
            ],
            'payment_merchant_account' => [
                'bank_label' => config('payments.merchant_account.bank_label'),
                'account_name' => config('payments.merchant_account.account_name'),
                'account_number' => config('payments.merchant_account.account_number'),
                'qr_path' => null,
            ],
            'payment_channels' => [],
            'pickup_address_label' => config('payments.pickup_address_label', 'In-store pickup at Othbar'),
            'gst_percentage' => 5,
        ];
    }
}
