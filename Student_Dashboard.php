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

    // Check if the student has an appointment.
    $sql = "SELECT `m_id` FROM `Students` WHERE `s_id` = \"$studentID\"";
    $rs = $COMMON->executeQuery($sql, $_SERVER["SCRIPT_NAME"]);
    $result = mysql_result($rs, 0);
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Student Dashboard</title>
        <link rel = "stylesheet" type = "text/css" href = "Student_Dashboard.css">
    </head>

    <body>
        <div id = "box">
            <h1 id = "title">Student Dashboard</h1>

            <?php

                // If the student has a meeting:
                if ($result != 0)
                {
                    // See how many meetings the advisor has.
                    $sql = "SELECT * FROM `Meetings` WHERE `m_id` = $result";
                    $rs = $COMMON->executeQuery($sql, $_SERVER["SCRIPT_NAME"]);
                    $row = mysql_fetch_row($rs);

                    $sql = "SELECT * FROM `Advisors` WHERE `a_id` = '$row[2]'";
                    $rs = $COMMON->executeQuery($sql, $_SERVER["SCRIPT_NAME"]);
                    $advisorRow = mysql_fetch_row($rs);

                    // Print out a new table.
                    echo ("<table>\n");

                    // Set up the key for reading the table of meetings.
                    echo("<table border = '2px'>\n");
                    echo("<tr>\n");
                    echo("<th>Advisor</th>\n");
                    echo("<th>Email</th>\n");
                    echo("<th>Location</th>\n");
                    echo("<th>Date</th>\n");
                    echo("<th>Time</th>\n");
                    echo("<th>Type</th>\n");
                    echo("</tr>\n");
                    echo("<tr>\n");

                    // Advisor Name
                    echo("<td>" . $advisorRow[1] . " " . $advisorRow[2] . "</td>\n");

                    // Advisor Email
                    echo("<td>" . $advisorRow[3] . "</td>\n");

                    // Meeting Location
                    echo("<td>" . $row[3] . "</td>\n");

                    // Meeting Date
                    echo("<td>" . $row[5] . "</td>\n");

                    // Meeting Time
                    echo("<td>" . $row[6] . "</td>\n");

                    // Check the type of group meeting.
                    if ($row[1] == 0)
                    {
                        echo("<td>Individual</td>\n");
                    }

                    else
                    {
                        echo("<td> Group </td>\n");
                    }

                    echo("</tr>\n");
                    echo("</table>\n");

                    echo("<br>\n");

                    echo("<a href = \"Change_Meeting.php\">\n");
                        echo("<div id = \"meeting\">Change Meeting</div>\n");
                    echo("</a>\n");

                    echo("<a href = \"Edit_Info.php\">\n");
                        echo("<div id = \"information\">Update Information</div>\n");
                    echo("</a>\n");
                }


                // If the student does not have a meeting:
                else
                {
                    echo ("<h2>You do not have a meeting!</h2>");
                    echo("<a href = \"Student_Search.php\">\n");
                        echo("<div id = \"meeting\">Search For Meeting</div>\n");
                    echo("</a>\n");

                    echo("<a href = \"Edit_Info.php\">\n");
                        echo("<div id = \"information\">Update Information</div>\n");
                    echo("</a>\n");
                }

                echo("<a href = \"Logout.php\">\n");
                    echo("<div id = \"information\">Logout</div>\n");
                echo("</a>\n");
            ?>

        </div>
    </body>
</html>