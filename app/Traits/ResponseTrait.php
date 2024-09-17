<?php

namespace App\Traits;

trait ResponseTrait
{
    public function getResponse($key, $val, $code)
    {
        return response(
            [
                'isSuccess'         => ($code >= 200 && $code < 300) ? true : false,
                $key                =>      $val
            ],
            $code
        );
    }
}
