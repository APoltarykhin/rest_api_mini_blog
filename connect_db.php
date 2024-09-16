<?php

$username = 'root';
$password = 'root';
$db_name = 'blog_bd';
$host = 'MySQL-8.0';

$dsm = 'mysql:host='.$host.';dbname='.$db_name.';charset=utf8;';

$database = new PDO($dsm, $username, $password);