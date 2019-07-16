<?php 
    include 'pdo.php';
    include 'functions.php';
    // echo '<link rel="stylesheet" type="text/css" href="/accounts/css_style_admin.css" />';
    
    session_start();
    $email = $_SESSION['email'];
    $username = $_SESSION['username'];
    $timeout = new DateTime();                                                             // convert UNIX time to local time
    $timeout->setTimestamp($_SESSION['time_timeout']);
    $timeout->setTimezone(new DateTimeZone('America/Los_Angeles'));
    $deleteReturn = null;

    if ($username == 'admin' && $email == 'admin@marvel.com') {
        // check session timeout vs. current time
        if (time() >= $_SESSION['time_timeout']) {
            session_unset();
            session_destroy();
            echo "<script> location.href='/accounts/index.php?session=expired' </script>";
        } else if (time() < $_SESSION['time_timeout']) {
            echo '<link rel="stylesheet" type="text/css" href="/accounts/css_style_admin.css" />';          // need to put css line here, otherwise cause intermittent user/admin login error
                                                                                                            // "session_start(): Cannot send session cache limiter - headers already sent ..."
            echo "<div id='header'>
                    <form id='home' action='' method='GET'>                                                                                     
                    <label> $username <input class='logout' name='logout' type='submit' value='LOGOUT'></label>
                    </form>
                  </div>";

            // *************************************************************//

            if (!isset($_GET['search'])) {                                                      // IF-ELSE statement to prevent two tables displayed on same page
                $row = adminViewAll();
                //echo "admin row: ".$row;
                $queryStr = $_SERVER['QUERY_STRING'];
                if ($queryStr == 'delete=success') {
                    echo "<p class=invalid>Record deleted</p>";
                } else if ($queryStr == 'delete=cancel') {
                    echo "<p class=invalid>Delete cancelled</p>";                
                } else if ($queryStr == 'update=success&email=invalid') {
                    echo "<p class=invalid>Email is invalid</p>";
                } else if ($queryStr == 'update=success&username=invalid') {
                    echo "<p class=invalid>Username is invalid</p>";
                } else if ($queryStr == 'update=success&first=invalid') {
                    echo "<p class=invalid>First name is invalid</p>";
                } else if ($queryStr == 'update=success&last=invalid') {
                    echo "<p class=invalid>Last name is invalid</p>";
                } else if ($queryStr == 'update=success&username=invalid') {
                    echo "<p class=invalid>Username is invalid</p>";
                } else if ($queryStr == 'update=success&password=invalid') {
                    echo "<p class=invalid>Password is invalid</p>";
                } else if ($queryStr == 'email=exist') {
                    echo "<p class=invalid>Email / username already exist</p>";
                } else if ($queryStr == 'update=success') {
                    echo "<p class=success>Record updated successfully</p>";
                } else {
                    // return to index.php page in 'else' below for no query string
                }

                echo "<p>Your session will expire at ".$timeout->format('h:i:s')." </p>
                        <form><input type='submit' name='refresh' value='REFRESH'>                      
                        <input type='submit' name='edit' value='EDIT'>
                        <input type='submit' name='delete' value='DELETE'> <br>
                        <input type='text' name='value' placeholder='Email / Username'>
                        <input type='submit' name='search' value='SEARCH'> 
                        </form>";
            } else if (isset($_GET['search'])) {                                          
                adminSearch($_GET['value']);
                echo "<p>Your session will expire at ".$timeout->format('h:i:s')." </p>
                        <form><input type='submit' name='refresh' value='REFRESH'>
                        <input type='submit' name='edit' value='EDIT'>
                        <input type='submit' name='delete' value='DELETE'> <br>
                        <input type='text' name='value' placeholder='Email / Username'>
                        <input type='submit' name='search' value='SEARCH'> 
                        </form></div>";
            }

            if (isset($_GET['edit'])) {
                if ($row != null) {
                    adminEdit($row);
                }
            } else if (isset($_GET['delete'])) {
                $queryStr = $_SERVER['QUERY_STRING'];
                if ($row != null) {
                    $deleteReturn = adminDelete($row);
                } 
            } else if (isset($_GET['logout'])) {
                session_unset();
                session_destroy();
                echo "<script> location.href='/accounts/index.php?logout=success' </script>";
            }
        } 
    } else {
        echo "<script> location.href='/accounts/index.php' </script>";
    }
?>