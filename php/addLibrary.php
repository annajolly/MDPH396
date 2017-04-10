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
$privacy = $request->privacy;
$doctor_ser_num = $request->doctorSerNum;

// Create new library
$sql = "INSERT INTO library (name_en, name_fr, private, created_by)
VALUES (" . "'" . $name . "', '" . $name . "', " . $privacy . ", " . $doctor_ser_num . ");";
if ($conn->query($sql) === true) {
  $library_id = mysqli_insert_id($conn);
  //send back to controller
  echo $library_id;
}
else {
  echo "Failure";
}

$conn->close();
?>
