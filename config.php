<?php

define('DB_SERVER', 'fuelit');
define('DB_USERNAME', 'fishr');
define('DB_PASSWORD', 'fishr123');
define('DB_NAME', 'fishr');
 
// define('DB_SERVER', 'localhost');
// define('DB_USERNAME', 'fishr');
// define('DB_PASSWORD', 'fishr123');
// define('DB_NAME', 'fishr');


/* Attempt to connect to MySQL database */
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

function getCurDate() {
    date_default_timezone_set('Pacific/Auckland');
    $date = date("Y-m-d H:i:s");
    return $date;
}

  function mysqli($link, $statement, $parameters) {

    if($reqStmt = mysqli_prepare($link, $statement)) {
      //create the parameter string to pass the types to bind 
      $paraStr = "";
      foreach($parameters as $parameter) {
        $type = gettype($parameter);
        if($type == "integer") {
          $paraStr = $paraStr."i";
        } elseif($type == "double") {
          $paraStr = $paraStr."d";
        } elseif($type == "string") {
          $paraStr = $paraStr."s";
        } else {
          $paraStr = $paraStr."b";
        }
      }
      //Bind the to statement
      mysqli_stmt_bind_param($reqStmt, $paraStr, ...$parameters);
      
      if(mysqli_stmt_execute($reqStmt)) {
        mysqli_stmt_store_result($reqStmt);
      }
      
      return $reqStmt;
    }
  }

function altermysqli($link, $statement, $parameters) {



  if($reqStmt = mysqli_prepare($link, $statement)) {
    //create the parameter string to pass the types to bind 
    $paraStr = "";
    foreach($parameters as $parameter) {
      $type = gettype($parameter);
      if($type == "integer") {
        $paraStr = $paraStr."i";
      } elseif($type == "double") {
        $paraStr = $paraStr."d";
      } elseif($type == "string") {
        $paraStr = $paraStr."s";
      } else {
        $paraStr = $paraStr."b";
      }
    }

    //Bind the to statement
    mysqli_stmt_bind_param($reqStmt, $paraStr, ...$parameters);
    
    mysqli_stmt_execute($reqStmt);      
  }
}

function getFav($link) {
  $sqlq = "SELECT `favspots` FROM `users` WHERE `Username` = '".trim($_SESSION['username'])."';";
  $results = mysqli_query($link,$sqlq);
  $resultString = mysqli_fetch_row($results);
  return $resultString[0];
}

?>