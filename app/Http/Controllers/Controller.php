<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function handleTransaction($errorMessage, $callback)
    {
        try {
            DB::beginTransaction();
            $response = $callback();
            DB::commit();
            return $response;
        } catch (\Throwable $throwable) {
            DB::rollBack();
            Log::error($errorMessage, ['message' => $throwable->getMessage()]);
            throw $throwable;
        }
    }
}
