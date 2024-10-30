<?php
// Pass session data over.
//session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

//set exection time to maximum
set_time_limit(0);

//set to use maximum available memory
ini_set('memory_limit', '-1');

// Include the required dependencies.
require_once(  __DIR__.'/libraries/Twitter/TwitterOAuth.php' );