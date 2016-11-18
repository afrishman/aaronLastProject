<?php
    include("CommonMethods.php");

    // If the submit button has been clicked, then we execute the rest of the script.
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

        // If the advisor is already in the database:
        // Really just a filler to go to the next one inside of the HTML.
        if ($result == 1)
        {
            echo("");
        }

        // If the advisor is not in the database, create user and send them to dashboard.
        else
        {
            // Needed on every page where variable is transferred from page to page.
            session_start();

            // Store session ID. Needed on every page after.
            $_SESSION['sessionID'] = $_POST["txtAdvID"];

            // Required advisor information.
            $fName = $_POST["txtFName"];
            $lName = $_POST["txtLName"];
            $email = $_POST["txtEmail"];
            $office = $_POST["txtOffice"];

            $sql = "INSERT INTO `Advisors` (`a_id`, `fName`, `lName`, `email`, `office`) VALUES ('$advisorID', '$fName', '$lName', '$email', '$office')";

            $rs = $COMMON->executeQuery($sql, $_SERVER["SCRIPT_NAME"]);

            // Send user to the Advisor Meeting Manager page.
            header('Location: Advisor_Meeting_Manager.php');
        }
    }
?>

<!DOCTYPE HTML>
<html>
    <head>
        <title>Advisor Creator</title>
        <link rel = "stylesheet" type = "text/css" href = "Account_Creation.css">
    </head>

    <body>
        <div id = "box">
            <h1 id = "title">Advisor Manager</h1>
            <p><a href = "Advisor_Login.php">Already have an account? Click here.</a></p>

            <?php
                // If the submit button has been clicked:
                if (isset($_POST["submit"]))
                {
                    // If the advisor is already in the database:
                    if ($result == 1) {
                        echo("<h2>This user already exists! Please log in!</h1>");
                    }
                }
            ?>

            <form method = "post" action = "<?php echo $_SERVER["PHP_SELF"]; ?>">
                <div class = "names">First Name:</div>
                <input type = "text" name = "txtFName" maxlength = "20" autofocus required>

                <div class = "names">Last Name:</div>
                <input type = "text" name = "txtLName" maxlength = "20" required>

                <div class = "names">Advisor ID:</div>
                <input type = "text" name = "txtAdvID" maxlength = "7" pattern = "[A-Z]{2}\d{5}" placeholder = "AB12345 (Case Sensitive)" required>

                <div class = "names">Email:</div>
                <input type = "text" name = "txtEmail" maxlength = "35" pattern = ".+@umbc[.]edu$" placeholder = "username@umbc.edu" required>

                <div class = "names">Office Location:</div>
                <input type = "text" name = "txtOffice" required>
                <br>
                
                <input type = "submit" name = "submit" value = "Continue">
            </form>

        </div>
    </body>
</html>