<?php
declare(strict_types=1);

function autoload($class) {
    $srcDir = dirname(__FILE__).'/src/';
    $classRelPath = str_replace('\\', '/', $class);
    if(file_exists($srcDir.$classRelPath.'.php')) {
        include_once $srcDir.$classRelPath . '.php';
    }
}

spl_autoload_register('autoload');