<?php
/**
 * TranslateAPI PHP SDK - API Exception
 * https://github.com/forcekeys/translate-api-php
 */

namespace ForceKeys\TranslateAPI\Exceptions;

use Exception;

/**
 * API Error Exception
 */
class APIException extends Exception
{
    private ?string $errorCode;
    private ?int $statusCode;
    private ?int $retryAfter;
    
    public function __construct(
        string $message, 
        ?string $errorCode = null, 
        ?int $statusCode = null,
        ?int $retryAfter = null,
        int $code = 0,
        ?Exception $previous = null
    ) {
        $this->errorCode = $errorCode;
        $this->statusCode = $statusCode;
        $this->retryAfter = $retryAfter;
        
        parent::__construct($message, $code, $previous);
    }
    
    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }
    
    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }
    
    public function getRetryAfter(): ?int
    {
        return $this->retryAfter;
    }
}
