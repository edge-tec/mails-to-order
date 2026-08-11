<?php

namespace App\Services;

use Exception;

class EncryptionService {
    private static function getKey(): string {
        $key = config('app.key', '');
        if (empty($key)) {
            $key = getenv('APP_KEY') ?: 'v9B7xK3mP8qR2wZ5tY0uL4nJ1sF6aE9c';
        }
        return hash('sha256', $key, true); // 32 byte key for aes-256-gcm
    }

    public static function encrypt(string $plaintext): string {
        if ($plaintext === '') {
            return '';
        }
        $key = self::getKey();
        $cipher = "aes-256-gcm";
        $ivlen = openssl_cipher_iv_length($cipher);
        $iv = openssl_random_pseudo_bytes($ivlen);
        
        $tag = "";
        $ciphertext = openssl_encrypt($plaintext, $cipher, $key, OPENSSL_RAW_DATA, $iv, $tag);
        if ($ciphertext === false) {
            throw new Exception("Encryption failed.");
        }
        
        // Return base64 encoded payload: iv + tag + ciphertext
        return base64_encode($iv . $tag . $ciphertext);
    }

    public static function decrypt(string $encryptedData): string {
        if (empty($encryptedData)) {
            return '';
        }
        $raw = base64_decode($encryptedData);
        if ($raw === false) {
            return '';
        }
        
        $key = self::getKey();
        $cipher = "aes-256-gcm";
        $ivlen = openssl_cipher_iv_length($cipher);
        $taglen = 16;
        
        if (strlen($raw) < ($ivlen + $taglen)) {
            return '';
        }
        
        $iv = substr($raw, 0, $ivlen);
        $tag = substr($raw, $ivlen, $taglen);
        $ciphertext = substr($raw, $ivlen + $taglen);
        
        $plaintext = openssl_decrypt($ciphertext, $cipher, $key, OPENSSL_RAW_DATA, $iv, $tag);
        return $plaintext !== false ? $plaintext : '';
    }
}
