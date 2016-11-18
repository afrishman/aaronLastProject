<?php
    include("CommonMethods.php");

    if (isset($_POST["submit"]))
    {
        // Get advisor ID for checking.
        $advisorID = $_POST["txtAdvID"];

        $debug = false;
        $COMMON = new Common ($debug);

        // Check if the advisor exists in the database.
        $sql = "SELECT COUNT(*) FROM `Advisors` WHERE `a_id` = \"$advisorID\"";
        $rs = $COMMON->executeQuery($sql, $_SERVER["SCRIPT_NAME"]);

        // Store the number of advisors with entered ID.
        $result =  mysql_result($rs, 0);

        // If the advisor is not in the database:
        // Print error in HTML section.
        if ($result == 0)
        {
            echo("");
        }

        // If the advisor is in the database.
        else
        {
            // Needed on every page where variable is transferred from page to page.
            session_start();

            // Store session ID. Needed on every page after.
            $_SESSION['sessionID'] = $_POST["txtAdvID"];

            // Send user to the Advisor Meeting Manager page.
            header('Location: Advisor_Meeting_Manager.php');
        }
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Advisor Login</title>
        <link rel = "stylesheet" type = "text/css" href = "Account_Creation.css">
    </head>

    <body>
        <div id = "box">
            <h1 id = "title">Advisor Login</h1>

            <?php
                // If the submit button has been clicked:
                if (isset($_POST["submit"]))
                {
                    // If the advisor is not in the database:
                    if ($result == 0)
                    {
                        echo("<h2>This user does not exist! Please create an account!</h1>");
                    }
                }
            ?>

            <form method = "post" action = "<?php echo $_SERVER["PHP_SELF"]; ?>">
                <div class = "names">Advisor ID:</div>
                <input type = "text" name = "txtAdvID" maxlength = "7" pattern = "[A-Z]{2}\d{5}" placeholder = "AB12345 (Case Sensitive)" required>
                <br>

                <input type = "submit" name = "submit" value = "submit">
            </form>
            
        </div>
    </body>
</html>