<?php

namespace Coinpush\Exceptions;

use Exception;

class RequestException extends Exception
{
    /**
     * @var int
     */
    private $statusCode = 0;

    /**
     * @var array
     */
    private $response = [];

    /**
     * @param string $message
     * @param int $statusCode
     * @param array $response
     * @param null|\Exception $previous
     */
    public function __construct(
        $message,
        $statusCode = 0,
        array $response = [],
        Exception $previous = null
    )
    {
        parent::__construct($message, $statusCode, null);

        $this->statusCode = $statusCode;
        $this->response = $response;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return array
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $className = __CLASS__;
        $statusCode = $this->statusCode;
        $message = rtrim($this->message, "\n");
        $stackTrace = $this->getTraceAsString();

        return "{$className}: [{$statusCode}]: {$message}\n{$stackTrace}";
    }
}
