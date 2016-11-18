<?php
    include("CommonMethods.php");

    $debug = false;
    $COMMON = new Common($debug);

    session_start();

    // Redirect student to log in page if they are not logged in.
    if(!isset($_SESSION['sessionID']))
    {
        header('Location: Student_Login.php');
    }

    // Set the student ID.
    else
    {
        $studentID = $_SESSION['sessionID'];
        $_SESSION['sessionID'] = $studentID;

        // Grab student information to display.
        $sql = "SELECT * FROM `Students` WHERE `s_id` = \"$studentID\"";
        $rs = $COMMON->executeQuery($sql, $_SERVER["SCRIPT_NAME"]);
        $row = mysql_fetch_row($rs);
    }

    if (isset($_POST["submit"]))
    {

        // Check if the student exists in the database.
        $sql = "SELECT * FROM `Students` WHERE `s_id` = \"$studentID\"";
        $rs = $COMMON->executeQuery($sql, $_SERVER["SCRIPT_NAME"]);
        $row = mysql_fetch_row($rs);

        // Grab entered data.
        $firstName = $_POST["txtFName"];
        $lastName = $_POST["txtLName"];
        $email = $_POST["txtEmail"];
        $majorName = $_POST["drpMajor"];

        // Checking if any are blank.
        if (ctype_space($_POST["txtFName"]) || $_POST["txtFName"] == "")
        {
            $firstName = $row[1];
        }

        if (ctype_space($_POST["txtLName"]) || $_POST["txtLName"] == "")
        {
            $lastName = $row[2];
        }

        if (ctype_space($_POST["txtEmail"]) || $_POST["txtEmail"] == "")
        {
            $email = $row[3];
        }

        if ($_POST["drpMajor"] == "Don't Change")
        {
            $majorName = $row[4];
        }

        // Actually update.
        $sql = "UPDATE `Students` SET `fName` = '$firstName', `lName` = '$lastName', `email` = '$email', `major` = '$majorName' WHERE `s_id` = '$studentID'";
        $rs = $COMMON->executeQuery($sql, $_SERVER["SCRIPT_NAME"]);
    }

    else if (isset($_POST["back"]))
    {
    	header("Location: Student_Dashboard.php");
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Edit Information</title>
        <link rel = "stylesheet" type = "text/css" href = "Account_Creation.css">
    </head>

    <body>
        <div id = "box">
            <h1 id = "title">Update Information</h1>

            <form method = "post" action = "<?php echo $_SERVER["PHP_SELF"]; ?>">
                <div class = "names">First Name:</div>

                <?php
                	// Create a textbox with the user's first name as a placeholder.
                	echo ("<input type = 'text' name = 'txtFName' maxlength = '20' autofocus placeholder = '$row[1]'>\n");
                ?>

                <div class = "names">Last Name:</div>

                <?php
                	// Create a textbox with the user's first name as a placeholder.
                	echo ("<input type = 'text' name = 'txtLName' maxlength = '20' autofocus placeholder = '$row[2]'>\n");
                ?>

                <div class = "names">Email:</div>

                <?php
                	// Create a textbox with the user's first name as a placeholder.
                	echo ("<input type = 'text' name = 'txtEmail' maxlength = '35' pattern = '.+@umbc[.]edu$'' autofocus placeholder = '$row[3]'>\n");
                ?>
                
                <div class = "names">Major:</div>
                <select name = "drpMajor" required>
                    <option name = "Don't Change">Don't Change</option>
                    <option name = "Biological Sciences BA">Biological Sciences BA</option>
                    <option name = "Biological Sciences BS">Biological Sciences BS</option>
                    <option name = "Biology Education BA">Biology Education BA</option>
                    <option name = "Bioinformatics BS">Bioinformatics BS</option>
                    <option name = "Biochemistry BS">Biochemistry BS</option>
                    <option name = "Chemistry BA">Chemistry BA</option>
                    <option name = "Chemistry BS">Chemistry BS</option>
                    <option name = "Chemistry Education BA">Chemistry Education BA</option>
                    <option name = "Physics Education BA">Physics Education BA</option>
                    <option name = "Physics BS">Physics BS</option>
                    <option name = "Mathematics BA">Mathematics BA</option>
                    <option name = "Mathematics BS">Mathematics BS</option>
                    <option name = "Statistics BS">Statistics BS</option>
                </select>
                <br>

                <input type = "submit" name = "submit" value = "Submit">
                <input type = "submit" name = "back" value = "Go Back">
            </form>
        </div>
    </body>
</html>