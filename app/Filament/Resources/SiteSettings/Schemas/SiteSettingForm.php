<?php

namespace App\Filament\Resources\SiteSettings\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class SiteSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Tabs::make('Website content')
                    ->tabs([
                        Tab::make('Brand & contact')
                            ->schema(static::brandTab()),
                        Tab::make('Homepage hero')
                            ->schema(static::heroTab()),
                        Tab::make('Homepage sections')
                            ->schema(static::homeSectionsTab()),
                        Tab::make('Story page')
                            ->schema(static::storyTab()),
                        Tab::make('Provenance strip')
                            ->schema(static::provenanceTab()),
                        Tab::make('Stats')
                            ->schema(static::statsTab()),
                        Tab::make('Testimonials')
                            ->schema(static::testimonialsTab()),
                        Tab::make('Principles')
                            ->schema(static::principlesTab()),
                        Tab::make('Team')
                            ->schema(static::teamTab()),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    /**
     * @return array<int, \Filament\Schemas\Components\Component>
     */
    protected static function brandTab(): array
    {
        return [
            Section::make('Company identity')
                ->columns(2)
                ->schema([
                    TextInput::make('company_name')->required()->maxLength(120),
                    TextInput::make('company_subtitle')->required()->maxLength(120),
                    Textarea::make('announcement_text')->rows(2)->columnSpanFull(),
                    Textarea::make('footer_about')->rows(4)->columnSpanFull(),
                ]),
            Section::make('Contact')
                ->columns(2)
                ->schema([
                    Textarea::make('contact_address')->rows(3)->columnSpanFull(),
                    TextInput::make('contact_phone')->tel()->maxLength(50),
                    TextInput::make('contact_email')->email()->maxLength(150),
                ]),
        ];
    }

    /**
     * @return array<int, \Filament\Schemas\Components\Component>
     */
    protected static function heroTab(): array
    {
        return [
            TextInput::make('hero_badge')->maxLength(255)->columnSpanFull(),
            TextInput::make('hero_line1')->label('Headline line 1')->maxLength(120),
            TextInput::make('hero_emphasis')->label('Headline emphasis (italic)')->maxLength(120),
            TextInput::make('hero_line2')->label('Headline line 2')->maxLength(120),
            Textarea::make('hero_description')->rows(4)->columnSpanFull(),
            TextInput::make('hero_cta_primary')->label('Primary button label')->maxLength(80),
            TextInput::make('hero_cta_secondary')->label('Secondary button label')->maxLength(80),
        ];
    }

    /**
     * @return array<int, \Filament\Schemas\Components\Component>
     */
    protected static function homeSectionsTab(): array
    {
        return [
            Section::make('Categories')
                ->columns(2)
                ->schema([
                    TextInput::make('home_categories_label')->maxLength(120),
                    TextInput::make('home_categories_title')->maxLength(255),
                ]),
            Section::make('Featured products')
                ->columns(2)
                ->schema([
                    TextInput::make('home_featured_label')->maxLength(120),
                    TextInput::make('home_featured_title')->maxLength(255),
                ]),
            Section::make('Story teaser (homepage)')
                ->schema([
                    TextInput::make('home_story_label')->maxLength(120),
                    TextInput::make('home_story_title')->maxLength(255)->columnSpanFull(),
                    Textarea::make('home_story_paragraph_1')->rows(3)->columnSpanFull(),
                    Textarea::make('home_story_paragraph_2')->rows(3)->columnSpanFull(),
                    TextInput::make('home_story_media_title')->maxLength(120),
                    TextInput::make('home_story_media_subtitle')->maxLength(120),
                    TextInput::make('home_story_stat_value')->label('Stat box value')->maxLength(20),
                    Textarea::make('home_story_stat_label')->label('Stat box label')->rows(2),
                ]),
            Section::make('Testimonials heading')
                ->columns(2)
                ->schema([
                    TextInput::make('home_testimonials_label')->maxLength(120),
                    TextInput::make('home_testimonials_title')->maxLength(255),
                ]),
            Section::make('Newsletter')
                ->schema([
                    TextInput::make('newsletter_label')->maxLength(120),
                    TextInput::make('newsletter_title')->maxLength(255)->columnSpanFull(),
                    Textarea::make('newsletter_description')->rows(3)->columnSpanFull(),
                ]),
        ];
    }

    /**
     * @return array<int, \Filament\Schemas\Components\Component>
     */
    protected static function storyTab(): array
    {
        return [
            Section::make('Hero')
                ->schema([
                    TextInput::make('story_hero_label')->maxLength(120),
                    TextInput::make('story_hero_title')->maxLength(255)->columnSpanFull(),
                    Textarea::make('story_hero_intro')->rows(4)->columnSpanFull(),
                ]),
            Section::make('Origin story')
                ->schema([
                    TextInput::make('story_origin_label')->maxLength(120),
                    TextInput::make('story_origin_title')->maxLength(255)->columnSpanFull(),
                    Repeater::make('story_origin_paragraphs')
                        ->label('Body paragraphs')
                        ->schema([
                            Textarea::make('body')->required()->rows(4)->columnSpanFull(),
                        ])
                        ->columnSpanFull(),
                    TextInput::make('story_origin_media_title')->maxLength(120),
                    TextInput::make('story_origin_media_subtitle')->maxLength(120),
                ]),
            Section::make('Section headings')
                ->columns(2)
                ->schema([
                    TextInput::make('story_principles_label')->maxLength(120),
                    TextInput::make('story_principles_title')->maxLength(255),
                    TextInput::make('story_team_label')->maxLength(120),
                    TextInput::make('story_team_title')->maxLength(255),
                ]),
            Section::make('Call to action')
                ->schema([
                    TextInput::make('story_cta_title')->maxLength(255)->columnSpanFull(),
                    Textarea::make('story_cta_body')->rows(3)->columnSpanFull(),
                ]),
        ];
    }

    /**
     * @return array<int, \Filament\Schemas\Components\Component>
     */
    protected static function provenanceTab(): array
    {
        return [
            Repeater::make('provenance_items')
                ->label('Provenance highlights')
                ->schema([
                    TextInput::make('icon')->label('Icon / emoji')->maxLength(10)->required(),
                    TextInput::make('text')->required()->maxLength(120),
                ])
                ->columns(2)
                ->collapsible()
                ->defaultItems(0)
                ->columnSpanFull(),
        ];
    }

    /**
     * @return array<int, \Filament\Schemas\Components\Component>
     */
    protected static function statsTab(): array
    {
        return [
            Repeater::make('stats')
                ->label('Impact stats')
                ->schema([
                    TextInput::make('value')->required()->maxLength(20),
                    TextInput::make('unit')->required()->maxLength(80),
                    TextInput::make('description')->required()->maxLength(120),
                ])
                ->columns(3)
                ->collapsible()
                ->defaultItems(0)
                ->columnSpanFull(),
        ];
    }

    /**
     * @return array<int, \Filament\Schemas\Components\Component>
     */
    protected static function testimonialsTab(): array
    {
        return [
            Repeater::make('testimonials')
                ->label('Customer stories')
                ->schema([
                    Textarea::make('quote')->required()->rows(4)->columnSpanFull(),
                    TextInput::make('name')->required()->maxLength(120),
                    TextInput::make('location')->required()->maxLength(120),
                    TextInput::make('rating')->numeric()->minValue(1)->maxValue(5)->default(5),
                ])
                ->collapsible()
                ->defaultItems(0)
                ->columnSpanFull(),
        ];
    }

    /**
     * @return array<int, \Filament\Schemas\Components\Component>
     */
    protected static function principlesTab(): array
    {
        return [
            Repeater::make('principles')
                ->schema([
                    TextInput::make('number')->label('Number')->maxLength(10)->required(),
                    TextInput::make('title')->required()->maxLength(150),
                    Textarea::make('body')->required()->rows(3)->columnSpanFull(),
                ])
                ->collapsible()
                ->defaultItems(0)
                ->columnSpanFull(),
        ];
    }

    /**
     * @return array<int, \Filament\Schemas\Components\Component>
     */
    protected static function teamTab(): array
    {
        return [
            Repeater::make('team_members')
                ->label('Farming families')
                ->schema([
                    TextInput::make('name')->required()->maxLength(120),
                    TextInput::make('role')->required()->maxLength(150),
                    TextInput::make('valley')->required()->maxLength(120),
                ])
                ->columns(3)
                ->collapsible()
                ->defaultItems(0)
                ->columnSpanFull(),
        ];
    }
}
