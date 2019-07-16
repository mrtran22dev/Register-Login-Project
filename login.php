<?php
    include 'pdo.php';
    include 'functions.php';
    //echo '<link rel="stylesheet" type="text/css" href="/accounts/css_style_login.css" />';

    if (isset($_POST["login"])) {
        $session = new Session();
        $session->startSession();
        $email = $session->email;
        $username = $session->username;
    }

    if (empty(session_id())) {                                                              
        session_start();
    }
    $email = $_SESSION['email'];
    $username = $_SESSION['username'];
    $timeout = new DateTime();                                                             // convert UNIX time to local time
    $timeout->setTimestamp($_SESSION['time_timeout']);
    $timeout->setTimezone(new DateTimeZone('America/Los_Angeles'));
    //echo $timeout->format('U = H:i:s') . "\n";

    if (time() >= $_SESSION['time_timeout']) {
        session_unset();
        session_destroy();
        echo "<script> location.href='/accounts/index.php?session=expired' </script>";
    } else if (time() < $_SESSION['time_timeout']) {
        // load header + stuff
        echo '<link rel="stylesheet" type="text/css" href="/accounts/css_style_login.css" />';                          // need to put css line here, otherwise cause intermittent user/admin login error
                                                                                                                        // "session_start(): Cannot send session cache limiter - headers already sent ..."
        $searchRefreshString = "<p>Your session will expire at ".$timeout->format('h:i:s')." </p>
                                    <form action='login.php' method='get'>
                                        <input class='submit' name='refresh' type='submit' value='REFRESH'> <br>
                                        <input type='text' name='value' placeholder='Email / Username'>
                                        <input class='submit' name='search' type='submit' value='SEARCH'>
                                    </form>

                                <p>Enter one email / username at a time</p>";

        $updateString = "<div id='update'> 
                        <form action='login.php' method='post'>
                            <p>This form below is to change your username</p>
                            <p>You are signed in as: ".$email."</p>
                            <p>Username: ".$username."</p>
                            
                            <input type='text' name='password' placeholder='Password'><br>
                            <input type='text' name='oldUser' placeholder='Old username'>
                            <label class=notes>[A-Z, 0-9, underscore, no spaces]</label>
                            <input type='text' name='newUser' placeholder='New username'>
                            <label class=notes>[A-Z, 0-9, underscore, no spaces]</label>
                            <input class='submit' name='update' type='submit' value='UPDATE'>
                        </form>
                        </div>";

        echo "  <div id='header'>
                <form id='home' action='' method='get'>                                            
                    <label> $username <input class='submit' name='logout' type='submit' value='LOGOUT'></label>
                </form>
                </div>";

        if (isset($_GET['search'])) {
            search();
            echo $searchRefreshString;
            echo $updateString;
        } else if (isset($_GET['refresh'])) {
            viewAll();
            echo $searchRefreshString;
            echo $updateString;
        } else if (isset($_POST['update'])) {                                                   // use POST here, not GET
            update($_POST['password'], $_POST['oldUser'], $_POST['newUser']);
            echo $searchRefreshString;
            echo $updateString;
        } else if (isset($_GET['logout'])) {
            session_unset();
            session_destroy();
            echo "<script> location.href='/accounts/index.php?logout=success' </script>";  
        } else {
            viewAll();
            echo $searchRefreshString;
            echo $updateString;
        }
    }


?>

