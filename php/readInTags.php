<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "QuestionnaireDB2017";

class Tag {
  public $ser_num;
  public $level;
  public $ref_tag_ser_num;
  public $name;

  public function __construct($ser_num, $level, $ref_tag_ser_num, $name) {
    $this->ser_num = $ser_num;
    $this->level = $level;
    $this->ref_tag_ser_num = $ref_tag_ser_num;
    $this->name = $name;
  }
}

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$tags = array();

//read in tags
$sql = "SELECT ser_num, level, ref_tag_ser_num, name_en FROM tag;";
$result = $conn->query($sql);
while($row = $result->fetch_assoc()) {
  $ser_num = $row["ser_num"];
  $level = $row["level"];
  $ref_tag_ser_num = $row["ref_tag_ser_num"];
  $name = $row["name_en"];
  $tag = new Tag($ser_num, $level, $ref_tag_ser_num, $name);
  array_push($tags, $tag);
}

header('Content-Type: application/json');
//convert to json
echo json_encode($tags, JSON_UNESCAPED_SLASHES);

$conn->close();
?>
