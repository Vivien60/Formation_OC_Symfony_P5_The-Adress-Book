<?php
declare(strict_types=1);

function autoload($class) {
    $srcDir = dirname(__FILE__).'/src/';
    $classRelPath = str_replace('\\', '/', $class);
    require_once dirname(__FILE__).'/src/'.$classRelPath . '.php';
}

spl_autoload_register('autoload');