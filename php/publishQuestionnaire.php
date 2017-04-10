<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "QuestionnaireDB2017";

// Get post data
$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$q_ser_num = $request->questionnaire_ser_num;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// change questionnaire's published field to 1
$sql = "UPDATE questionnaire SET publish = 1 WHERE ser_num = " . $q_ser_num . ";";
if ($conn->query($sql) !== true) {
  echo "Failure";
}

$conn->close();
?>
