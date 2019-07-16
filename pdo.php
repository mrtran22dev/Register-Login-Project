<?php
/* Connect to a MySQL database using driver invocation */
// Create PDO instance to connect to database
// public PDO::__construct ( string $dsn [, string $username [, string $passwd [, array $options ]]] )

class Dbh {
    private $user = 'root';                                                  // for running apache web server locally, use this config
    private $password = '';
    private $dbname = 'accounts';
    private $servername = 'localhost';

    // private $user = 'accounts_tran';                                            // for GoDaddy database connection, use this config
    // private $password = '$1234Dec';
    // private $dbname = 'accounts_tran';
    // private $servername = 'localhost';
    //$charset = 'utf8mb4';
    

    public function pdoConnect() {
        try {
            $dsn = "mysql:dbname=".$this->dbname.";host=".$this->servername;
            $dbh = new PDO($dsn, $this->user, $this->password);                 // Data Source Handler
            //echo "MYSQL CONNECT SUCCESS!<br>";
            
            /*
            // INSERT METHOD 1:  Insert data to MySQL DB via 'named placeholder' (arrays)
            $data = array('first'=>'first', 'last'=>'last', 'email'=>'email', 'sex'=>'M', 'acctNum'=>11111, 'tagLine'=>'N/A');
            $stmt = $dbh->prepare("INSERT INTO accounts (first, last, email, sex, acctNum, tagLine) VALUES (:first, :last, :email, :sex, :acctNum, :tagLine);");                        
            $stmt->execute($data);

            // INSERT METHOD 2:  Insert data to MySQL DB via 'positional placeholder' (using '?')
            $stmt = $dbh->prepare("INSERT INTO accounts (first, last, email, sex, acctNum, tagLine) VALUES (?, ?, ?, ?, ?, ?);");                        
            $stmt->execute(['mike', 't', 'gmail', 'M', 12345, 'life is like ... ']);

            $stmt = $dbh->query("SELECT * FROM accounts");
            while($row = $stmt->fetch()) {
                $uid = $row['uid'];
                echo $uid."<br>";
            }
            */

            return $dbh;

        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
    }
}

?>
