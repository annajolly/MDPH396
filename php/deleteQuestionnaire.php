<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "QuestionnaireDB";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get post data
$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$q_ser_num = $request->questionnaire_ser_num;

// delete questionnaire_questiongroup records
$sql = "DELETE FROM questionnaire_questiongroup
WHERE questionnaire_ser_num = " . $q_ser_num . ";";
if ($conn->query($sql) !== true) {
  echo "Failure to delete questionnaire_questiongroup records";
}
// delete questionnaire_user records
$sql = "DELETE FROM questionnaire_user
WHERE questionnaire_ser_num = " . $q_ser_num . ";";
if ($conn->query($sql) !== true){
  echo "Failure to delete questionnaire_user records";
}
// delete questionnaire_tag records
$sql = "DELETE FROM questionnaire_tag
WHERE questionnaire_ser_num = " . $q_ser_num . ";";
if ($conn->query($sql) !== true){
  echo "Failure to delete questionnaire_user records";
}
// delete questionnaire record
$sql = "DELETE FROM questionnaire
WHERE ser_num = " . $q_ser_num . ";";
if ($conn->query($sql) !== true){
  echo "Failure to delete questionnaire record";
}

$conn->close();
?>
