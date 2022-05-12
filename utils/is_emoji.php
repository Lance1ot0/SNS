<?php

function is_emoji($char) {
    preg_match( '/[\x{10000}-\x{1FFFF}]/u', $char, $matches);

    return !empty($matches[0]);
}