<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RentalResource\Pages;
use App\Filament\Resources\RentalResource\RelationManagers;
use App\Models\Rental;
use App\Models\RentalItem;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;


use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\DateTimePicker;

class RentalResource extends Resource
{
    protected static ?string $model = Rental::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // DateTimePicker::make('created_at')
                // ->label('Creation Date')
                // ->disabled()
                // ->default(now()),

                // Forms\Components\TextInput::make('customer_name')
                //     ->required(),

                    DateTimePicker::make('created_at')
                    ->label('Creation Date')
                    ->disabled()
                    ->default(now()),
                    TextInput::make('customer_name')
                    ->required(),
                    DatePicker::make('rental_date')
                        ->required(),
                    DatePicker::make('return_date'),

                    Repeater::make('items')
                    ->relationship('items')
                    ->schema([
                        TextInput::make('item_name')->required(),
                        TextInput::make('quantity')
                            ->required()
                            ->numeric()
                            ->reactive(),
                        TextInput::make('price_per_day')
                            ->required()
                            ->numeric()
                            ->reactive(),
                        TextInput::make('total_price')
                            ->label('Total Price')
                            ->numeric()
                            ->disabled()
                            ->reactive(),
                    ])
                    ->createItemButtonLabel('Add Item')
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, $get) {
                        static::updateTotalRentalPrice($set, $get);
                    }),
                TextInput::make('total_rental_price')
                    ->label('Total Rental Price')
                    ->numeric()
                    ->disabled()
                    ->reactive(),
            ])
            ->columns(2);
    }

    protected static function updateTotalRentalPrice(callable $set, callable $get)
    {
        $items = $get('items') ?? [];
        $totalRentalPrice = 0;

        foreach ($items as $item) {
            $quantity = $item['quantity'] ?? 0;
            $pricePerDay = $item['price_per_day'] ?? 0;
            $totalPrice = $quantity * $pricePerDay;

            $set('items.' . array_search($item, $items) . '.total_price', $totalPrice);
            $totalRentalPrice += $totalPrice;
        }

        $set('total_rental_price', $totalRentalPrice);
    }






    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creation Date')
                    ->dateTime(),

                Tables\Columns\TextColumn::make('customer_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('item_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rental_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('return_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price_per_day')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRentals::route('/'),
            'create' => Pages\CreateRental::route('/create'),
            'edit' => Pages\EditRental::route('/{record}/edit'),
        ];
    }
}
