<?php
	include("CommonMethods.php");

	$debug = false;
	$COMMON = new Common($debug);

	session_start();

    // Store the advisor ID from the previous page. (Can be the login or the creation page).
    $advisorID = "";

    // Redirect advisor to log in page if they are not logged in.
    if(!isset($_SESSION['sessionID']))
    {
        header('Location: Advisor_Login.php');
    }

    // If the session is set.
    else
    {
        $advisorID = $_SESSION['sessionID'];

        // Check if the current user is actually an advisor.
		$sql = "SELECT COUNT(*) FROM `Advisors` WHERE `a_id` = \"$advisorID\"";
		$rs = $COMMON->executeQuery($sql, $_SERVER["SCRIPT_NAME"]);
		$result = mysql_result($rs, 0);

		// If the user is not, log them out and make them log in again.
		if ($result == 0)
		{
			header("Location: Logout.php");
		}

		// Otherwise, let them continue on.
		else
		{
			$_SESSION['sessionID'] = $advisorID;
		}
    }

    // Select the name of the advisor from the database.
	$sql = "SELECT `fName` FROM `Advisors` WHERE `a_id` = \"$advisorID\"";
	$rs = $COMMON->executeQuery($sql, $_SERVER["SCRIPT_NAME"]);

    // Array that holds the name.
	$names = mysql_fetch_array($rs);

    // Name to display when greeting advisor.
	$displayName = $names[0];

    // See how many meetings the advisor has.
    $sql = "SELECT * FROM `Meetings` WHERE `a_id` = \"$advisorID\"";
    $rs = $COMMON->executeQuery($sql, $_SERVER["SCRIPT_NAME"]);
    $numMeetings = mysql_num_rows($rs);

    // When submit is clicked.
	if (isset($_POST["submit"]))
	{
        $txtMeetId = $_POST['txtMeetingID'];
        $txtLoc = $_POST['txtLocation'];
        $txtDate = $_POST['date'];
        $txtTime = $_POST['time'];

		$result = 0;

        //Check the radio buttons 
        $type = "";
        $option = "";

        // If the user has not selected an update type:
        if (!isset($_POST['radUpdateType']))
        {
            $option = "";
        }

        // If the user has selected an update type:
        else
        {
            $option = $_POST['radUpdateType'];
        }

        // If the user has not selected a meeting type:
        if (!isset($_POST['radMeetingType']))
        {
            $type = "";
        }

        // If the user has selected a meeting type:
        else
        {
            $type = $_POST['radMeetingType'];
        }


        // Delete meeting
        if ($option == "delete")
        {
        	// Make sure they enter m_id.
        	if (ctype_space($txtMeetId) || $txtMeetId == "")
        	{
        		echo("");
        	}

        	else
        	{
        		// Check if that meeting exists (and if it is there meeting).
        		$check = "SELECT COUNT(*) FROM `Meetings` WHERE `m_id` = '$txtMeetId' and `a_id` = '$advisorID'";
            	$rs = $COMMON->executeQuery($check, $_SERVER["SCRIPT_NAME"]);
            	$result = mysql_result($rs, 0);

            	// If there is a row with that information to delete:
            	if ($result == 1)
            	{
                	// Update every student with the same m_id.
                	$updateStudent = "update `Students` set `m_id` = 0 where `m_id` = '$txtMeetId'";
                	$rs = $COMMON->executeQuery($updateStudent, $_SERVER["SCRIPT_NAME"]);

                	// Delete the actual meeting.
                	$delete = "DELETE FROM `Meetings` WHERE `m_id` = '$txtMeetId'";
                	$rs = $COMMON->executeQuery($delete, $_SERVER["SCRIPT_NAME"]);
                	header ('Location: Advisor_Meeting_Manager.php');
            	}

            	// If not, skip to the HTML and print out error:
            	else
            	{
                	echo ("");
            	}
        	}
        }

        // If the user wants to create a meeting:
        elseif ($option == "create")
        {
            // Check if any of the required fields are empty or just whitespaces.
            if ((ctype_space($type) || $type == "") || ((ctype_space($txtLoc)) || $txtLoc == "") || ((ctype_space($txtDate)) || $txtDate == "") || ((ctype_space($txtTime)) || $txtTime == ""))
            {
                // Error message to be made in the HTML.
                echo("");
            }

            // All required fields entered.
            else
            {
                // Check if there is already an existing meeting with that information
                $check = "SELECT COUNT(*) FROM `Meetings` WHERE `location` = '$txtLoc' and `Date` = '$txtDate' and `Time` = '$txtTime'";
                $rs = $COMMON->executeQuery($check, $_SERVER["SCRIPT_NAME"]);
                $result = mysql_result($rs, 0);

                // If there is a meeting with that information, skip to HTML and print out error:
                if ($result == 1)
                {
                    echo ("");
                }

                // Otherwise, create the meeting:
                else
                {
                    $temp = intval($type);
                    $insert = "INSERT INTO `Meetings`(`m_id`, `type`, `a_id`, `location`, `num_students`, `Date`, `Time`) VALUES ('', $temp, '$advisorID', '$txtLoc', 0, '$txtDate', '$txtTime')";
                    $rs = $COMMON->executeQuery($insert, $_SERVER["SCRIPT_NAME"]);

                    // Reload the page after creating the meeting.
                    header ('Location: Advisor_Meeting_Manager.php');
                }
            }
        }


		elseif ($option == "update")
        {
            // If they don't enter an ID:
            if (ctype_space($txtMeetId) || $txtMeetId == "")
            {
                // Print out error in the HTML.
				echo ("");
			}

            // Continue otherwise.
			else
            {
            	$get = "SELECT COUNT(*) FROM `Meetings` WHERE `m_id` = '$txtMeetId' and `a_id` = '$advisorID'";
				$rs = $COMMON->executeQuery($get, $_SERVER["SCRIPT_NAME"]);
				$resultsExist = mysql_result($rs, 0);

				if ($resultsExist == 0)
				{
					echo("");
				}

				else
				{
					// Get the actual values of the existing meeting.
					$get = "SELECT `location`, `Date`, `Time`, `type` FROM `Meetings` WHERE `m_id` = '$txtMeetId'";
					$rs = $COMMON->executeQuery($get, $_SERVER["SCRIPT_NAME"]);
					$row = mysql_fetch_row($rs);					
				
                	// Check if they want to update the location.
					if ($txtLoc != "")
                	{
                    	$row[0] = $txtLoc;
                	}

                	// Check if they want to update the date.
					if ($txtDate != "")
                	{
                    	$row[1] = $txtDate;
                	}

                	// Check if they want to update the time.
					if ($txtTime != "")
                	{
                    	$row[2] = $txtTime;
                	}

                	// See if there is another meeting with the same values as the user's desired new meeting:
					$check = "SELECT COUNT(*) FROM `Meetings` WHERE `location` = '$row[0]' and `Date` = '$row[1]' and `Time` = '$row[2]' and `m_id` != '$txtMeetId'";
					$rs = $COMMON->executeQuery($check, $_SERVER["SCRIPT_NAME"]);
					$result = mysql_result($rs, 0);

                	// If there isn't, go ahead and update:
					if ($result == 0)
                	{
						$update = "";

                    	// If the meeting type hasn't been selected:
						if (!isset($_POST['radMeetingType']))
                    	{
                        	// Update everything else.
                        	$update = "UPDATE `Meetings` SET `location` = '$row[0]', `Date` = '$row[1]', `Time` = '$row[2]' WHERE `m_id` = '$txtMeetId'";
                        	$rs = $COMMON->executeQuery($update, $_SERVER["SCRIPT_NAME"]);
                    	}

						else
                    	{
                    		// If we want to update from group to individual.
                    		if ($type == 0 && $row[3] == 1)
                    		{
                    			// Update the meeting and number of students.
                    			$temp = intval($type);
								$update = "UPDATE `Meetings` SET `type` = 0, `location` = '$row[0]', `num_students` = 0, `Date` = '$row[1]', `Time` = '$row[2]' WHERE `m_id` = '$txtMeetId'";
								$rs = $COMMON->executeQuery($update, $_SERVER["SCRIPT_NAME"]);

								// Set all students who are in this meeting to no meeting, since it goes from group to individual.
								$update = "UPDATE `Students` SET `m_id` = 0 WHERE `m_id` = '$txtMeetId'";
								$rs = $COMMON->executeQuery($update, $_SERVER["SCRIPT_NAME"]);
                    		}

                    		// If we want to update from individual to group:
                    		elseif ($type == 1 && $row[3] == 0)
                    		{
                    			// Only update the type of meeting.
                    			$temp = intval($type);
								$update = "UPDATE `Meetings` SET `type` = 1, `location` = '$row[0]', `Date` = '$row[1]', `Time` = '$row[2]' WHERE `m_id` = '$txtMeetId'";
								$rs = $COMMON->executeQuery($update, $_SERVER["SCRIPT_NAME"]);
                    		}

							// If it doesn't change, but they accidentally selected a type.
                    		else
                    		{
                        		// Update everything else.
                        		$update = "UPDATE `Meetings` SET `location` = '$row[0]', `Date` = '$row[1]', `Time` = '$row[2]' WHERE `m_id` = '$txtMeetId'";
                        		$rs = $COMMON->executeQuery($update, $_SERVER["SCRIPT_NAME"]);
                    		}
						}
							header ('Location: Advisor_Meeting_Manager.php');
					}


                	// If there is a meeting with that information.
					else
                	{
						echo("");
					}
				}
			}
		}
	}

    elseif (isset($_POST["logout"]))
    {
        header("Location: Logout.php");
    }
