<?php

namespace App\Filament\Resources\JournalPosts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
class JournalPostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Article')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true),
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('author_name')
                            ->maxLength(120)
                            ->placeholder('Optional display name'),
                        Textarea::make('excerpt')
                            ->rows(3)
                            ->helperText('Shown on the journal listing page.')
                            ->columnSpanFull(),
                        RichEditor::make('body')
                            ->required()
                            ->columnSpanFull(),
                    ]),
                Section::make('Featured image')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('featured')
                            ->label('Cover image')
                            ->collection('featured')
                            ->disk('public')
                            ->image()
                            ->maxFiles(1)
                            ->columnSpanFull(),
                    ]),
                Section::make('Publishing')
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_published')
                            ->label('Published')
                            ->default(false),
                        DateTimePicker::make('published_at')
                            ->label('Publish date')
                            ->seconds(false)
                            ->native(false),
                    ]),
            ]);
    }
}
