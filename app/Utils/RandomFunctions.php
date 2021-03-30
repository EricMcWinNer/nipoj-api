<?php

namespace App\Utils;

/**
 * Description of RandomFunctions
 *
 * @author Eric McWinNEr
 */
class RandomFunctions {

    //put your code here
    public static function generateRandomString($length = 149, $wallet = false) {
        $permittedCharacters = $wallet ? '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ' : '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_';
        $lengthChars = strlen($permittedCharacters);
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $randomCharacter = $permittedCharacters[mt_rand(0, $lengthChars - 1)];
            $string .= $randomCharacter;
        }
        return $string;
    }

    public static function generateUniqueRandomString($length) {
        $permittedCharacters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_';
        $time = "" . time() . "";
        $string = '';
        $lengthChars = $length - strlen($time);
        for ($i = 0; $i < $lengthChars; $i++) {
            $string .= $permittedCharacters[mt_rand(0, strlen($permittedCharacters) - 1)];
        }
        $delimiter = mt_rand(0, $lengthChars);
        $alpha = substr($string, 0, $delimiter);
        $omega = substr($string, $delimiter);
        return $alpha . $time . $omega;
    }

    public static function parseUrl($url) {
        $r = "^(?:(?P<scheme>\w+)://)?";
        $r .= "(?:(?P<login>\w+):(?P<pass>\w+)@)?";
        $r .= "(?P<host>(?:(?P<subdomain>[\w\.]+)\.)?" . "(?P<domain>\w+\.(?P<extension>\w+)))";
        $r .= "(?::(?P<port>\d+))?";
        $r .= "(?P<path>[\w/]*/(?P<file>\w+(?:\.\w+)?)?)?";
        $r .= "(?:\?(?P<arg>[\w=&]+))?";
        $r .= "(?:#(?P<anchor>\w+))?";
        $r = "!$r!";                                                // Delimiters

        preg_match($r, $url, $out);

        return $out;
    }

    public function stringContains($hayStack, $needle) {
        if (strpos($hayStack, $needle) !== false) {
            return true;
        }
    }

}
