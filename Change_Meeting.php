<?php
    include("CommonMethods.php");

    $debug = false;
    $COMMON = new Common($debug);

    session_start();

    // Store the student ID from the previous page.
    $studentID = "";

    // Redirect student to log in page if they are not logged in.
    if(!isset($_SESSION['sessionID']))
    {
        header('Location: Student_Login.php');
    }

    // Set the student and session ID.
    else
    {
        $studentID = $_SESSION['sessionID'];
        $_SESSION['sessionID'] = $studentID;
    }

    if (isset($_POST["Yes"]))
    {
        // Get the meeting ID that the student has.
        $sql = "SELECT `m_id` FROM `Students` WHERE `s_id` = \"$studentID\"";
        $rs = $COMMON->executeQuery($sql, $_SERVER["SCRIPT_NAME"]);
        $result = mysql_result($rs, 0);


        // Decrement the number of students
        $sql = "UPDATE `Meetings` SET `num_students` = `num_students` - 1 WHERE `m_id` = '$result' and `num_students` > 0";
        $rs = $COMMON->executeQuery($sql, $_SERVER["SCRIPT_NAME"]);

        // Update the student's meeting ID.
        $sql = "UPDATE `Students` SET `m_id` = 0 WHERE `s_id` = '$studentID'";
        $rs = $COMMON->executeQuery($sql, $_SERVER["SCRIPT_NAME"]);

        // Send them to the meeting search page.
        header("Location: Student_Dashboard.php");
    }

    // If the student clicks no, send them back to their dashboard.
    elseif (isset($_POST["No"]))
    {
        header("Location: Student_Dashboard.php");
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Change Meeting</title>
        <link rel = "stylesheet" type = "text/css" href = "Student_Dashboard.css">
    </head>

    <body>
        <div id = "box">
            <h1 id = "title">Student Dashboard</h1>

            <h2>Are you sure you want to get rid of your current meeting?</h2>

            <form method = "post" action = "<?php echo $_SERVER["PHP_SELF"]; ?>">
                <input type = "submit" name = "Yes" value = "Yes">
                <input type = "submit" name = "No" value = "No">
            </form>
        </div>
    </body>
</html>