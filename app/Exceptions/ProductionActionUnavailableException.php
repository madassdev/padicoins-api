<?php

namespace App\Exceptions;

use Exception;

class ProductionActionUnavailableException extends Exception
{
    //
    public $message;
    public $code;
    public function __construct($message = null, $code = 400)
    {
        $this->message = $message;
        $this->code = $code;
    }

    public function render()
    {
        return response()->json([
            'success' => false,
            'message' => $this->message ?? "This action is currently unavailable on production environment , please try on development environment.",

        ], $this->code);
    }
}
