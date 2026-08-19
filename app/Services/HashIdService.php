<?php

namespace App\Services;

use Hashids\Hashids;

class HashIdService
{
  public function encodeId($ID)
  {
    $hashid = new Hashids('swiftpay', 6);
    return $hashid->encode($ID);
  }

  public function decodeId($hashedId, $model)
  {
    try {
      $modelName = class_basename($model);
      $hashid = new Hashids("swiftpay_{$modelName}", 6);
      $decoded = $hashid->decode($hashedId);
      return $decoded[0] ?? null;
    } catch (\Throwable $th) {
      abort(404);
    }
  }

  public function getRecord(string $hashedId, $model)
  {
    $id = $this->decodeId($hashedId, $model);
    return $model::findOrFail($id);
  }
}
