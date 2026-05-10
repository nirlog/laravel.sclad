<?php
namespace App\Models;
trait ConcernsTags { public function tags(){ return $this->morphToMany(Tag::class,'taggable')->withTimestamps(); } }
