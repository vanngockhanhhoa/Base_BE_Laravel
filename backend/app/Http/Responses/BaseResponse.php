<?php

namespace App\Http\Responses;

use Illuminate\Http\Response;

class BaseResponse
{
    public $code = Response::HTTP_OK;
    public $message;

    // Common method
    public function printOut()
    {
        print $this->getCode() . "\n";
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code): void
    {
        $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message): void
    {
        $this->message = $message;
    }
}
