<?php

    # Header required when receiving content from the ajax at the front-end
    header('Content-type: application/json');

    # Connection to the dataLayer
	require_once __DIR__ . '/dataLayer.php';

    # Execute the action that is being called in the ajax at the front-end
	$action = $_POST['action'];
	switch($action)
	{
        case 'LOGIN':	userLogin();
						break;
		case 'COOKIE':	verifyCookies();
						break;
		case 'END_SES': endSession();
						break;
		case 'GET_SES':	getSession();
						break;
		case 'REGISTER':registerUser();
						break;
	}

?>
