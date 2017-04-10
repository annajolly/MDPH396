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
$name = mysqli_real_escape_string($conn,$request->name);
$type = $request->type;
$group = $request->group;
$doctor_ser_num = $request->doctorSerNum;

// create new question
$sql = "INSERT INTO question (text_en, text_fr, questiongroup_ser_num, answertype_ser_num, created_by)
VALUES ('" . $name . "', '" . $name . "', " . $group . ", " . $type . ", " . $doctor_ser_num . ");";
if ($conn->query($sql) === true) {
  $question_id = mysqli_insert_id($conn);
  //send back to controller
  echo $question_id;
}
else {
  echo "Failure to insert question record";
}

$conn->close();
?>
