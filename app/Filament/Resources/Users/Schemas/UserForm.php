<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->unique(ignoreRecord: false)
                    ->email()
                    ->required(),
                Select::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->required(),



              TextInput::make('password')
                ->password()
                ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                ->saved(fn (?string $state): bool => filled($state))
                ->required(fn (string $operation): bool => $operation === 'create')
            ]);
    }
}
