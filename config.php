<?php

    const LIMIT_SITE = 1000000;

    $PROTOCOL = 'https';

    if ($_SERVER['SERVER_NAME'] == 'ibox.leonidasamorim.com.br') $PROTOCOL = 'http';



    echo $_SERVER['SERVER_NAME'];
    echo $PROTOCOL;
    exit;
?>
