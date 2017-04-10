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
$name = mysqli_real_escape_string($conn, $request->name);
$category = mysqli_real_escape_string($conn, $request->category);
$library_name = $request->library;
$privacy = $request->privacy;
$doctor_ser_num = $request->doctorSerNum;

// create new question group
$sql = "INSERT INTO questiongroup (name_en, name_fr, category_en, category_fr, private, created_by)
VALUES ('" . $name . "', '" . $name . "', '" . $category . "', '" . $category . "', " . $privacy . ", " . $doctor_ser_num . ");";
if ($conn->query($sql) === true) {
  $questiongroup_id = mysqli_insert_id($conn);
  // create new questiongroup_library entry
  $sql = "INSERT INTO questiongroup_library (questiongroup_ser_num, library_ser_num, created_by)
  VALUES (" . $questiongroup_id . ", " . $library_name . ", " . $doctor_ser_num . ");";
  if ($conn->query($sql) === true) {
    // add default tag of oncology
    $tagSQL = "INSERT INTO questiongroup_tag (questiongroup_ser_num, tag_ser_num, created_by)
    VALUES (" . $questiongroup_id . ", 1, " . $doctor_ser_num . ");";
    if ($conn->query($tagSQL) === true) {
      //send back to controller
      echo $questiongroup_id;
    }
    else {
      echo "Failure to insert questiongroup_tag record";
    }
  }
  else {
    echo "Failure to insert questiongroup_library record";
  }
}
else {
  echo "Failure to insert questiongroup record";
}

$conn->close();
?>
