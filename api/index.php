<?php
//require_once 'objects/api.php';
require_once 'objects/organisation.php';
require_once 'config/database.php';
/**
 * @var Database $database 
 *  
 * creates new object of class Database
 */
$database = new Database();
/**
 * @var Connetion $db
 */
$db = $database->getConnection();
try {
    /**
     * @var Organisation $api
     * 
     *  creates new api object of class Organisatsion
     */
    $api = new Organisation($db);
    echo $api->run();
} catch (Exception $e) {
    echo json_encode(array('error' => $e->getMessage()));
}
