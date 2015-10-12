<?php

chdir(dirname(__DIR__));

if (file_exists('vendor/autoload.php')) {
    require_once 'vendor/autoload.php';
}

require_once 'src/main.php';
