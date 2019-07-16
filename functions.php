<?php
    
    class HeaderString {

        public $logout;
        public $home;

        public function str($session) {
            
            if (isset($_SESSION['username'])) {
                $this->logout = " <div id='header'>
                        <form id='home' action='index.php' method='get'>                                            
                            <label>".$_SESSION['username']."  <input class='logout' name='logout' type='submit' value='LOGOUT'></label>
                        </form>
                        </div>";

                $this->home = "  <div id='header'>
                        <form id='home' action='index.php' method='get'>                                            
                            <label><input class='logout' name='logout' type='submit' value='Home'></label>
                        </form>
                        </div>";
            } else {
                $this->home = "  <div id='header'>
                        <form id='home' action='index.php' method='get'>                                            
                            <label><input class='logout' name='logout' type='submit' value='Home'></label>
                        </form>
                        </div>";
            }             
        }
   
    }       
    
    // ********************************************************************************************************* //
    class Session {
        public $email;
        public $username;

        public function startSession() {
            //session_set_cookie_params(10);
            session_start();
            $pdo = new Dbh();
            $link = $pdo->pdoConnect();

            $_SESSION['pwd'] = $_POST["pwd"];                                                   // grab user entered password
            $_SESSION['email'] = strtolower($_POST["email"]);                                   // grab user entered email
            $this->email = $_SESSION['email'];
            //echo "session: $email<br>";  
            $_SESSION['time_current'] = time();                                                 // returns time as Int, UNIX time
            $_SESSION['time_timeout'] = $_SESSION['time_current'] + 600;                        // +15 seconds, set session expiration time

            //$stmt = $link->query("SELECT * FROM accounts where email='$email';");             // non-prepared statement
            $stmt = $link->prepare("SELECT * FROM accounts where email=:email");                // use prepared statement
            $stmt->execute(array('email'=>$_SESSION['email']));
            $count = $stmt->rowCount();
            $row = $stmt->fetch();

            if ($_SESSION['pwd'] == null || $_SESSION['email'] == null) {
                echo "<script> location.href='/accounts/index.php?login=blank' </script>";
            } else if ($count == 0 || $_SESSION['pwd'] != $row['pwd']) {                               // email Not found / password mismatch
                echo "<script> location.href='/accounts/index.php?login=invalid' </script>";
            } else {                                                                                    // found email & password match
                $_SESSION['username'] = $row['userName'];
                $this->username = $_SESSION['username'];
                if ($_SESSION['username'] == 'admin') {
                    echo "<script> location.href='/accounts/admin.php' </script>";                      // trigger admin.php script
                }
            }
        }
    }


    // ********************************************************************************************************* //
    

    function viewAll() { 
        $pdo = new Dbh();
        $link = $pdo->pdoConnect();

        echo "<div id='results'>";
        echo "<table id='data'> <tr><th>UID</th> <th>First</th> <th>Last</th> <th>Email</th> <th>Username</th> <th>Sex</th> <th>Account </th> <th>Comments</th> </tr>";            
        $stmt = $link->prepare("SELECT * FROM accounts");
        $stmt->execute();
        echo "<p id='results_txt' >All records:</p>";
        while($row = $stmt->fetch()) {
            echo "<tr><td>".$row[0]."</td><td>".$row[1]."</td><td>".$row[2]."</td><td>".$row[3]."</td><td>".$row[4]."</td><td>".$row[6]."</td><td>".$row[7]."</td><td>".$row[8]."</td></tr>";
        }
        echo "</table>";

        $queryStr = $_SERVER['QUERY_STRING'];
                if ($queryStr == 'username=invalid') {
                    echo "<p class='update_msg'>Only characters a-Z, 0-9, and underscore allowed for username</p>";
                } else if ($queryStr == 'user_password=mismatch') {
                    echo "<p class='update_msg'>Old username or password incorrect</p>";
                } else if ($queryStr == 'username=exist') {
                    echo "<p class='update_msg'>Username already exists</p>";
                } else if ($queryStr == 'update=success') {
                    echo "<p class='update_success'>Username updated successfully</p>";
                } else {
                    // nothing, for no query string
                }
    }

    // ********************************************************************************************************* //


    function adminViewAll() { 
        $pdo = new Dbh();
        $link = $pdo->pdoConnect();

        echo "<div id='results'>";
        echo "<form action='' method='GET'>";
        echo "<table id='data'> <tr><th></th> <th>UID</th> <th>First</th> <th>Last</th> <th>Email</th> <th>Username</th> <th>Password</th> <th>Sex</th> <th>Account</th> <th>Comments</th> <th>Created</th> <th>Updated</th> </tr>";          
        $stmt = $link->prepare("SELECT * FROM accounts");
        $stmt->execute();
        echo "<p id='results_txt' >All records:</p>";
        $box_count = 0;
        while($row = $stmt->fetch()) {        
            if ($box_count < 5) {
                echo " <tr><td></td><td>".$row[0]."</td><td>".$row[1]."</td><td>".$row[2]."</td><td>".$row[3]."</td><td>".$row[4]."</td><td>".$row[5]."</td><td>".$row[6]."</td><td>".$row[7]."</td><td>".$row[8]."</td><td>".$row[9]."</td><td>".$row[10]."</td></tr>";
            } else {
                echo " <tr><td><input type='radio' name='row' value='$box_count'> </td><td>".$row[0]."</td><td>".$row[1]."</td><td>".$row[2]."</td><td>".$row[3]."</td><td>".$row[4]."</td><td>".$row[5]."</td><td>".$row[6]."</td><td>".$row[7]."</td><td>".$row[8]."</td><td>".$row[9]."</td><td>".$row[10]."</td></tr>";        
            }
            $box_count = $box_count+1;
        }
        echo "</table>";

        if (empty($_GET['row'])) {
            //header("Location: /accounts/admin.php?row=null");
        } else {
            //$_SESSION['row'] = $_GET['row'];
            //echo "session row: ".$_SESSION['row']."<br>";
            return $_GET['row'];
        }
    }    

    // *************************************************************************************************************** //

    function adminDelete($row) {

        $pdo = new Dbh();
        $link = $pdo->pdoConnect();

        $stmt = $link->query("SELECT * from accounts LIMIT $row, 1");
        $row = $stmt->fetch();  
        $uid = $row["uid"];      
        $currentUsername = $row["userName"];
        $currentEmail = $row["email"];
        echo "<div id='delete'><form action='' method='POST'>";                             //
        echo "<p>Are you sure you want to delete record: $uid</p>";
        echo "<input type='submit' name='yes' value='YES'>  ";
        echo "<input type='submit' name='no' value='NO'>  </form></div>";

        if (isset($_POST['yes'])) {
            $data = array('email'=>$currentEmail);
            $stmt = $link->prepare("DELETE FROM accounts WHERE email=:email");
            $stmt->execute($data);
            echo "<script> location.href='/accounts/admin.php?delete=success' </script>";
        } else if (isset($_POST['no'])) {
            echo "<script> location.href='/accounts/admin.php?delete=cancel' </script>";
        }
    }        

    // ******************************************************************************************************************* //

    function adminEdit($row) {

        if (!empty($row)) {

            $pdo = new Dbh();
            $link = $pdo->pdoConnect();

            $stmt = $link->query("SELECT * from accounts LIMIT $row, 1");
            $row = $stmt->fetch();
            $uid = $row["uid"];
            $currentFirst = $row["first"];
            $currentLast = $row["last"];
            $currentPwd = $row["pwd"];
            $currentUsername = $row["userName"];
            $currentEmail = $row["email"];
            $currentSex = $row["sex"];
            $currentFieldsArray = array($currentFirst, $currentLast, $currentEmail, $currentPwd, $currentUsername, $currentSex);

            echo "<form id='edit' action='' method='POST'>
                        <p>You are updating record: $uid</p>
                        <p>Enter new value in fields to update.  Blank fields or invalid entries will retain old values</p>
                        <input type='text' name='first' placeholder='New first name'><br>
                        <label class='notes'>[A-Z only, no spaces]</label>
                        <input type='text' name='last' placeholder='New last name'><br>
                        <label class='notes'>[A-Z only, no spaces]</label>
                        <input type='text' name='email' placeholder='New email'><br>
                        <input type='text' name='newUser' placeholder='New username'><br>
                        <label class='notes'>[A-Z, 0-9, underscore, no spaces]</label>
                        Male: <input class='radio' type='radio' name='sex' value='Male'>
                        Female: <input class='radio' type='radio' name='sex' value='Female'><br>
                        <input type='text' name='password' placeholder='New password'><br>
                        <input class='submit' name='update' type='submit' value='UPDATE'>
                    </form></div>";


            if (isset($_POST['update'])) {
                $newFirst = $_POST['first'];
                $newLast = $_POST['last'];
                $newEmail = $_POST['email'];
                $newPassword = $_POST['password'];
                $newUsername = $_POST['newUser'];
                if (!isset($_POST['sex'])) {
                    $newSex = $currentSex;
                } else {
                    $newSex = $_POST["sex"];
                }
                $newFieldsArray = array($newFirst, $newLast, $newEmail, $newPassword, $newUsername, $newSex);
            
                $stmt = $link->query("SELECT * from accounts WHERE email='$newEmail' OR userName='$newUsername'");
                $count = $stmt->rowCount();
                if ($newFieldsArray[0] == null || $newFieldsArray[1] == null || $newFieldsArray[2] == null || $newFieldsArray[3] == null || $newFieldsArray[4] == null || $newFieldsArray[5] == null) {                                    
                    for ($i=0; $i<sizeof($newFieldsArray); $i++) {
                        if ($newFieldsArray[$i] == null) {
                            $newFieldsArray[$i] = $currentFieldsArray[$i];
                        } else {
                            // do nothing, valid entry
                        }
                    }
                } 

                if (!filter_var($newFieldsArray[2], FILTER_VALIDATE_EMAIL)) {                       // email
                    $newFieldsArray[2] = strtolower($currentFieldsArray[2]);
                    $param = 'email';
                } else if (!ctype_alpha($newFieldsArray[0])) {                                     // first name
                    $newFieldsArray[0] = strtolower($currentFieldsArray[0]);
                    $param = 'first';
                } else if (!ctype_alpha($newFieldsArray[1])) {                                     // last name
                    $newFieldsArray[1] = strtolower($currentFieldsArray[1]);
                    $param = 'last';
                } else if (preg_match('/[^a-zA-Z0-9_]/', $newFieldsArray[4]) == 1) {               // username, no spaces/special characters
                    //echo var_dump(preg_match('/[^a-zA-Z0-9_]/', $newFieldsArray[4]));
                    $newFieldsArray[4] = $currentFieldsArray[4];
                    $param = 'username';
                } else if (strpos($newFieldsArray[3], " ") !== false) {                            // password, no spaces. '!=' will not work, need to use '!=='
                    $newFieldsArray[3] = $currentFieldsArray[3];
                    $param = 'password';
                } else {
                    $param = null;
                }     
            
                if ($count > 0) {
                    if ($newEmail == $currentEmail && $newUsername == $currentUsername) {
                        // allow changes, its the same account
                        echo "<script> location.href='/accounts/admin.php?update=success' </script>";
                    } else {
                        // do NOT allow changes, already an existing account somewhere
                        echo "<script> location.href='/accounts/admin.php?email=exist' </script>";
                    }
                } else if ($count < 1) {
                    // allow changes
                    $data = array('first'=>$newFieldsArray[0], 'last'=>$newFieldsArray[1], 'email'=>$newFieldsArray[2], 'sex'=>$newFieldsArray[5], 'pwd'=>$newFieldsArray[3], 'newUser'=>$newFieldsArray[4], 'currentEmail'=>$currentFieldsArray[2]);
                    $stmt = $link->prepare("UPDATE accounts SET first=:first, last=:last, email=:email, sex=:sex, pwd=:pwd, userName=:newUser WHERE email=:currentEmail");
                    $stmt->execute($data);
                    if ($param == null) {
                        //header("Location: /accounts/admin.php?update=success");                                               // *** header() will cause error as oppose to redirecting to page w/ <script>
                        echo "<script> location.href='/accounts/admin.php?update=success' </script>";
                    } else {
                        //header("Location: /accounts/admin.php?update=success&$param=invalid");
                        echo "<script> location.href='/accounts/admin.php?update=success&$param=invalid' </script>";
                    }
                    
                } 
            }
        }
    }

    // ******************************************************************************************************** //

    function adminSearch($value) {
        $pdo = new Dbh();
        $link = $pdo->pdoConnect();
        //$value=$_GET['value'];
        $data = array('email'=>$value, 'user'=>$value);
        $stmt = $link->prepare("SELECT * FROM accounts WHERE email=:email OR userName=:user");
        $stmt->execute($data);
        $count = $stmt->rowCount();

        echo "<div id='results'>";
        echo "<form action='' method='GET'>";
        echo "<p>Search Results: </p>";
        echo "<table id='data'> <tr><th></th> <th>UID</th> <th>First</th> <th>Last</th> <th>Email</th> <th>Username</th> <th>Passowrd</th> <th>Sex</th> <th>Account </th> <th>Comments</th> <th>Created</th> <th>Updated</th> </tr>";
            
        if ($value == null) {                                                                           // BLANK field, no search value entered
            //$stmt = $stmt = $link->query("SELECT * FROM accounts"); 
            $stmt = $link->query("SELECT * FROM accounts"); 
            $box_count = 0;
            while($row = $stmt->fetch()) {        
                if ($box_count < 5) {
                    echo " <tr><td></td><td>".$row[0]."</td><td>".$row[1]."</td><td>".$row[2]."</td><td>".$row[3]."</td><td>".$row[4]."</td><td>".$row[5]."</td><td>".$row[6]."</td><td>".$row[7]."</td><td>".$row[8]."</td><td>".$row[9]."</td><td>".$row[10]."</td></tr>";
                } else {
                    echo " <tr><td><input type='radio' name='row' value='$box_count'> </td><td>".$row[0]."</td><td>".$row[1]."</td><td>".$row[2]."</td><td>".$row[3]."</td><td>".$row[4]."</td><td>".$row[5]."</td><td>".$row[6]."</td><td>".$row[7]."</td><td>".$row[8]."</td><td>".$row[9]."</td><td>".$row[10]."</td></tr>";        
                }
                $box_count = $box_count+1;
            }         
            echo "</table><p class='search_msg'>No results found.  Search field is blank</p>";
        } else if ($count < 1) {                                                                        // no search value found
            //$stmt = $stmt = $link->query("SELECT * FROM accounts"); 
            $stmt = $link->query("SELECT * FROM accounts"); 
            $box_count = 0;
            while($row = $stmt->fetch()) {    
                if ($box_count < 5) {
                    echo " <tr><td></td><td>".$row[0]."</td><td>".$row[1]."</td><td>".$row[2]."</td><td>".$row[3]."</td><td>".$row[4]."</td><td>".$row[5]."</td><td>".$row[6]."</td><td>".$row[7]."</td><td>".$row[8]."</td><td>".$row[9]."</td><td>".$row[10]."</td></tr>";
                } else {
                    echo " <tr><td><input type='radio' name='row' value='$box_count'> </td><td>".$row[0]."</td><td>".$row[1]."</td><td>".$row[2]."</td><td>".$row[3]."</td><td>".$row[4]."</td><td>".$row[5]."</td><td>".$row[6]."</td><td>".$row[7]."</td><td>".$row[8]."</td><td>".$row[9]."</td><td>".$row[10]."</td></tr>";        
                }
                $box_count = $box_count+1;
            }              
            echo "</table><p class='search_msg'>0 results found. No matches for '$value'</p>";
        } else if ($count > 0) {                                                                        // search value found
            $stmt = $link->prepare("SELECT * FROM accounts");
            $stmt->execute($data);

            $box_count = 0;
            while($row = $stmt->fetch()) {     
                if ($box_count < 5) {
                    //highlight record
                    if ($row[3] == $value || $row[4] == $value) {
                        echo "<tr bgcolor='#FFE37B' style=color:black><td></td><td>".$row[0]."</td><td>".$row[1]."</td><td>".$row[2]."</td><td>".$row[3]."</td><td>".$row[4]."</td><td>".$row[5]."</td><td>".$row[6]."</td><td>".$row[7]."</td><td>".$row[8]."</td><td>".$row[9]."</td><td>".$row[10]."</td></tr>";
                    } else {
                        echo "<tr><td></td><td>".$row[0]."</td><td>".$row[1]."</td><td>".$row[2]."</td><td>".$row[3]."</td><td>".$row[4]."</td><td>".$row[5]."</td><td>".$row[6]."</td><td>".$row[7]."</td><td>".$row[8]."</td><td>".$row[9]."</td><td>".$row[10]."</td></tr>";
                    } 
                } else {
                    //highlight record
                    if ($row[3] == $value || $row[4] == $value) {
                        echo "<tr bgcolor='#FFE37B' style=color:black><td><input type='radio' name='row' value='$box_count'> </td><td>".$row[0]."</td><td>".$row[1]."</td><td>".$row[2]."</td><td>".$row[3]."</td><td>".$row[4]."</td><td>".$row[5]."</td><td>".$row[6]."</td><td>".$row[7]."</td><td>".$row[8]."</td><td>".$row[9]."</td><td>".$row[10]."</td></tr>";
                    } else {
                        echo "<tr><td><input type='radio' name='row' value='$box_count'> </td><td>".$row[0]."</td><td>".$row[1]."</td><td>".$row[2]."</td><td>".$row[3]."</td><td>".$row[4]."</td><td>".$row[5]."</td><td>".$row[6]."</td><td>".$row[7]."</td><td>".$row[8]."</td><td>".$row[9]."</td><td>".$row[10]."</td></tr>";
                    }                     
                }
                $box_count = $box_count+1;
            }
            echo "</table><p class='search_msg'>Result highlighted</p>";
        } else {
            // nothing
        }

    }


    // ******************************************************************************************************** //
    
    function search() { 
        $pdo = new Dbh();
        $link = $pdo->pdoConnect();
        $value=$_GET['value'];
        $data = array('email'=>$value, 'user'=>$value);
        $stmt = $link->prepare("SELECT * FROM accounts WHERE email=:email OR userName=:user");
        $stmt->execute($data);
        $count = $stmt->rowCount();

        echo "<div id='results'>";
        echo "<p>Search Results: </p>";
        echo "<table id='data'> <tr><th>UID</th> <th>First</th> <th>Last</th> <th>Email</th> <th>Username</th> <th>Sex</th> <th>Account </th> <th>Comments</th> </tr>";// </table>";

        if ($value == null) {
            //$stmt = $stmt = $link->query("SELECT * FROM accounts"); 
            $stmt = $link->query("SELECT * FROM accounts"); 
            while($row = $stmt->fetch()) {
                echo " <tr><td>".$row[0]."</td><td>".$row[1]."</td><td>".$row[2]."</td><td>".$row[3]."</td><td>".$row[4]."</td><td>".$row[6]."</td><td>".$row[7]."</td><td>".$row[8]."</td></tr>";
            }           
            echo "</table><p class='search_msg'>No results found.  Search field is blank</p>";
        } else if ($count < 1) {
            //$stmt = $stmt = $link->query("SELECT * FROM accounts"); 
            $stmt = $link->query("SELECT * FROM accounts"); 
            while($row = $stmt->fetch()) {
                echo " <tr><td>".$row[0]."</td><td>".$row[1]."</td><td>".$row[2]."</td><td>".$row[3]."</td><td>".$row[4]."</td><td>".$row[6]."</td><td>".$row[7]."</td><td>".$row[8]."</td></tr>";
            }   
            echo "</table><p class='search_msg'>0 results found. No matches for '$value'</p>";
        } else if ($count > 0) {
            $stmt = $link->prepare("SELECT * FROM accounts");
            $stmt->execute($data);
            while($row = $stmt->fetch()) {
                if ($row[3] == $value || $row[4] == $value) {
                    //highlight matching search record
                    echo "<tr bgcolor='#FFE37B' style=color:black><td>".$row[0]."</td><td>".$row[1]."</td><td>".$row[2]."</td><td>".$row[3]."</td><td>".$row[4]."</td><td>".$row[6]."</td><td>".$row[7]."</td><td>".$row[8]."</td></tr>";
                } else {
                    echo "<tr><td>".$row[0]."</td><td>".$row[1]."</td><td>".$row[2]."</td><td>".$row[3]."</td><td>".$row[4]."</td><td>".$row[6]."</td><td>".$row[7]."</td><td>".$row[8]."</td></tr>";
                } 
            }
            echo "</table><p class='search_msg'>Result highlighted</p>";
        } else {
            // nothing
        }
    }


    function update($password, $old_user, $new_user) {                                          // retrieve all session variables/values
        $pdo = new Dbh();
        $link = $pdo->pdoConnect();

        $email = $_SESSION['email'];
        $username = $_SESSION['$username'];
        $password = $_POST['password'];
        $old_user = $_POST['oldUser'];
        $new_user = $_POST['newUser'];
        $data = array('email'=>$email, 'pwd'=>$password, 'user'=>$old_user);
        $stmt = $link->prepare("SELECT * FROM accounts WHERE email=:email AND pwd=:pwd AND userName=:user");
        $stmt->execute($data);
        $count = $stmt->rowCount();

        if ($count < 1) {                                                                           // check for email + pw + old_username record                                                
            echo "<script> location.href='/accounts/login.php?user_password=mismatch' </script>";
        } else {                                                                                
            $data = array('user'=>$new_user);                                                       
            $stmt = $link->prepare("SELECT * FROM accounts WHERE userName=:user");
            $stmt->execute($data);
            $count = $stmt->rowCount();

            if ($count > 0) {                                                                       // check for existing username
                echo "<script> location.href='/accounts/login.php?username=exist' </script>";
            } else {
                // check is username is valid input - [a-Z,0-9_]
                if (preg_match('/[^a-zA-Z0-9_]/', $new_user) == 1) {                                // username, no spaces/special characters
                    $new_user = $old_user;
                    echo "<script> location.href='/accounts/login.php?username=invalid' </script>";
                } 
                $data = array('email'=>$email, 'pwd'=>$password, 'oldUser'=>$old_user, 'newUser'=>$new_user);
                $stmt = $link->prepare("UPDATE accounts SET userName=:newUser WHERE email=:email AND pwd=:pwd AND userName=:oldUser");
                $stmt->execute($data);
                echo "<script> location.href='/accounts/login.php?update=success' </script>";
            }
        } 
    }
?>