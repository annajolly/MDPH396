<?php
// POPULATE DB FROM symptoms.json
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

// get all questions as json object
$f = fopen("../../json/symptoms.json", "r") or die("Unable to open file!");
$file = file_get_contents("../../json/symptoms.json");
$json = json_decode($file);

// loop through libraries (die after CTCAE)
foreach ($json as $library) {
  $library_name = $library->class;
  if ($library_name == 'Extra') {
    $sql = "INSERT INTO library (name_en, name_fr, private)
    VALUES ('" . $library_name . "','" . $library_name . "', 0);";
    if ($conn->query($sql) === TRUE) {
        echo "New library record created successfully\n";
        //get ser_num of library
        $library_id = mysqli_insert_id($conn);
        //loop through categories of each library
        foreach ($library->categories as $category) {
          $category_name = $category->category;
          //loop through question groups (aka symptoms) of each category
          foreach ($category->symptoms as $symptom) {
            $questiongroup_name = $symptom->name;
            $sql = "INSERT INTO questiongroup (name_en, name_fr, category_en, category_fr, private)
            VALUES (" . "'" . $questiongroup_name . "'" . ", ". "'" . $questiongroup_name . "'" . ", "  . "'" . $category_name . "'" . ", ". "'" . $category_name . "'" . ", 0);";
            if ($conn->query($sql) === TRUE) {
                echo "New questiongroup record created successfully\n";
                $questiongroup_id = mysqli_insert_id($conn);
                $sql = "INSERT INTO questiongroup_library (questiongroup_ser_num, library_ser_num)
                VALUES ($questiongroup_id, $library_id)";
                if ($conn->query($sql) === TRUE) {
                  //loop through questions of each question group
                  foreach ($symptom->questions as $question) {
                    //find ser_num of question's answertype
                    $sql = "SELECT ser_num FROM answertype WHERE name_en=" . "'" . $question->type . "'" . ";";
                    $type_result = $conn->query($sql);
                    $type = $type_result->fetch_assoc();
                    $sql = "INSERT INTO question (text_en, text_fr, questiongroup_ser_num, questiontype_ser_num)
                    VALUES (" . "'" . $question->question . "'" . ", " . "'" . $question->question . "'" . ", " . $questiongroup_id . ", " . $type["ser_num"] . ");";
                    if ($conn->query($sql) === TRUE) {
                        echo "New question record created successfully\n";
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
            else {
                echo "Error: " . $sql . "<br>" . $conn->error . "\n";
            }
          }
        }
      }
      else {
        echo "Error: " . $sql . "<br>" . $conn->error . "\n";
      }
    die("Extra added");
  }
}

fclose($f);

$conn->close();
?>
