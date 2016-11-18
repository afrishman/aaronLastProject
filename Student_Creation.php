<?php
	include("CommonMethods.php");

	// If the submit button has been clicked, then we execute the rest of the script.
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

        // If the student is already in the database:
        // Really just a filler to go to the next one inside of the HTML.
        if ($result == 1)
        {
            echo("");
        }

        // If the student is not in the database, create user and send them to dashboard.
        else
        {
            // Needed on every page where variable is transferred from page to page.
            session_start();

            // Store session ID. Needed on every page after.
            $_SESSION['sessionID'] = $_POST["txtStudID"];

            // Required student information.
            $fName = $_POST["txtFName"];
            $lName = $_POST["txtLName"];
            $email = $_POST["txtEmail"];
            $major = $_POST["drpMajor"];

            $sql = "INSERT INTO `Students` (`s_id`, `fName`, `lName`, `email`, `major`, `m_id`) VALUES ('$studentID', '$fName', '$lName', '$email', '$major', 0)";

            $rs = $COMMON->executeQuery($sql, $_SERVER["SCRIPT_NAME"]);

            // Send user to the student dashboard page.
            header('Location: Student_Dashboard.php');
        }
	}
?>

<!DOCTYPE HTML>
<html>
	<head>
		<title>Student Creator</title>
		<link rel = "stylesheet" type = "text/css" href = "Account_Creation.css">
	</head>

	<body>
		<div id = "box">
			<h1 id = "title">Student Sign-Up</h1>
            <p><a href = "Student_Login.php">Already have an account? Click here.</a></p>

            <?php
                // If the submit button has been clicked, then we execute the rest of the script.
                if (isset($_POST["submit"]))
                {
                    // If the student is already in the database:
                    if ($result == 1) {
                        echo("<h2>This user already exists! Please log in!</h1>");
                    }
                }
            ?>

			<form method = "post" action = "<?php echo $_SERVER["PHP_SELF"]; ?>">
                <div class = "names">First Name:</div>
                <input type = "text" name = "txtFName" maxlength = "20" autofocus required>

                <div class = "names">Last Name:</div>
                <input type = "text" name = "txtLName"  maxlength = "20" required>

                <div class = "names">Student ID:</div>
                <input type = "text" name = "txtStudID" maxlength = "7" pattern = "[A-Z]{2}\d{5}" placeholder = "AB12345 (Case Sensitive)" required>

                <div class = "names">Email:</div>
                <input type = "text" name = "txtEmail" maxlength = "35" pattern = ".+@umbc[.]edu$" placeholder = "username@umbc.edu" required>

                <div class = "names">Major:</div>
                <select name = "drpMajor" required>
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

                <input type = "submit" name = "submit" value = "Continue">
			</form>

		</div>
	</body>
</html>