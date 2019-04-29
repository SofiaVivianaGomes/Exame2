<?php

function db_connect() {
    
    // dsn - database source name
    $dsn = 'mysql:host=' . DB_SERVER . ';dbname=' . DB_NAME; //standard port of mysql with Ubuntu2 IP

    try {
        $connection = new PDO($dsn, DB_USER, DB_PASS);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  
    } catch (PDOException $e) {
        echo 'Connection failed: ' . $e->getMessage();
    }

    return $connection;

}

function db_disconnect($connection) {
    if (isset($connection)) {
        $connection = null;
    }
}


?>