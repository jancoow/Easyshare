<?php

require_once("settings.php");

# If there are no api keys continue without authentication

if (!(hasValidApiKey($_SERVER['HTTP_API_KEY']) || hasValidApiKey($_GET['API_KEY']))){
    die(json_encode(
        array(
            "success" => false,
            "error" => "forbidden"
        )
    ));
}


function hasValidApiKey($apiKey){
    return empty(Settings::API_KEYS) || in_array($apiKey, Settings::API_KEYS);
}