<?php
	include("CommonMethods.php");

    $debug = false;
    $COMMON = new Common($debug);

    session_start();

    // Store the student ID from the previous page. (Can be the login or the creation page).
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

    // When the student clicks submit.
	if (isset($_POST["submit"]))
	{
    	$type = $_POST["drpType"];

        // If they choose an individual meeting.
        if ($type == "Individual")
        {
            // Get the meetings where they aren't full.
            $sql = "SELECT * FROM `Meetings` WHERE `type` = 0 and `num_students` = 0";
            $rs = $COMMON->executeQuery($sql, $_SERVER["SCRIPT_NAME"]);
            $numMeetings = mysql_num_rows($rs);

            // If no meetings are available, we go to HTML and print out error.
            if ($numMeetings == 0)
            {
                echo("");
            }
        }

        // If they choose a group meeting.
    	elseif ($type == "Group")
    	{
            $sql = "SELECT * FROM `Meetings` WHERE `type` = 1 and `num_students` != 10";
            $rs = $COMMON->executeQuery($sql, $_SERVER["SCRIPT_NAME"]);
            $numMeetings = mysql_num_rows($rs);

            // If no meetings are available, we go to HTML and print out error.
            if ($numMeetings == 0)
            {
                echo("");
            }
    	}
	}

    // Once they sign hit sign up.
    if (isset($_POST["sign-up"]))
    {
        // Gather informataion.
        $idSelected = $_POST["txtMeetingID"];

        // Check if they entered an invalid meeting format.
        if ($idSelected == "" || ctype_space($idSelected))
        {
            echo("");
        }

        // If they entered a valid ID.
        else
        {
        	// Get desired meeting.
            $sqlMeeting = "SELECT * FROM `Meetings` WHERE `m_id` = $idSelected";
            $rs1 = $COMMON->executeQuery($sqlMeeting, $_SERVER["SCRIPT_NAME"]);

            // Find how many.
            $numberMeetings = mysql_num_rows($rs1);
            $row1 = mysql_fetch_row($rs1);

            // Type of meeting and number of people in meeting.
            $meetType = (int)$row1[1];
            $meetAttendance = (int)$row1[4];

            // If no meetings are available, we go to HTML and print out error.
            if ($numberMeetings == 0)
            {
                echo("");
            }

            // If they choose an individual meeting.
            elseif($meetType == 0)
            {
                // If the meeting is not full.
                if ($meetAttendance == 0)
                {
                    $addStudent = "UPDATE `Meetings` SET `num_students` = ($meetAttendance + 1) WHERE `m_id` = '$idSelected'";
                    $addID = "UPDATE `Students` SET `m_id` = '$idSelected' WHERE `s_id` = '$studentID'";

                    $rs = $COMMON->executeQuery($addStudent, $_SERVER["SCRIPT NAME"]);
                    $rs = $COMMON->executeQuery($addID, $_SERVER["SCRIPT NAME"]);
                    header("Location: Student_Dashboard.php");
                }

                // Full meeting.
                else
                {
                    echo("");
                }
            }

            // If they choose a group meeting.
            else
            {
                // If the group meeting is full:
                if ($meetAttendance >= 10)
                {
                    echo("");
                }

                // Otherwise, add them.
                else
                {
                    $addStudents2 = "UPDATE `Meetings` SET `num_students` = `num_students` + 1 WHERE `m_id` = '$idSelected'";
                    $addID2 = "UPDATE `Students` SET `m_id` = '$idSelected' WHERE `s_id` = '$studentID'";
                    $rs = $COMMON->executeQuery($addStudents2, $_SERVER["SCRIPT NAME"]);
                    $rs = $COMMON->executeQuery($addID2, $_SERVER["SCRIPT NAME"]);
                    header("Location: Student_Dashboard.php");
                }
            }
        }
    }

    // Send them back to the dashboard when they hit back.
    if (isset($_POST["back"]))
    {
        header("Location: Student_Dashboard.php");
    }
?>


