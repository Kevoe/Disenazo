<?php

    # Establishing the connection to the Database
    function connect()
    {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "Disenazo";

        $connection = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($connection->connect_error)
        {
            return null;
        }
        else
        {
            return $connection;
        }
    }

    # Callback error messages
	function errors($type)
	{
		$header = "HTTP/1.1 ";

		switch($type)
		{
			case 306:	$header .= "306 Wrong Credentials";
						break;
			case 400:	$header .= "400 User Not Found";
						break;
			case 404:	$header .= "404 Request Not Found";
						break;
			case 409:	$header .= "409 Your action was not completed correctly, please try again later";
						break;
			case 412:	$header .= "412 Username already in use";
						break;
			case 417:	$header .= "417 No content set in the cookie/session";
						break;
			case 500:	$header .= "500 Bad connection to Database";
						break;
			default:	$header .= "404 Request Not Found";
		}

		header($header);
		return array('status' => 'ERROR', 'code' => $type);
	}

?>
