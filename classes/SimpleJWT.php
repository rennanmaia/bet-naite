<?php
/**
 * Implementação simples de JWT para a aplicação Bet Naite
 * Baseada no padrão RFC 7519
 */
class SimpleJWT {
    
    public static function encode($payload, $key, $algorithm = 'HS256') {
        $header = json_encode(['typ' => 'JWT', 'alg' => $algorithm]);
        $payload = json_encode($payload);
        
        $headerEncoded = self::base64UrlEncode($header);
        $payloadEncoded = self::base64UrlEncode($payload);
        
        $signature = hash_hmac('sha256', $headerEncoded . '.' . $payloadEncoded, $key, true);
        $signatureEncoded = self::base64UrlEncode($signature);
        
        return $headerEncoded . '.' . $payloadEncoded . '.' . $signatureEncoded;
    }
    
    public static function decode($token, $key) {
        $parts = explode('.', $token);
        
        if (count($parts) !== 3) {
            throw new Exception('Token inválido');
        }
        
        list($headerEncoded, $payloadEncoded, $signatureEncoded) = $parts;
        
        $header = json_decode(self::base64UrlDecode($headerEncoded), true);
        $payload = json_decode(self::base64UrlDecode($payloadEncoded), true);
        
        // Verificar assinatura
        $expectedSignature = hash_hmac('sha256', $headerEncoded . '.' . $payloadEncoded, $key, true);
        $actualSignature = self::base64UrlDecode($signatureEncoded);
        
        if (!hash_equals($expectedSignature, $actualSignature)) {
            throw new Exception('Assinatura inválida');
        }
        
        // Verificar expiração
        if (isset($payload['exp']) && time() > $payload['exp']) {
            throw new Exception('Token expirado');
        }
        
        return (object) $payload;
    }
    
    private static function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    
    private static function base64UrlDecode($data) {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
}
?>
