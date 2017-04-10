<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "QuestionnaireDB2017";

// Get post data
$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$questionnaire_id = $request->questionnaire_id;
$questiongroup_ser_num = $request->questiongroup_ser_num;
$doctor_id = $request->doctor_id;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// get max position number and add 1
$sql = "SELECT MAX(position) FROM questionnaire_questiongroup WHERE questionnaire_ser_num = " . $questionnaire_id . ";";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$new_position = $row["MAX(position)"] + 1;

// add questionnaire_questiongroup instance
$sql = "INSERT INTO questionnaire_questiongroup (questionnaire_ser_num, questiongroup_ser_num, position, created_by) VALUES (" . $questionnaire_id . ", " . $questiongroup_ser_num . ", " . $new_position . ", " . $doctor_id . ");";
if ($conn->query($sql) !== true) {
  echo "Failure to insert into questionnaire_questiongroup";
}

$conn->close();
?>
