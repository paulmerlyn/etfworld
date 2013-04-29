<?php
define('DB_HOST', 'localhost'); //host of mysql
define('DB_USER', 'paulme6_merlyn'); //username of the mysql user
define('DB_PASS', 'fePhaCj64mkik'); //password for mysql user
define('DB_DATABASE', 'paulme6_nerdwallet'); //mysql database to connect to

$db = new mysqli ( DB_HOST, DB_USER, DB_PASS, DB_DATABASE );

/* check connection */
if ($mysqli->connect_errno) {
    die('Connect Error: ' . $mysqli->connect_errno);
}
?>