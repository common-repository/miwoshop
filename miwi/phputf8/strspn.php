<?php
function utf8_strspn($str, $mask, $start = NULL, $length = NULL) {

    $mask = preg_replace('!([\\\\\\-\\]\\[/^])!','\\\${1}',$mask);

	// Fix for $start but no $length argument.
    if ($start !== null && $length === null) {
    	$length = utf8_strlen($str);
    }

    if ( $start !== NULL || $length !== NULL ) {
        $str = utf8_substr($str, $start, $length);
    }

    preg_match('/^['.$mask.']+/u',$str, $matches);

    if ( isset($matches[0]) ) {
        return utf8_strlen($matches[0]);
    }

    return 0;

}