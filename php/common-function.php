<?php

/**
 * 常用函数
 * @Author ALLEN_ZHAO
 */

/**
 * 加密、解密函数
 *
 * @param string $string        	
 * @param string $operation
 *        	[DECODE、ENCODE]
 * @param string $key        	
 * @return string
 */
function authcode($string, $operation = 'DECODE', $key = '')
{
    $expiry = 0;
    $ckey_length = 5; // 随机密钥长度 取值 0-32;
    // 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。
    // 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方
    // 当此值为 0 时，则不产生随机密钥

    $key = md5($key ? $key : AUTHCODE_KEY);
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), - $ckey_length)) : '';

    $cryptkey = $keya . md5($keya . $keyc);
    $key_length = strlen($cryptkey);

    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
    $string_length = strlen($string);

    $result = '';
    $box = range(0, 255);

    $rndkey = array();
    for ($i = 0; $i <= 255; $i ++) {
        $rndkey [$i] = ord($cryptkey [$i % $key_length]);
    }

    for ($j = $i = 0; $i < 256; $i ++) {
        $j = ($j + $box [$i] + $rndkey [$i]) % 256;
        $tmp = $box [$i];
        $box [$i] = $box [$j];
        $box [$j] = $tmp;
    }

    for ($a = $j = $i = 0; $i < $string_length; $i ++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box [$a]) % 256;
        $tmp = $box [$a];
        $box [$a] = $box [$j];
        $box [$j] = $tmp;
        $result .= chr(ord($string [$i]) ^ ($box [($box [$a] + $box [$j]) % 256]));
    }

    if ($operation == 'DECODE') {
        if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16)
                == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        return $keyc . str_replace('=', '', base64_encode($result));
    }
}

/**
 * 加密字符串
 * @param $str
 * @param $key
 * @return string
 */
function enstrhex($str, $key)
{
    /* 开启加密算法/ */
    $td = mcrypt_module_open('twofish', '', 'ecb', '');
    /* 建立 IV，并检测 key 的长度 */
    $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
    $ks = mcrypt_enc_get_key_size($td);
    /* 生成 key */
    $keystr = substr(md5($key), 0, $ks);
    /* 初始化加密程序 */
    mcrypt_generic_init($td, $keystr, $iv);
    /* 加密, $encrypted 保存的是已经加密后的数据 */
    $encrypted = mcrypt_generic($td, $str);
    /* 检测解密句柄，并关闭模块 */
    mcrypt_module_close($td);
    /* 转化为16进制 */
    $hexdata = bin2hex($encrypted);
    //返回
    return $hexdata;
}

/**
 * 解密字符串
 * @param $str
 * @param $key
 * @return string
 */
function destrhex($str, $key)
{
    /* 开启加密算法/ */
    $td = mcrypt_module_open('twofish', '', 'ecb', '');
    /* 建立 IV，并检测 key 的长度 */
    $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
    $ks = mcrypt_enc_get_key_size($td);
    /* 生成 key */
    $keystr = substr(md5($key), 0, $ks);
    /* 初始化加密模块，用以解密 */
    mcrypt_generic_init($td, $keystr, $iv);
    /* 解密 */
    $encrypted = pack("H*", $str);
    $decrypted = mdecrypt_generic($td, $encrypted);
    /* 检测解密句柄，并关闭模块 */
    mcrypt_generic_deinit($td);
    mcrypt_module_close($td);
    /* 返回原始字符串 */
    return $decrypted;
}

/**
 * 获取手机号码归属地信息
 * @param string $tel
 */
function get_phone_info($tel)
{
    $url = 'http://sj.apidata.cn/?mobile=' . $tel;
    //初始化
    $curl = curl_init();
    //设置抓取的url
    curl_setopt($curl, CURLOPT_URL, $url);
    //设置头文件的信息作为数据流输出
    curl_setopt($curl, CURLOPT_HTTPHEADER, 1);
    //设置获取的信息以文件流的形式返回，而不是直接输出。
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    //执行命令
    $mesg = curl_exec($curl);
    //关闭URL请求
    curl_close($curl);
    //显示获得的数据
    $mesg = json_decode($mesg, true);
    return $mesg['data'];
}

/* 强建路径 */

function path_exists($path)
{
    $pathinfo = pathinfo($path . '/tmp.txt');
    if (!empty($pathinfo ['dirname'])) {
        if (file_exists($pathinfo ['dirname']) === false) {
            if (mkdir($pathinfo ['dirname'], 0777, true) === false) {
                $log = array();
                $log ['message'] = $path;
                $log ['key'] = 2000001;
                cls_log::save($log);
                return false;
            }
        }
    }
    return $path;
}

//随机生成字符串
function createNonceStr($length = 16)
{
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
        $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
}

/**
 * 将一个二维数组转换为 hashmap
 *
 * 如果省略 $val 参数，则转换结果每一项为包含该项所有数据的数组。
 *
 * @param array $arr            
 * @param string $keyField            
 * @param string $val            
 *
 * @return array
 */
function _arrHash($arr, $keyField, $val = null)
{
    $ret = array();
    if ($val) {
        foreach ($arr as $row) {
            $ret[$row[$keyField]][] = $row[$val];
        }
    } else {
        foreach ($arr as $row) {
            $ret[$row[$keyField]]['date'] = $row[$keyField];
            $ret[$row[$keyField]]['list'][] = $row;
        }
    }
    return $ret;
}
