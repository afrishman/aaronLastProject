<?php
	// Logout method.
	// Unsets the session and destroys it.
	session_start();
	session_unset();
	session_destroy();

	// Send them back to login page.
	header("Location: Account_Type_Selection.html");
?>