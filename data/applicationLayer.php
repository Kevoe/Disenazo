<?php

    # Header required when receiving content from the ajax at the front-end
    header('Content-type: application/json');

    # Connection to the dataLayer
	require_once __DIR__ . '/dataLayer.php';

    # Execute the action that is being called in the ajax at the front-end
	$action = $_POST['action'];
	switch($action)
	{
        case 'LOGIN':	      userLogin();
						      break;
		case 'COOKIE':	      verifyCookies();
						      break;
		case 'END_SES':       endSession();
						      break;
		case 'GET_SES':	      getSession();
						      break;
		case 'REGISTER':      registerUser();
						      break;
        case 'NEW_DESIGNS':   getDesigns('NEW_DESIGNS');
                              break;
        case 'MOST_POP_WEEK': getDesigns('MOST_POP_WEEK');
                              break;
        case 'DESIGN':        getCompleteDesign();
                              break;
        case 'ADD_CART':      addItemsToCart();
                              break;
	}

    # ACtion to login the current user credentials and redirect it to home.html
	function userLogin()
	{
		$userName = $_POST['userName'];
		$password = $_POST['userPassword'];
		$rememberData = $_POST['rememberData'];

		# Verify if the user currently exists in the Database
		$result = validateUserCredentials($userName);

		if ($result['status'] == 'COMPLETE')
		{
			$decryptedPassword = decryptPassword($result['password']);

			# Compare the decrypted password with the one provided by the user
		   	if ($decryptedPassword === $password)
		   	{
		    	$response = array("status" => "COMPLETE");

			    # Starting the sesion
		    	startSession($result['fName'], $result['lName'], $userName);

			    # Setting the cookies
			    if ($rememberData)
				{
					setcookie("cookieUserName", $userName);
			  	}
			    echo json_encode($response);
			}
			else
			{
				die(json_encode(errors(306)));
			}
		}
		else
		{
			die(json_encode($result));
		}
	}

    # Action to get the current cookies if they exist
	function verifyCookies()
	{
		if (isset($_COOKIE['cookieUserName']))
		{
			echo json_encode(array('cookieUserName' => $_COOKIE['cookieUserName']));
		}
		else
		{
			# Cookie not set yet
		    die(json_encode(errors(417)));
		}
	}

    # Action to register a user
	function registerUser()
	{
		$userName = $_POST['userName'];

		# Verify that the user doesn't exist in the database
		$result = verifyUser($userName);

		if ($result['status'] == 'COMPLETE')
		{
			$email = $_POST['email'];
			$userFirstName = $_POST['userFirstName'];
			$userLastName = $_POST['userLastName'];

			$userPassword = encryptPassword();

			# Make the insertion of the new user to the Database
			$result = registerNewUser($userFirstName, $userLastName, $userName, $email, $userPassword);

			# Verify that the insertion was successful
			if ($result['status'] == 'COMPLETE')
			{
				# Starting the session
				startSession($userFirstName, $userLastName, $userName);
				echo json_encode($result);
			}
			else
			{
				# Something went wrong while inserting the new user
				die(json_encode($result));
			}
		}
		else
		{
			# Username already exists
			die(json_encode($result));
		}
	}

    # Action to encrypt the password of the user
	function encryptPassword()
	{
		$userPassword = $_POST['userPassword'];

	    $key = pack('H*', "bcb04b7e103a05afe34763051cef08bc55abe029fdebae5e1d417e2ffb2a00a3");
	    $key_size =  strlen($key);

	    $plaintext = $userPassword;

	    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
	    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

	    $ciphertext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $plaintext, MCRYPT_MODE_CBC, $iv);
	    $ciphertext = $iv . $ciphertext;

	    $userPassword = base64_encode($ciphertext);

	    return $userPassword;
	}

    #Action to decrypt the password of the user
	function decryptPassword($password)
	{
		$key = pack('H*', "bcb04b7e103a05afe34763051cef08bc55abe029fdebae5e1d417e2ffb2a00a3");

	    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
	    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

	    $ciphertext_dec = base64_decode($password);
	    $iv_dec = substr($ciphertext_dec, 0, $iv_size);
	    $ciphertext_dec = substr($ciphertext_dec, $iv_size);

	    $password = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $ciphertext_dec, MCRYPT_MODE_CBC, $iv_dec);

	   	$count = 0;
	   	$length = strlen($password);

	    for ($i = $length - 1; $i >= 0; $i --)
	    {
	    	if (ord($password{$i}) === 0)
	    	{
	    		$count ++;
	    	}
	    }

	    $password = substr($password, 0,  $length - $count);

	    return $password;
	}

    # Action to set the initial values of the session
	function startSession($fName, $lName, $username)
    {
		// Starting the session
	    session_start();
		$_SESSION['userFirstName'] = $fName;
	    $_SESSION['userLastName'] = $lName;
	    $_SESSION['userName'] = $username;
    }

    # Action to get the current session data
    function getSession()
    {
    	session_start();
    	if (isset($_SESSION['userFirstName']) && $_SESSION['userLastName'] && $_SESSION['userName'])
    	{
    		echo json_encode(array("firstName" => $_SESSION['userFirstName'], "lastName" => $_SESSION['userLastName']));
    	}
    	else
    	{
    		echo json_encode(errors(417));
    	}
    }

    # Action to end the current session data
    function endSession()
	{
		session_start();
		if (isset($_SESSION['userFirstName']) && isset($_SESSION['userLastName']) && isset($_SESSION['userName']))
		{
			unset($_SESSION['userFirstName']);
			unset($_SESSION['userLastName']);
			unset($_SESSION['userName']);
			session_destroy();

			echo json_encode(array('success' => 'Session deleted'));
		}
		else
		{
			die(json_encode(errors(417)));
		}
	}

    #Action to get all designs from the newest to the oldest
    function getDesigns($act)
    {
        # Execute the action to get different designs
        switch($act)
    	{
            case 'NEW_DESIGNS':   $result = getNewestDesignsDB();
                                  break;
            case 'MOST_POP_WEEK': $result = mostPopularInTheWeekDB();
                                  break;
    	}

        if($result['message'] == 'OK')
        {
            echo json_encode($result);
        }
        else
        {
            if($result['message'] == 'NONE')
            {
                # Designs not found
                echo json_encode($result);
            }
            else
            {
                die(json_encode($result));
            }
        }
    }

    #Action to get the whole design info
    function getCompleteDesign()
    {
        $designId = $_POST['designId'];
        $result = getCompleteDesignDB($designId);

        if($result['message'] == 'OK')
        {
            # All the info of the design
            echo json_encode($result);
        }
        else
        {
            if($result['message'] == 'NONE')
            {
                # No designs with that designId
                echo json_encode($result);
            }
            else
            {
                # Error
                die(json_encode($result));
            }
        }
    }

    # Action to add Items to the Cart
	function addItemsToCart()
	{
		$order = $_POST['order'];
		session_start();
		if(isset($_SESSION['userName']))
		{
			$result = addItemsToCartDB($_SESSION['userName'], $order);
			if($result['status'] == 'COMPLETE')
			{
				echo json_encode($result);
			}
			else
			{
                # Something went wrong
				die(json_encode($result));
			}
		}
		else
		{
			die(json_encode(errors(417)));
		}
	}

?>
