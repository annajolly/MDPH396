<?php
// POPULATE ANSWERTYPE TABLE FROM ESASat.json
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

// get question-types json object
$f = fopen("../../json/ESASat.json", "r") or die("Unable to open file!");
$qt_file = file_get_contents("../../json/ESASat.json");
$qt_json = json_decode($qt_file);

// populate database tables answertype and answeroption
foreach ($qt_json as $answer_type) {
  $qt_type = $answer_type->type;
  $sql = "INSERT INTO answertype (name_en, name_fr, private)
  VALUES (" . "'" . $qt_type . "'" . ", ". "'" . $qt_type . "'" . ", 0)";
  if ($conn->query($sql) === TRUE) {
      echo "New answertype record created successfully\n";
      //get ser_num of answertype
      $id = mysqli_insert_id($conn);
      $i = 0;
      //insert options
      foreach ($answer_type->options as $qt_option) {
        $i++;
        $sql = "INSERT INTO answeroption (text_en, text_fr, answertype_ser_num, position)
        VALUES (" . "'" . $qt_option . "'" . ", ". "'" . $qt_option . "'" . ", " . $id . "," . $i . ")";
        if ($conn->query($sql) === TRUE) {
            echo "New option record created successfully\n";
        }
        else {
            echo "Error: " . $sql . "<br>" . $conn->error . "\n";
        }
      }
  }
  else {
      echo "Error: " . $sql . "<br>" . $conn->error . "\n";
  }
}

fclose($f);

$conn->close();
?>
