<?php
    class Database {
        
        private $host = "db"; 
        private $dbname = "organic_shop";
        private $username = "root";
        private $password = "123456"; 
        private $port = 3306;
        public $conn;
        public function getConnection(){
            $this->conn = null;
            try{
                // Chuỗi kết nối (DSN)
                $dsn = "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->dbname . ";charset=utf8";
                
                $this->conn = new PDO(
                    $dsn,
                    $this->username,
                    $this->password
                );
                
               
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
            } catch(PDOException $err){
                echo "Lỗi kết nối Docker Database: " . $err->getMessage();
            }
            return $this->conn;
        }
    } 


    $database = new Database();
    $db = $database->getConnection();
?>