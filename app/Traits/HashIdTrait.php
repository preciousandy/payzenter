<?php

namespace App\Traits;

use Hashids\Hashids;


trait HashIdTrait
{


  public function getHashIdAttribute()
  {
    $modelName = class_basename($this);
    $hashid = new Hashids("swiftpay_{$modelName}", 6);
    return $hashid->encode($this->id);
  }
}
