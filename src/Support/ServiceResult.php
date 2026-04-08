<?php

namespace KaueF\Structura\Support;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceResult implements Responsable
{
    /**
     * @param  bool  $success  Determines if the service executed successfully.
     * @param  mixed  $data  The payload or entity processed.
     * @param  string|null  $message  Feedback message.
     * @param  int  $status  Standard HTTP Status code alias.
     */
    protected function __construct(
        public readonly bool $success,
        public readonly mixed $data = null,
        public readonly ?string $message = null,
        public readonly int $status = 200,
    ) {}

    /**
     * Factory for a successful result.
     */
    public static function success(mixed $data = null, ?string $message = null, int $status = 200): self
    {
        return new self(success: true, data: $data, message: $message, status: $status);
    }

    /**
     * Factory for a failed result.
     */
    public static function fail(?string $message = null, int $status = 400, mixed $data = null): self
    {
        return new self(success: false, data: $data, message: $message, status: $status);
    }

    /**
     * Checks if the result is a success.
     */
    public function isSuccess(): bool
    {
        return $this->success === true;
    }

    /**
     * Checks if the result is a failure.
     */
    public function isFail(): bool
    {
        return $this->success === false;
    }

    /**
     * Formats the result as an HTTP response.
     * Allows controllers to simply return the ServiceResult directly.
     *
     * @param  Request  $request
     */
    public function toResponse($request): JsonResponse
    {
        $payload = [
            'success' => $this->success,
        ];

        if ($this->message !== null) {
            $payload['message'] = $this->message;
        }

        if ($this->data !== null) {
            $payload['data'] = $this->data;
        }

        return response()->json($payload, $this->status);
    }
}
