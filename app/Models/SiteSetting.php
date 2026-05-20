<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'business_name',
        'gst_tpn',
        'business_licence_number',
        'business_type',
        'drc_registration_number',
        'business_address_line1',
        'business_address_line2',
        'business_city',
        'business_district',
        'business_postal_code',
        'business_phone',
        'business_email',
        'business_website',
        'business_logo_path',
        'default_currency',
        'fiscal_year_start_month',
        'invoice_payment_terms_days',
        'invoice_terms_text',
        'invoice_footer_text',
        'is_gst_registered',
        'default_tax_classification_id',
        'default_bank_account_id',
        'prefix_invoice',
        'prefix_bill',
        'prefix_customer_payment',
        'prefix_supplier_payment',
        'prefix_quotation',
        'prefix_contract',
        'prefix_credit_note',
        'prefix_debit_note',
    ];

    protected function casts(): array
    {
        return [
            'is_gst_registered' => 'boolean',
            'fiscal_year_start_month' => 'integer',
            'invoice_payment_terms_days' => 'integer',
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
            'business_name' => 'Othbar processing and packaging',
            'gst_tpn' => 'TBB29438',
            'business_licence_number' => 'Lic.1054845',
            'business_type' => 'retail',
            'business_address_line1' => 'Othbar Valley',
            'business_city' => 'Thimphu',
            'business_district' => 'Thimphu',
            'business_postal_code' => '11001',
            'business_phone' => '+975-2-XXXXXX',
            'business_email' => 'business@othbar.bt',
            'default_currency' => 'BTN',
            'fiscal_year_start_month' => 1,
            'invoice_payment_terms_days' => 30,
            'invoice_terms_text' => 'Payment due within 30 days. Late payment subject to interest charges.',
            'invoice_footer_text' => 'Thank you for your business!',
            'is_gst_registered' => true,
            'prefix_invoice' => 'INV',
            'prefix_bill' => 'BILL',
            'prefix_customer_payment' => 'RCP',
            'prefix_supplier_payment' => 'SPR',
            'prefix_quotation' => 'QT',
            'prefix_contract' => 'CTR',
            'prefix_credit_note' => 'CN',
            'prefix_debit_note' => 'DN',
        ];
    }

    public function defaultTaxClassification(): BelongsTo
    {
        return $this->belongsTo(TaxClassification::class, 'default_tax_classification_id');
    }

    public function defaultBankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class, 'default_bank_account_id');
    }

    public function businessAddressBlock(): string
    {
        return collect([
            $this->business_address_line1,
            $this->business_address_line2,
            collect([$this->business_city, $this->business_district, $this->business_postal_code])
                ->filter()
                ->implode(', '),
        ])->filter()->implode("\n");
    }

    public function businessContactLine(): string
    {
        return collect([$this->business_phone, $this->business_email])
            ->filter()
            ->implode(' · ');
    }
}
