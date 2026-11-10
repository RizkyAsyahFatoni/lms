<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

/**
 * Base Secure Form Request
 *
 * Extended oleh semua FormRequest untuk menambahkan:
 * - Proteksi CSRF otomatis (Laravel built-in)
 * - Logging gagal validasi untuk audit trail
 * - Sanitasi otomatis sebelum validasi
 * - Mencegah parameter pollution
 */
abstract class SecureFormRequest extends FormRequest
{
    /**
     * Handle failed validation - log attempt dan kembalikan error
     */
    protected function failedValidation(Validator $validator): void
    {
        Log::info('[VALIDATION] Request validation failed', [
            'ip'        => $this->ip(),
            'url'       => $this->fullUrl(),
            'method'    => $this->method(),
            'errors'    => $validator->errors()->toArray(),
            'user_id'   => auth()->id(),
        ]);

        parent::failedValidation($validator);
    }

    /**
     * Persiapkan data sebelum validasi: bersihkan input berbahaya
     */
    protected function prepareForValidation(): void
    {
        // Hapus kolom yang tidak seharusnya ada dalam request
        // (mencegah mass-assignment via HTTP override)
        $this->request->remove('_method_override');

        // Trim semua string input (kecuali password)
        $data = $this->all();
        $trimmed = $this->trimStrings($data);
        $this->replace($trimmed);
    }

    /**
     * Trim semua string kecuali field yang dikecualikan
     */
    protected function trimStrings(array $data, array $except = ['password', 'password_confirmation', 'current_password']): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->trimStrings($value, $except);
            } elseif (is_string($value) && !in_array($key, $except)) {
                $data[$key] = trim($value);
            }
        }
        return $data;
    }

    /**
     * Response untuk otorisasi gagal (anti-enumeration: jangan bocorkan info)
     */
    protected function failedAuthorization(): void
    {
        Log::warning('[SECURITY] Unauthorized form submission attempt', [
            'ip'      => $this->ip(),
            'url'     => $this->fullUrl(),
            'user_id' => auth()->id(),
        ]);

        throw new HttpResponseException(
            response()->json(['message' => 'Tindakan ini tidak diizinkan.'], 403)
        );
    }
}
