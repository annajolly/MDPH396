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

//tag EPIC as prostate cancer
for ($i = 101; $i <= 139; $i++) {
  $sql = "INSERT INTO questiongroup_tag (questiongroup_ser_num, tag_ser_num) VALUES (" . $i . ", 4);";
  if ($conn->query($sql) === true) {
    echo $i . " now tagged with prostate cancer";
  }
  else {
    die("Error at " . $i);
  }
}

//tag everything as oncology and medical oncology
for ($i = 1; $i <= 81; $i++) {
  $sql1 = "INSERT INTO questiongroup_tag (questiongroup_ser_num, tag_ser_num) VALUES (" . $i . ", 1);";
  if ($conn->query($sql1) === true) {
    echo $i . " now tagged with oncology";
  }
  else {
    die("Error at " . $i);
  }
  $sql2 = "INSERT INTO questiongroup_tag (questiongroup_ser_num, tag_ser_num) VALUES (" . $i . ", 2);";
  if ($conn->query($sql2) === true) {
    echo $i . " now tagged with medical oncology";
  }
  else {
    die("Error at " . $i);
  }
}

for ($i = 91; $i <= 139; $i++) {
  $sql1 = "INSERT INTO questiongroup_tag (questiongroup_ser_num, tag_ser_num) VALUES (" . $i . ", 1);";
  if ($conn->query($sql1) === true) {
    echo $i . " now tagged with oncology";
  }
  else {
    die("Error at " . $i);
  }
  $sql2 = "INSERT INTO questiongroup_tag (questiongroup_ser_num, tag_ser_num) VALUES (" . $i . ", 2);";
  if ($conn->query($sql2) === true) {
    echo $i . " now tagged with medical oncology";
  }
  else {
    die("Error at " . $i);
  }
}

$conn->close();
?>