<!DOCTYPE HTML>
<html>

	<head>
  	<title>Student Search</title>
  		<link rel= "stylesheet" type= "text/css" href= "Manager.css">
	</head>

	<body>
        <div id = "box">
            <h1 id = "title">Meeting Search</h1>

            <?php

                if (isset($_POST["submit"]))
                {
                    // Error message for no meetings.
                    if ($numMeetings == 0)
                    {
                        echo("<h2>No Meetings Found</h2>");
                        header("refresh : 3; url = Student_Search.php");
                    }

                    // Otherwiser print out the meetings.
                    else
                    {
                        $row = array();
                        $counter = 0;
                        while ($temp = mysql_fetch_row($rs))
                        {
                            $row[$counter] = $temp;
                            $counter++;
                        }

                        // Print out a new table.
                        echo ("<table class = 'center'>\n");

                        // Set up the key for reading the table of meetings.
                        echo("<table border = '2px'>\n");
                        echo("<tr>\n");
                        echo("<th>Meeting ID</th>\n");
                        echo("<th>Advisor</th>\n");
                        echo("<th>Location</th>\n");
                        echo("<th>Date</th>\n");
                        echo("<th>Time</th>\n");
                        echo("<th>Type</th>\n");
                        echo("</tr>\n");

                        // Array of advisors.
						$advRow = array();

                        // Print out the individual meetings.
                        for ($i = 0; $i < $counter; $i++)
                        {

                            // New row.
                            echo("<tr>\n");

                            // Meeting ID
                            echo("<td>" . $row[$i][0] . "</td>\n");

							// Get the advisors' names.
							$advId = $row[$i][2];
							$sql = "SELECT `fName`, `lName` FROM `Advisors` WHERE `a_id` = '$advId'";
							$rs2 = $COMMON->executeQuery($sql, $_SERVER["SCRIPT_NAME"]);
							while($temp_adv = mysql_fetch_row($rs2))
							{
								array_push($advRow, $temp_adv);
							}
							
                            // Advisor's Name
                            echo("<td>" . $advRow[$i][0] . " " . $advRow[$i][1] . "</td>\n");

                            // Meeting Location
                            echo("<td>" . $row[$i][3] . "</td>\n");

                            // Meeting Date
                            echo("<td>" . $row[$i][5] . "</td>\n");

                            // Meeting Time
                            echo("<td>" . $row[$i][6] . "</td>\n");

                            // Check the type of group meeting.
                            if ($row[$i][1] == 0)
                            {
                                echo("<td>Individual</td>\n");
                            }

                            else
                            {
                                echo("<td>Group</td>\n");
                            }

                            echo("</td>\n");

                            // End row.
                            echo("</tr>\n");

                        }

                        echo("</table>\n");
                    }
                }

                // Error messages to be printed out if they have errors.
                if (isset($_POST["sign-up"]))
                {

                    if ($idSelected == "" || ctype_space($idSelected))
                    {
                        echo("<h2>Meeting ID Required!</h2>\n");
                        header("refresh : 3; url = Student_Search.php");
                    }

                    // If there are no meetings with that desired ID.
                    else if ($numberMeetings == 0)
                    {
                        echo("<h2>Meeting Does Not Exist!</h2>\n");
                        header("refresh : 3; url = Student_Search.php");
                    }

                    else
                    {
                        // Individual meeting.
                        if($meetType == 0)
                        {
                            // If the meeting is not full.
                            if ($meetAttendance != 0)
                            {
                                echo("<h2>Meeting is Full!</h2>\n");
                                header("refresh : 3; url = Student_Search.php");
                            }
                        }

                        else
                        {
                            // Meeting is full.
                            if ($meetAttendance >= 10)
                            {
                                echo("<h2>Meeting is Full!</h2>\n");
                                header("refresh : 3; url = Student_Search.php");
                            }
                        }
                    }
                }
            ?>

            <form method = "post" action = "<?php echo $_SERVER["PHP_SELF"]; ?>" >

                <div class = "names">Meeting Type:</div>
                <br>
                <select name = "drpType" required>
                    <option name = "0">Individual</option>
                    <option name = "1">Group</option>
                </select>
                <input name = 'submit'  type = 'submit' value = 'submit'>
                <br>
            </form>


            <form method = "post" action = "<?php echo $_SERVER["PHP_SELF"]; ?>" >
                    <div class = "names">Meeting ID:</div>
                    <br>
                    <input type = "text" name = "txtMeetingID" autofocus pattern = "^\d+$">
                    <br>
                    <input name = 'sign-up'  type = 'submit' value = 'Sign Up!'>
                    <input type = "submit" name = "back" value = "Go Back">
            </form>
        </div>
	</body>
</html>