<?php

header('http/1.1 500 internal server error');


$fileInc = dirname(__DIR__) . '/error.php';

if (is_file($fileInc)) {

    ob_start();
        require_once $fileInc;
    echo ob_get_clean();
}
