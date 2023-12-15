<?php
use Illuminate\Support\Str;
//encode 
if (!function_exists('encode_string')) {
    function encode_string($id)
    {
        $encodedId = rtrim(strtr(base64_encode($id), '+/', '-_'), '=');
        $shuffle = Str::random(10); // Generate a 10-character random string.
        $backShuffle = Str::random(15); // Generate a 15-character random string.
        $enc_string = $shuffle . $encodedId . $backShuffle;
        return $enc_string;
    }
}

//decode 
if (!function_exists('decode_string')) {
    function decode_string($id)
    {
        $encodedIdWithPadding = substr($id, 10, -15);
        $paddedEncodedId = $encodedIdWithPadding . str_repeat('=', 4 - strlen($encodedIdWithPadding) % 4);
        $encodedId = strtr($paddedEncodedId, '-_', '+/');
        $originalId = base64_decode($encodedId);
        return $originalId;
    }
}