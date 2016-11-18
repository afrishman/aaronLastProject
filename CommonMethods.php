<?php

class Common
{
    var $conn;
    var $debug;

    function Common($debug)
    {
        $this->debug = $debug;
        $rs = $this->connect("twebb1"); // Database name should be used as parameters.
        return $rs;
    }

    // Connect to MySQL.
    function connect($db)
    {
        $conn = @mysql_connect("studentdb-maria.gl.umbc.edu", "twebb1", "twebb1") or die("Could not connect to MySQL");
        $rs = @mysql_select_db($db, $conn) or die("Could not connect select $db database");
        $this->conn = $conn;
    }

    // Actually execute the query.
    function executeQuery($sql, $filename)
    {
        if($this->debug == true)
        {
            echo("$sql <br>\n");
        }
        $rs = mysql_query($sql, $this->conn) or die("Could not execute query '$sql' in $filename");
        return $rs;
    }
}

?>