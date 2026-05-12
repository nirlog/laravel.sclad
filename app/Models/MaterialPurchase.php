<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class MaterialPurchase extends Model { use ConcernsTags; protected $fillable=['project_id','date','supplier_name','document_number','payment_status','total_amount','comment']; protected function casts(): array{return ['date'=>'date','total_amount'=>'decimal:2'];} public function project(){return $this->belongsTo(Project::class);} public function items(){return $this->hasMany(MaterialPurchaseItem::class);} public function attachments(){return $this->morphMany(Attachment::class,'attachable');} }
