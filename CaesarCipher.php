<?php
function decryptAgentId($number, $shift) {
    return ($number - $shift + 10000) % 10000;
}
