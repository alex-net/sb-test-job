<?php

use M1\Env\Parser;

$env = realpath(__DIR__.'/../.env');
if ($env !== false) {
    $p = new Parser(file_get_contents($env));
    $arr = $p->getContent();
    foreach ($arr as $x => $y) {
        putenv("$x=$y");
    }
}

