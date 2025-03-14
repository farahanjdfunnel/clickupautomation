<?php

namespace App\Exceptions;

use Exception;

class OAuthException extends Exception
{
    public function render($request)
    {
        return response()->json(['error' => $this->getMessage()], $this->code ?: 400);
    }
}
