<?php

namespace App\Filament\Resources\Roles\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                   ->unique(ignoreRecord: true)
                    ->required(),
                 Select::make('permissions')
                    ->relationship('permissions', 'name')
                    ->preload()
                    ->multiple(),

            ]);
    }
}
