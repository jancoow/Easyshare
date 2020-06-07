<?php
require_once("settings.php");

if (!(hasValidApiKey($_SERVER['HTTP_API_KEY']) || hasValidApiKey($_GET['API_KEY']) || isLoggedIn())){
	 // Seems that the user is not authorized. Check if an OAUTH server is configured, if not just throw a not authenticated exception
	 if(getenv("AUTH_SERVER_URL") != null){
	    // If we don't have an authorization code then get one
			$authUrl = $provider->getAuthorizationUrl();
			$_SESSION['oauth2state'] = $provider->getState();
			header('Location: '.$authUrl);
			exit;
	 }else{
	     die(json_encode(
        array(
            "success" => false,
            "error" => "forbidden"
        )
   	 ));
    }
}


function hasValidApiKey($apiKey){
    return API_KEY !== null && API_KEY != "" && API_KEY == $apiKey;
}

	
function isLoggedIn(){
	global $provider;
	if(!isset($_SESSION['token'])){
		return false;
	}

	try {
    // We got an access token, let's now get the user's details
    $token = unserialize($_SESSION['token']);
    if($token instanceof League\OAuth2\Client\Token\AccessToken){
		  $user = $provider->getResourceOwner($token);
		  return true;
    }
  } catch (Exception $e) {
     return false;
 }
 return false;
}
