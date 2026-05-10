<?php
namespace App\Filament\Widgets;
use App\Models\Project;use App\Services\CostAnalyticsService;use Filament\Widgets\StatsOverviewWidget;use Filament\Widgets\StatsOverviewWidget\Stat;
class ConstructionSummaryWidget extends StatsOverviewWidget
{ protected function getStats(): array{$project=Project::query()->first();$data=$project?app(CostAnalyticsService::class)->getActualPayments($project):[];return [Stat::make('Фактические расходы',number_format($data['actual_total']??0,0,',',' ').' ₽'),Stat::make('Покупки материалов',number_format($data['materials_purchased_total']??0,0,',',' ').' ₽'),Stat::make('Услуги',number_format($data['services_total']??0,0,',',' ').' ₽'),Stat::make('Списано материалов',number_format($data['materials_written_off_total']??0,0,',',' ').' ₽'),Stat::make('Стоимость склада',number_format($data['inventory_value']??0,0,',',' ').' ₽')];}}
