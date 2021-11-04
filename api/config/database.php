<?php
/**
 * Database Class
 * 
 */
class Database {
    // database and account
    /**
    * @var string $host Host
    */
    private $host="localhost";

    /**
     *  @var string $db_name Name of database on server
     */
    private $db_name="api_db";
    /**
     * @var string $user Username with access privilegies
     */
    private $user="api_user";
    /**
     * @var string $pass Password for user 
     */
    private string $pass="@qxgriJQ8Lj*Rrc";
    /**
     * @var PDO object $conn Connection object
     */
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