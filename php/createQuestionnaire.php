<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "QuestionnaireDB2017";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get post data
$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$postgroups = $request->questionGroups;
$name = mysqli_real_escape_string($conn, $request->name);
$private = $request->privacy;
$tags = $request->tags;
$doctor_ser_num = $request->doctorSerNum;

// create new questionnaire
$sql = "INSERT INTO questionnaire (name_en, name_fr, private, publish, created_by)
VALUES ('" . $name . "', '" . $name . "', " . $private . ", 0, " . $doctor_ser_num . ");";
if ($conn->query($sql) === true) {
  //save questionnaire ser_num
  $questionnaire_id = mysqli_insert_id($conn);
  //loop through question groups, inserting them into questionnaire_questiongroup
  $i = 1;
  foreach($postgroups as $group) {
    // get question group ser_num
    $group_id = $group->ser_num;
    $optional = $group->optional;
    $sql = "INSERT INTO questionnaire_questiongroup (questionnaire_ser_num, questiongroup_ser_num, position)
    VALUES (" . $questionnaire_id . ", " . $group_id . ", " . $i . ");";
    if ($conn->query($sql) !== true){
      echo "Failure to insert questionnaire_questiongroup";
    }
    $i++;
  }
}
else {
  echo "Failure to insert questionnaire";
}

// add in questionnaire tags
foreach ($tags as $tag) {
  $sql = "INSERT INTO questionnaire_tag (questionnaire_ser_num, tag_ser_num, created_by)
  VALUES (" . $questionnaire_id . ", " . $tag->ser_num . ", " . $doctor_ser_num . ");";
  if ($conn->query($sql) !== true) {
    echo "Failure to insert questionnaire_tag record";
  }
}

$conn->close();
?>
