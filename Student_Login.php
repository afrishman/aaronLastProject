<?php
    include("CommonMethods.php");

    if (isset($_POST["submit"]))
    {
        // Get student ID for checking.
        $studentID = $_POST["txtStudID"];

        $debug = false;
        $COMMON = new Common ($debug);

        // Check if the student exists in the database.
        $sql = "SELECT COUNT(*) FROM `Students` WHERE `s_id` = \"$studentID\"";
        $rs = $COMMON->executeQuery($sql, $_SERVER["SCRIPT_NAME"]);

        // Store the number of students with entered ID.
        $result =  mysql_result($rs, 0);

        // If the student is not in the database:
        // Really just a filler to go to the next one inside of the HTML.
        if ($result == 0)
        {
            echo("");
        }

        // If the student is in the database.
        else
        {
            // Needed on every page where variable is transferred from page to page.
            session_start();

            // Store session ID. Needed on every page after.
            $_SESSION['sessionID'] = $_POST["txtStudID"];

            // Send student to their student dashboard.
            header('Location: Student_Dashboard.php');
        }
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Student Login</title>
        <link rel = "stylesheet" type = "text/css" href = "Account_Creation.css">
    </head>

    <body>
        <div id = "box">
            <h1 id = "title">Student Login</h1>
            <form method = "post" action = "<?php echo $_SERVER["PHP_SELF"]; ?>">
            <p><a href = "Student_Creation.php">Need to make an account? Click here.</a></p>

            <?php
                // If the submit button has been clicked:
                if (isset($_POST["submit"]))
                {
                    // If the student is not in the database:
                    if ($result == 0)
                    {
                        echo("<h2>This user does not exist! Please create an account!</h1>");
                    }
                }
            ?>

                <div class = "names">Student ID:</div>
                <input type = "text" name = "txtStudID" maxlength = "7" pattern = "[A-Z]{2}\d{5}" placeholder = "AB12345 (Case Sensitive)" required>
                <br>

                <input type = "submit" name = "submit" value = "submit">
            </form>
        </div>
    </body>
</html>