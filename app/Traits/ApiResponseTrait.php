<?php

namespace App\Traits;

trait ApiResponseTrait
{
  /**
   * Return a success JSON response.
   *
   * @param  array|string  $data
   * @param  string  $message
   * @param  int  $code
   * @return \Illuminate\Http\JsonResponse
   */
  protected function success($data, string $message, int $code = 200)
  {
    return response()->json([
      'status' => 'success',
      'message' => $message,
      'data' => $data
    ], $code);
  }

  /**
   * Return an error JSON response.
   *
   * @param  string  $message
   * @param  int  $code
   * @param  array|string|null  $data
   * @return \Illuminate\Http\JsonResponse
   */
  protected function error(string $message, int $code = 400, $errors = null)
  {
    return response()->json([
      'status' => 'error',
      'message' => $message,
      'errors' => $errors
    ], $code);
  }

  /**
   * Return a validation error JSON response.
   * 
   * @param  \Illuminate\Validation\Validator  $validator
   * @return \Illuminate\Http\JsonResponse
   */
  protected function validationError($validator)
  {
    return $this->error(
      'Validation failed',
      422,
      $validator->errors()
    );
  }
}
