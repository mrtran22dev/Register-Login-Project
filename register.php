<?php

    include 'pdo.php';
    
        if (isset($_POST["submit"])) {
            $pdo = new Dbh();
            $link = $pdo->pdoConnect();
            
            $first = strtolower($_POST["firstName"]);                                                   // make all lower-case
            $last = strtolower($_POST["lastName"]);                                                     // make all lower-case
            $email = strtolower($_POST["email"]);                                                       // make all lower-case
            $username = strtolower($_POST['username']);
            $sex = $_POST["sex"];
            $pwd1 = $_POST["pwd1"];
            $pwd2 = $_POST["pwd2"];
            $random = rand(0,9999999);


            //$stmt = $link->query("SELECT * FROM accounts where email='$email';");                 // non-prepared statement
            $stmt = $link->prepare("SELECT * FROM accounts where email=:email");
            $stmt->execute(array('email'=>$email));
            $email_count = $stmt->rowCount();
            $stmt = $link->prepare("SELECT * FROM accounts where userName=:userName");
            $stmt->execute(array('userName'=>$username));
            $user_count = $stmt->rowCount();

            // echo "Email count: $email_count<br>";                                                // for check/debug
            // echo "User count: $user_count<br>";
            // echo var_dump(isset($sex));
            // echo var_dump(empty($sex));

            if ($email_count > 0) {                                                                 // email already exist
                header("Location: /accounts/index.php?signup=email");                             
                exit();                                                                             // stop executing rest of script
            } else if ($user_count > 0) {                                                           // username already exist
                header("Location: /accounts/index.php?signup=username");
                exit();
            } else if ($first == null || $last == null || $email == null || $pwd1 == null || $pwd2 == null || $username == null) {          // Empty fields
                header("Location: /accounts/index.php?signup=empty");
                exit();
            } else if (empty($sex)) {                                                               // sex not selected
                header("Location: /accounts/index.php?signup=gender");
                exit();
            } else if ($pwd1 != $pwd2) {                                                            // password mismatch
                header("Location: /accounts/index.php?signup=password");
                exit();
            } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {                                // email invalid
                header("Location: /accounts/index.php?email=invalid");
                exit();
            } else if (!ctype_alpha($first)) {                                                      // first name invalid
                header("Location: /accounts/index.php?first=invalid");
                exit();
            } else if (!ctype_alpha($last)) {                                                       // last name invalid
                header("Location: /accounts/index.php?last=invalid");
                exit();
            } else if (preg_match('/[^a-zA-Z0-9_]/', $username) == 1) {                             // username, no spaces/special characters
                //echo var_dump(preg_match('/[^a-zA-Z0-9_]/', $newFieldsArray[4]));
                header("Location: /accounts/index.php?username=invalid");
                exit();
            } else if (strpos($pwd1, " ") !== false) {                                              // password, no spaces. '!=' will not work, need to use '!=='
                header("Location: /accounts/index.php?password=invalid");
                exit();
            } else if (strpos($pwd2, " ") !== false) {                                              // password, no spaces. '!=' will not work, need to use '!=='
                header("Location: /accounts/index.php?password=invalid");
                exit();
            } else {
                // nothing, all valid
            }

            // Catch duplicate account #, generate new acct # if needed
            $stmt = $link->query("SELECT * FROM accounts where acctNum='$random';");                // non-prepare statement
            $count = $stmt->rowCount();
            while ($count > 0) {
                $random = rand(0,9999999);
                $stmt = $link->query("SELECT * FROM accounts where acctNum='$random';");            // non-prepare statement
                $count = $stmt->rowCount();
            }

            // *********** HASH password ***************** //

            // INSERT METHOD 1:  Insert data to MySQL DB prepared statement + 'named placeholder' (arrays)
            $data = array('first'=>$first, 'last'=>$last, 'email'=>$email, 'username'=>$username, 'pwd'=>$pwd1, 'sex'=>$sex, 'acctNum'=>$random, 'comments'=>'N/A');
            $stmt = $link->prepare("INSERT INTO accounts (first, last, email, userName, pwd, sex, acctNum, comments) VALUES (:first, :last, :email, :username, :pwd, :sex, :acctNum, :comments);");                        
            $stmt->execute($data);
            //echo "Success!<br>";
            header("Location: /accounts/index.php?register=success");                                   // return back to register page w/ 'registration success' message

            echo "SELECT * FROM accounts where email='$email'";
            //$stmt = $link->query("SELECT * FROM accounts where email='$email'");                      // non-prepared statement
            $stmt = $link->prepare("SELECT * FROM accounts where email=:email");
            $stmt->execute(array('email'=>$email));
            while($row = $stmt->fetch()) {
                $uid = "<br>UID: ".$row[0]." | First: ".$row[1]." | Last: ".$row[2]." | Email: ".$row[3]." | Username: ".$row[4]." | Sex: ".$row[6]." | Account: ".$row[7]." | Comments: ".$row[8];
                echo $uid;
            }
            //echo "Account created succesfully!";
        } else {
            echo 'Connection failed: ' . $e->getMessage();
        }

?>