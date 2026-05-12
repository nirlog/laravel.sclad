<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Project extends Model
{
    protected $fillable=['user_id','name','address','description','status','started_at','finished_at'];
    protected function casts(): array { return ['started_at'=>'date','finished_at'=>'date']; }
    public function user(){return $this->belongsTo(User::class);} public function materials(){return $this->hasMany(Material::class);} public function materialPurchases(){return $this->hasMany(MaterialPurchase::class);} public function inventoryMovements(){return $this->hasMany(InventoryMovement::class);} public function serviceEntries(){return $this->hasMany(ServiceEntry::class);} public function contractors(){return $this->hasMany(Contractor::class);} public function tags(){return $this->hasMany(Tag::class);} public function materialWriteOffs(){return $this->hasMany(MaterialWriteOff::class);}
}
