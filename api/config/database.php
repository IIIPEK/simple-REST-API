<?php
/**
 * Database Class
 * 
 */
class Database {
    // database and account
    /**
    * @var string $host Host
    * @var string $db_name Name of database on server
    * @var string $user Username with access privilegies
    * @var string $pass Password for user
    * @var PDO $conn Connection object
    */
    private $host="localhost";
    private $db_name="api_db";
    private $user="api_user";
    private string $pass="@qxgriJQ8Lj*Rrc";
    public $conn;

    // connection to DB
    /**
     * @api getConnection 
     * 
     * Try connect to Database
     * 
     * @return $this->conn property 
     */

    public function getConnection() {
        $this->conn=null;

        try {
            $this->conn= new PDO("mysql:host=".$this->host.";dbname=".$this->db_name,$this->user,$this->pass);
            $this->conn->exec("set names utf8");
        }
        catch(PDOException $exception){
            echo "Connection error: ".$exception->getMessage();
        }
        return $this->conn;

    }

}

?>