<?php

function utf8_str_pad($input, $length, $padStr = ' ', $type = STR_PAD_RIGHT) {

    $inputLen = utf8_strlen($input);
    if ($length <= $inputLen) {
        return $input;
    }

    $padStrLen = utf8_strlen($padStr);
    $padLen = $length - $inputLen;

    if ($type == STR_PAD_RIGHT) {
        $repeatTimes = ceil($padLen / $padStrLen);
        return utf8_substr($input . str_repeat($padStr, $repeatTimes), 0, $length);
    }

    if ($type == STR_PAD_LEFT) {
        $repeatTimes = ceil($padLen / $padStrLen);
        return utf8_substr(str_repeat($padStr, $repeatTimes), 0, floor($padLen)) . $input;
    }

    if ($type == STR_PAD_BOTH) {

        $padLen/= 2;
        $padAmountLeft = floor($padLen);
        $padAmountRight = ceil($padLen);
        $repeatTimesLeft = ceil($padAmountLeft / $padStrLen);
        $repeatTimesRight = ceil($padAmountRight / $padStrLen);

        $paddingLeft = utf8_substr(str_repeat($padStr, $repeatTimesLeft), 0, $padAmountLeft);
        $paddingRight = utf8_substr(str_repeat($padStr, $repeatTimesRight), 0, $padAmountLeft);
        return $paddingLeft . $input . $paddingRight;
    }

    trigger_error('utf8_str_pad: Unknown padding type (' . $type . ')',E_USER_ERROR);
}
