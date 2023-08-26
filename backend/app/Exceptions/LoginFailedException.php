<?php

namespace App\Exceptions;

use Exception;

class LoginFailedException extends Exception
{

    private $status = 422;

    protected $message = '';

    /**
     * Create a new exception instance.
     *
     * @param string $message
     */
    public function __construct($message = '')
    {
        $this->message = $message;
    }

    public function getStatusCode()
    {
        return $this->status;
    }
}
