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

    # Query to retrieve a user data
    function validateUserCredentials($userName)
    {
        # Open and validate the Database connection
    	$conn = connect();

        if ($conn != null)
        {
        	$sql = "SELECT * FROM User WHERE userName = '$userName'";
			$result = $conn->query($sql);

			# The current user exists
			if ($result->num_rows > 0)
			{
				while($row = $result->fetch_assoc())
		    	{
					$conn->close();
					return array("status" => "COMPLETE", "fName" => $row['fName'], "lName" => $row['lName'], "password" => $row['passwrd']);
				}
			}
			else
			{
				# The user doesn't exists in the Database
				$conn->close();
				return errors(400);
			}
        }
        else
        {
        	# Connection to Database was not successful
        	$conn->close();
        	return errors(500);
        }
    }

    # Query to find out if the user already exist in the Database
    function verifyUser($userName)
    {
    	# Open and validate the Database connection
    	$conn = connect();

        if ($conn != null)
        {
        	$sql = "SELECT * FROM User WHERE userName = '$userName'";
			$result = $conn->query($sql);

			if ($result->num_rows > 0)
			{
				# The current user already exists
				$conn->close();
				return errors(412);
			}
			else
			{
				$conn->close();
				return array("status" => "COMPLETE");
			}
        }
        else
        {
        	# Connection to Database was not successful
        	$conn->close();
        	return errors(500);
        }
    }

    # Query to insert a new user to the Database
    function registerNewUser($userFirstName, $userLastName, $userName, $email, $userPassword)
    {
    	# Open and validate the Database connection
    	$conn = connect();

        if ($conn != null)
        {
        	$sql = "INSERT INTO User(fName, lName, userName, email, passwrd) VALUES ('$userFirstName', '$userLastName', '$userName', '$email', '$userPassword')";
			if (mysqli_query($conn, $sql))
	    	{
	    		$conn->close();
			    return array("status" => "COMPLETE");
			}
			else
			{
				$conn->close();
				return errors(409);
			}
        }
        else
        {
        	# Connection to Database was not successful
        	$conn->close();
        	return errors(500);
        }
    }



?>
