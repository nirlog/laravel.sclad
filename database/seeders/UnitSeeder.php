<?php
namespace Database\Seeders;
use App\Models\Unit;use Illuminate\Database\Seeder;
class UnitSeeder extends Seeder{public function run(): void{foreach([['штука','шт'],['кубический метр','м³'],['квадратный метр','м²'],['килограмм','кг'],['тонна','т'],['мешок','мешок'],['погонный метр','пог. м'],['литр','л'],['рулон','рулон'],['пачка','пачка']] as [$name,$short]){Unit::firstOrCreate(['short_name'=>$short],['name'=>$name]);}}}
