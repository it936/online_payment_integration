<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of CCAvenuePayment
 *
 * @author sp
 */
namespace App\Services;

class CCAvenuePayment {
    public static function encrypt($plainText, $workingKey)
    {
        $secretKey = pack('H*', $workingKey);
        $initVector = pack('H*', '00000000000000000000000000000000');
        return openssl_encrypt($plainText, 'AES-128-CBC', $secretKey, 0, $initVector);
    }

    public static function decrypt($encryptedText, $workingKey)
    {
        $secretKey = pack('H*', $workingKey);
        $initVector = pack('H*', '00000000000000000000000000000000');
        return openssl_decrypt($encryptedText, 'AES-128-CBC', $secretKey, 0, $initVector);
    }
}
