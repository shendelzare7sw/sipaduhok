<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class RejectEmailHeaderInjection
{
    public function handle(Request $request, Closure $next): Response
    {
        $messages = [];
        $this->scanEmailFields($request->all(), '', $messages);

        if (!empty($messages)) {
            throw ValidationException::withMessages($messages);
        }

        return $next($request);
    }

    private function scanEmailFields(array $data, string $prefix, array &$messages): void
    {
        foreach ($data as $key => $value) {
            $field = $prefix === '' ? (string) $key : $prefix . '.' . $key;

            if (is_array($value)) {
                $this->scanEmailFields($value, $field, $messages);
                continue;
            }

            if (!is_string($value) || !str_contains(strtolower((string) $key), 'email')) {
                continue;
            }

            if (str_contains($value, "\r") || str_contains($value, "\n")) {
                $messages[$field] = 'Alamat email tidak valid.';
            }
        }
    }
}