?>

<!DOCTYPE HTML>
<html>
    <head>
        <title>Meeting Management</title>
        <link rel = "stylesheet" type = "text/css" href = "Manager.css">
    </head>

    <body>
        <div id = "box">
            <h1 id = "title">Meeting Manager</h1>

            <?php

                if (isset($_POST["submit"]))
                {

                    // If they choose to delete and there is no meeting to delete:
                    if ($option == "delete")
                    {

                    	if (ctype_space($txtMeetId) || $txtMeetId == "")
        				{
        					echo("<h2>Meeting ID Required!\n");
        				}

        				else
        				{
        					if ($result == 0)
                    		{
                    			echo("<h2>That meeting does not exist!</h2>\n");
                    		}

                    		else
                    		{
                    			echo ("<h2>Meeting Removed.</h2>\n");
                    		}
        				}
				    }

                    // If they choose to create and a meeting already exists:
				    elseif ($option == "create")
                    {

                        if ((ctype_space($type) || $type == "") || ((ctype_space($txtLoc)) || $txtLoc == "") || ((ctype_space($txtDate)) || $txtDate == "") || ((ctype_space($txtTime)) || $txtTime == ""))
                        {
                            echo ("<h2>1 or more required fields missing!</h2>\n");
                        }

                        else
                        {
							if ($result == 1)
                        	{
                            	echo("<h2>Meeting Information Conflicts With Another!</h2>\n");
                        	}
                        }
				    }

                    // If they choose to update and a meeting has that information already:
                    elseif ($option == "update")
                    {

                    	// If they don't enter an ID:
            			if (ctype_space($txtMeetId) || $txtMeetId == "")
            			{
							echo("<h2>Meeting ID Required!</h2>\n");
						}

						else
						{
                            if ($resultsExist == 0)
                            {
                            	echo("<h2>Invalid Meeting!</h2>");
                            }

                            else
                            {
								// If there is another meeting already.
								if ($result == 1)
								{
									echo("<h2>Meeting Information Conflicts With Another!</h2>\n");
								}
                            }
						}
                    }

                    // Refresh the page after displaying error message.
				    header("refresh : 3; url = Advisor_Meeting_Manager.php");	
                }
            ?>
		
            <p>Fill the form to make changes to the desired meetings.</p>
            <p>Instructions:
                <br>
                1. Create New Meeting: Fill in only Location, Date, Time, and Meeting Type.
                <br>
                <br>
                2. Update Meeting Information: Fill in the Meeting ID and whatever else you would like to update.
                <br>
                <br>
                3. Deleting a Meeting: Only fill in the Meeting ID.
            </p>

            <?php

                // Messages to display based on number of meetings the advisor has.
                if ($numMeetings == 0)
                {
                    echo "<p class = 'greeting'>Hello $displayName. You have no meetings at the moment.</p>\n";
                }

                // They have meetings.
                else
                {
                    echo "<p class = 'greeting'>Hello $displayName. You currently have $numMeetings meetings.</p>\n";

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
                    echo("<th>Location</th>\n");
                    echo("<th>Date</th>\n");
                    echo("<th>Time</th>\n");
                    echo("<th>Type</th>\n");
                    echo("<th>Number of Students</th>\n");
		    		echo("<th>Student IDs-Major</th>\n");
                    echo("</tr>\n");

                    // $student = array();

                    // Print out the individual meetings.
                    for ($i = 0; $i < $counter; $i++)
                    {
						$student = array();
                        echo("<tr>\n");

                        // Meeting ID
                        echo("<td>" . $row[$i][0] . "</td>\n");
					
						// Getting student names.
						$temp_id = $row[$i][0];
						$stud_name = "select `fName`, `lName`, `major` from `Students` where `m_id` = '$temp_id'";
						$rs = $COMMON->executeQuery($stud_name, $_SERVER["SCRIPT_NAME"]);
			
						while($temp_Student = mysql_fetch_row($rs))
						{
							array_push($student, $temp_Student);
						}							

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
                         echo("<td> Group </td>\n");
                        }

                        // Number of students.
                        echo("<td>" . $row[$i][4] . "</td>\n");

						// Student ID.
						echo("<td>");
			
						for ($j = 0; $j < count($student); $j++)
						{
							echo ($student[$j][0] . " " . $student[$j][1] . "-" . $student[$j][2]);
							echo ("<br>");
							echo ("-------------------------------");
							echo ("<br>");
						}
				
						echo("</td>\n");

                        echo("</tr>\n");
                    }

                    echo("</table>\n");
                }
            ?>

            <form method = "post" action = "<?php echo $_SERVER["PHP_SELF"]; ?>" >

                <div class = "names">Meeting ID:</div>
                <br>
	            <input type = "text" name = "txtMeetingID" autofocus>
                <br>

                <div class = "names">Location:</div>
                <br>
                <input type = "text" name = "txtLocation" placeholder = "BIO/MYER/PHYS/MATH/SOND/SHERM/ITE/ENG/FA/PAH ###" pattern = "^$|^(ITE|MYER|ENG|BIO|SOND|PHYS|FA|PAH|SHERM|MATH)\s[0-9]{3}$">
                <br>

                <div class = "names">Date:</div>
                <br>
	            <input type = "text" name = 'date' placeholder = "mm-dd-yyyy" pattern = "^$|^((0[1-9])|(1[0-2]))-(([0-2][0-9])|([3][0-2]))-[0-9]{4}$">
                <br>

                <div class = "names">Start Time:</div>
                <br>
	            <input type = "text" name = "time" placeholder = "hh:mm" pattern = "^$|^(0([1-4]|8|9)|10|11|12):(0|3)(0)$">
                <br>

                <div class = "names">Meeting Type:</div>
                <br>

                <label class = "radOption">
                    <input type = "radio" name = "radMeetingType" value = "0"><span>Individual</span>
                    <br>

                    <input type = "radio" name = "radMeetingType" value = "1"><span>Group</span>
                    <br>
                    <br>
                </label>

                <div class = "names">Update Option:</div>
                <br>

                <label class = "radOption">
                    <input type = "radio" name = "radUpdateType" value = "create"><span>Create New Meeting</span>
                    <br>

                    <input type = "radio" name = "radUpdateType" value = "update"><span>Update Meeting Information</span>
                    <br>

                    <input type = "radio" name = "radUpdateType" value = "delete"><span>Delete Meeting</span>
                    <br>
                </label>
	        <input name = 'submit'  type = 'submit' value = 'submit'>
            <input type = "submit" name = "logout" value = "Logout">
            </form>
        </div>
    </body>
</html>
