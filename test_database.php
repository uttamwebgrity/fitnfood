<?php
$host = "localhost";
$user  = "root";
$password =  "";
$database1 = "tabjoy_database";
$database2 = "nextstepasia";
 
try {
    $dbh1 = new PDO("mysql:host=$host;dbname=$database1", $user, $password);
} catch(PDOException $e) {
    die('Could not connect to the database:' . $e);
}
 
try {
    $dbh2 = new PDO("mysql:host=$host;dbname=$database2", $user, $password);
} catch(PDOException $e) {
    die('Could not connect to the database:' . $e);
}
?>