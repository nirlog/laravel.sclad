<?php
namespace App\Actions;
use App\Models\ServiceEntry;use Illuminate\Support\Facades\DB;
class CreateServiceEntryAction{public function execute(array $data): ServiceEntry{return DB::transaction(function() use($data){$tagIds=$data['tag_ids']??[];unset($data['tag_ids']);$data['total_amount']=$this->total($data);$data['paid_amount']=$data['paid_amount'] ?? ((($data['payment_status'] ?? 'paid') === 'paid') ? $data['total_amount'] : 0);$entry=ServiceEntry::create($data);$entry->tags()->sync($tagIds);return $entry->load('tags','contractor');});} private function total(array $d): float{return match($d['pricing_type']){'hourly'=>round((float)$d['hours']*(float)$d['hourly_rate'],2),'unit'=>round((float)$d['quantity']*(float)$d['unit_price'],2),default=>round((float)$d['total_amount'],2)};}}
