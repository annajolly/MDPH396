<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "QuestionnaireDB2017";

// Get post data
$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$questionnaire_id = $request->questionnaire_id;
$questiongroup_id = $request->questiongroup_id;
$doctor_id = $request->doctor_id;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// remove questionnaire_questiongroup instance
$sql = "DELETE FROM questionnaire_questiongroup WHERE questionnaire_ser_num = " . $questionnaire_id . " AND questiongroup_ser_num = " . $questiongroup_id . " ;";
if ($conn->query($sql) === true) {
  // TO DO
}
else {
  //TO DO
}

$conn->close();
?>
