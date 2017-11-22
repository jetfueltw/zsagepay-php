<?php

namespace Jetfuel\Zsagepay;

class Signature
{
    /**
     * Generate signature.
     *
     * @param array $payload
     * @param string $secretKey
     * @return string
     */
    public static function generate(array $payload, $secretKey)
    {
        $baseString = self::buildBaseString($payload).'&KEY='.$secretKey;

        return self::md5Hash($baseString);
    }

    /**
     * @param array $payload
     * @param string $secretKey
     * @param string $signature
     * @return bool
     */
    public static function validate(array $payload, $secretKey, $signature)
    {
        return self::generate($payload, $secretKey) === $signature;
    }

    private static function buildBaseString(array $payload)
    {
        ksort($payload);

        $baseString = '';
        foreach ($payload as $key => $value) {
            $baseString .= $key.'='.$value.'&';
        }

        return rtrim($baseString, '&');
    }

    private static function md5Hash($data)
    {
        return strtoupper(md5($data));
    }
}
