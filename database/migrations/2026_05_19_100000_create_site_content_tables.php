<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->default('OTHBAR');
            $table->string('company_subtitle')->default('Horticulture • Bhutan');
            $table->text('announcement_text')->nullable();
            $table->text('footer_about')->nullable();
            $table->text('contact_address')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();

            $table->string('hero_badge')->nullable();
            $table->string('hero_line1')->nullable();
            $table->string('hero_emphasis')->nullable();
            $table->string('hero_line2')->nullable();
            $table->text('hero_description')->nullable();
            $table->string('hero_cta_primary')->nullable();
            $table->string('hero_cta_secondary')->nullable();

            $table->string('home_categories_label')->nullable();
            $table->string('home_categories_title')->nullable();
            $table->string('home_featured_label')->nullable();
            $table->string('home_featured_title')->nullable();
            $table->string('home_story_label')->nullable();
            $table->string('home_story_title')->nullable();
            $table->text('home_story_paragraph_1')->nullable();
            $table->text('home_story_paragraph_2')->nullable();
            $table->string('home_story_media_title')->nullable();
            $table->string('home_story_media_subtitle')->nullable();
            $table->string('home_story_stat_value')->nullable();
            $table->string('home_story_stat_label')->nullable();
            $table->string('home_testimonials_label')->nullable();
            $table->string('home_testimonials_title')->nullable();
            $table->string('newsletter_label')->nullable();
            $table->string('newsletter_title')->nullable();
            $table->text('newsletter_description')->nullable();

            $table->string('story_hero_label')->nullable();
            $table->string('story_hero_title')->nullable();
            $table->text('story_hero_intro')->nullable();
            $table->string('story_origin_label')->nullable();
            $table->string('story_origin_title')->nullable();
            $table->json('story_origin_paragraphs')->nullable();
            $table->string('story_origin_media_title')->nullable();
            $table->string('story_origin_media_subtitle')->nullable();
            $table->string('story_principles_label')->nullable();
            $table->string('story_principles_title')->nullable();
            $table->string('story_team_label')->nullable();
            $table->string('story_team_title')->nullable();
            $table->string('story_cta_title')->nullable();
            $table->text('story_cta_body')->nullable();

            $table->json('provenance_items')->nullable();
            $table->json('stats')->nullable();
            $table->json('testimonials')->nullable();
            $table->json('principles')->nullable();
            $table->json('team_members')->nullable();

            $table->timestamps();
        });

        Schema::create('journal_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('body');
            $table->string('author_name')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_posts');
        Schema::dropIfExists('site_settings');
    }
};
