<?php
define('DB_HOST', '****'); //host of mysql
define('DB_USER', '****'); //username of the mysql user
define('DB_PASS', '****'); //password for mysql user
define('DB_DATABASE', '****'); //mysql database to connect to

$db = new mysqli ( DB_HOST, DB_USER, DB_PASS, DB_DATABASE );

/* check connection */
if ($mysqli->connect_errno) {
    die('Connect Error: ' . $mysqli->connect_errno);
}
?>
