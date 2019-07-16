<html>

<head>
    <meta charset="UTF-8">
    <title>Sign-up/Log in</title>
    <link rel="stylesheet" type="text/css" href="css_style.css"/>
</head>

<body>

    <div>
        <form class="login" action="login.php" method="post">
            <input class=login_text type="text" name="email" placeholder="Email address">
            <input class=login_text type="text" name="pwd" placeholder="Password">
            <input id='login_submit' name="login" type="submit" value="LOGIN">
        </form>
    </div>

    <?php
        $query_string = $_SERVER['QUERY_STRING'];    
        if ($query_string == 'login=invalid') {
            echo "<div id='login_error'><p>Account/password invalid!</p></div>";
        } else if ($query_string == 'login=blank') {
            echo "<div id='login_error'><p>Username/password cannot be blank for login</p></div>";
        } else if ($query_string == 'login=success') {
            echo "<div id='login_success'><p>Login successful!</p></div>";
        } else if ($query_string == 'logout=success') {
            echo "<div id='logout_success'><p>Log out successful!</p></div>";
        } else {
            // do nothing
        }
    ?>

    <?php
        $query_string = $_SERVER['QUERY_STRING'];
        if ($query_string == 'session=expired') {
            echo "<div id='session'><p>Session expired</p></div>";
        }
    ?>
    

    <div id="register">
        <form action="register.php" method="post">
            <p>All fields must be filled / selected</p>
            <input type="text" name="firstName" placeholder="First Name">
            <label class='notes'>[A-Z only, no spaces]</label>
            <input type="text" name="lastName" placeholder="Last Name">
            <label class='notes'>[A-Z only, no spaces]</label>
            <div id="select">
                Male: <input class="radio" type="radio" name="sex" value="Male"> 
                Female: <input class="radio" type="radio" name="sex" value="Female">
            </div> 
            <input type="text" name="email" placeholder="Email address">
            <input type="text" name="username" placeholder="Username">
            <label class='notes'>[A-Z, 0-9, underscore, no spaces]</label>
            <input type="text" name="pwd1" placeholder="Password">
            <label class='notes'>[All characters, but no spaces]</label>
            <input type="text" name="pwd2" placeholder="Confirm Password">
            <label class='notes'>[All characters, but no spaces]</label>
            <input id="reg_submit" name="submit" type="submit" value="REGISTER">

            <?php
                $query_string = $_SERVER['QUERY_STRING'];

                if ($query_string == 'signup=email') {
                    echo "<p class='fields'>Email address already exist</p>";
                } else if ($query_string == 'signup=username') {
                    echo "<p class='fields'>Username already exist</p>";
                } else if ($query_string == 'signup=empty') {
                    echo "<p class='fields'>Cannot have blank fields</p>";
                } else if ($query_string == 'signup=gender') {
                    echo "<p class='fields'>Must select a gender</p>";
                } else if ($query_string == 'signup=password') {
                    echo "<p class='fields'>Password does NOT match</p>";
                } else if ($query_string == 'email=invalid') {
                    echo "<p class='fields'>Email address is invalid</p>";
                } else if ($query_string == 'first=invalid') {
                    echo "<p class='fields'>First name is invalid</p>";
                } else if ($query_string == 'last=invalid') {
                    echo "<p class='fields'>Last name is invalid</p>";
                } else if ($query_string == 'username=invalid') {
                    echo "<p class='fields'>Username is invalid</p>";
                } else if ($query_string == 'password=invalid') {
                    echo "<p class='fields'>Password is invalid</p>";
                }else if ($query_string == 'register=success') {
                    echo "<p class='success'>Registration successful</p>";
                } else {
                    // do nothing, query string is null
                }
            ?>
        </form>

        <div id='guest'>
            <p>Guest account only has view/search option</p>
            <form action="guest.php" method="post">
                <input id="guest_submit" name="guest" type="submit" value="CONTINUE AS GUEST">
            </form>
        </div>
    </div>

    <div id='container'>
        <div class='item1'><span class='under'>SCOPE</span> The purpose of this project is to demonstrate the utilization HTML/CSS/PHP/MySQL from front-end to back-end.  This project allows user registration/login and grants permissions based on account types.<br><br>
                            The following core concepts are applied in this project:<br><br>
                            <img src="/accounts/checkmark.png"> Full front-end to back-end concepts via HTML/CSS/PHP/MySQL<br>
                            <img src="/accounts/checkmark.png"> PHP scripting to handle GET/POST requests via forms<br>
                            <img src="/accounts/checkmark.png"> Implement MySQL SELECT/UPDATE/INSERT/DELETE commands to manipulate MySQL database<br>
                            <img src="/accounts/checkmark.png"> Session-based login with timeout<br>
                            <img src="/accounts/checkmark.png"> MySQL prepared statement to prevent SQL injection<br>
                            <img src="/accounts/checkmark.png"> PHP script to provide Guest, User, and Admin permissions to database</div>
        <div>

        <div id='flex_container'>
            <div class='item2'><span class='under'>GUEST ACCOUNT</span><br><br>
                Continuing as a Guest account only has view/search permissions only.<br><br>
                Unable to edit/delete accounts.
            </div>
            <div class='item3'><span class='under'>USER ACCOUNT</span><br><br>
                Login as any of the user accounts to view/search + update permission for your own username.<br><br>

                - Register your own account and login to edit username<br>
                <span style="margin-left:40%"></span> -OR- <br>
                - Login with any of the first four accounts (example below) after 'admin'<br><br>

                Email: thanos@marvel.com<br>
                Password: 123<br>
            </div>
            <div class='item4'><span class='under'>ADMIN ACCOUNT</span><br><br>
                Login as Admin to view/search/edit/delete user accounts from database.<br><br> 
                
                Email: admin@marvel.com<br>
                Password: 123<br><br>

                <i><span class='under'>NOTE</span> Admin is still unable to edit/delete first 5 accounts listed</i>
            </div>
        </div>
    </div>

</body>

</html>