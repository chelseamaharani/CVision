<?php

namespace App\Exceptions;

use Exception;

/**
 * Exception thrown when AI processing fails.
 */
class AIProcessingException extends Exception
{
    public function __construct(
        string $message = 'AI processing failed',
        int $code = 500,
        ?\Throwable $previous = null,
        public readonly ?array $context = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Create from a failed HTTP response.
     */
    public static function fromHttpResponse(string $endpoint, int $statusCode, string $body): self
    {
        return new self(
            message: "AI Engine returned HTTP {$statusCode} for endpoint {$endpoint}: {$body}",
            code: $statusCode,
            context: [
                'endpoint' => $endpoint,
                'status_code' => $statusCode,
                'response_body' => $body,
            ],
        );
    }
}