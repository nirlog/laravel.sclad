<?php
namespace App\Filament\Pages;
use Filament\Pages\Page;
class Inventory extends Page
{
    protected static ?string $navigationIcon='heroicon-o-building-storefront';
    protected static ?string $navigationLabel='Склад';
    protected static ?string $title='Складские остатки';
    protected string $view='filament.pages.inventory';
}
