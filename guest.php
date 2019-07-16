<?php
    include 'pdo.php';
    include 'functions.php';
    echo '<link rel="stylesheet" type="text/css" href="/accounts/css_style_guest.css" />';
    
    echo "  <div id='header'>
                <form id='home' action='index.php' method='get'>                                            
                    <label> GUEST <input class='submit' name='logout' type='submit' value='HOME'></label>
                </form>
            </div>";
    
    if (isset($_POST["guest"]) || isset($_GET["view"]) ) {
        viewAll();
    }

    if (isset($_GET['search'])) {
        search();
    } 

    echo "  <form action='guest.php' method='get'>
                <input class='submit' name='view' type='submit' value='REFRESH'><br>

                <input type='text' name='value' placeholder='Email / Username'>
                <input class='submit' name='search' type='submit' value='SEARCH'>
            </form>

            <p>Enter one email / username at a time</p>    
            </div>" ;

?>