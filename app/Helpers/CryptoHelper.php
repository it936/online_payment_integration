<?php

namespace App\Helpers;
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of CryptoHelper
 *
 * @author sp
 */
class CryptoHelper {
    //put your code here
     /**
     * Encrypts plain text using the AES-128-CBC algorithm.
     *
     * @param string $plainText The text to encrypt.
     * @param string $key       The working key provided by CCAvenue.
     * @return string           The encrypted text in hexadecimal format.
     */
    public static function encrypt(string $plainText, string $key): string
    {
        $key = self::hexToBin(md5($key));
        $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
        $encryptedData = openssl_encrypt($plainText, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $initVector);
        return bin2hex($encryptedData);
    }

    /**
     * Decrypts encrypted text using the AES-128-CBC algorithm.
     *
     * @param string $encryptedText The text to decrypt in hexadecimal format.
     * @param string $key           The working key provided by CCAvenue.
     * @return string               The decrypted plain text.
     */
    public static function decrypt(string $encryptedText, string $key): string
    {
        $key = self::hexToBin(md5($key));
        $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
        $encryptedText = self::hexToBin($encryptedText);
        return openssl_decrypt($encryptedText, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $initVector);
    }

    /**
     * Converts a hexadecimal string to a binary string.
     *
     * @param string $hexString The hexadecimal string.
     * @return string           The binary string.
     */
    private static function hexToBin(string $hexString): string
    {
         $hexString = preg_replace('/[^a-fA-F0-9]/', '', $hexString); // remove non-hex
    return pack("H*", $hexString);
    }
    private static function hexToBin123(string $hexString): string
    {
        $length = strlen($hexString);
        $binString = '';
        for ($i = 0; $i < $length; $i += 2) {
            $binString .= pack("H*", substr($hexString, $i, 2));
        }
        return $binString;
    }
}
