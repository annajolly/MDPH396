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
$cat = mysqli_real_escape_string($conn, $request->category);
$options = $request->options;
$doctor_ser_num = $request->doctorSerNum;

// create new answertype
$sql = "INSERT INTO answertype (category_en, category_fr, private, created_by)
VALUES ('" . $cat . "', '" . $cat . "', 1, " . $doctor_ser_num . ");";
if ($conn->query($sql) === true) {
  $answertype_ser_num = mysqli_insert_id($conn);
  // add options
  $i = 0;
  foreach($options as $option) {
    $opt = mysqli_real_escape_string($conn, $option);
    $i++;
    $sql = "INSERT INTO answeroption (text_en, text_fr, answertype_ser_num, position, created_by)
    VALUES ('" . $opt . "', '" . $opt . "', " . $answertype_ser_num . ", " . $i . ", " . $doctor_ser_num . ");";
    if ($conn->query($sql) !== true) {
      //send back to controller
      echo "Failed to insert answeroption record.";
    }
  }
  echo $answertype_ser_num;
}
else {
  echo "Failed to insert answertype record.";
}

$conn->close();
?>
