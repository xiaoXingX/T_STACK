<?php

/**
 * 加密解密函数  
 * @Author Allen_Zhao
 * @date  2018-03-05
 */
function encryptDecrypt($key, $string, $decrypt)
{
    if ($decrypt) {
        $decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($string), MCRYPT_MODE_CBC, md5(md5($key))), "12");
        return $decrypted;
    } else {
        $encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5(md5($key))));
        return $encrypted;
    }
}

//以下是将字符串“Hello World!欢迎您”分别加密和解密
//加密：
echo encryptDecrypt('password', 'Hello World!欢迎您', 0);
echo "<br/>";
//解密：
echo encryptDecrypt('password', 'lDNDzxqNGDD/aZLJ4ldKWMw6S4lWRsu6DA0wzoqlQh0=', 1);
