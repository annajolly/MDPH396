<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "QuestionnaireDB";

class Tag {
  public $ser_num;
  public $name;

  public function __construct($ser_num, $name) {
    $this->ser_num = $ser_num;
    $this->name = $name;
  }
}

class Questionnaire {
  public $ser_num;
  public $name;
  public $private;
  public $publish;
  public $created_by;
  public $tags;

  public function __construct($ser_num, $name, $private, $publish, $created_by) {
    $this->ser_num = $ser_num;
    $this->name = $name;
    $this->private = $private;
    $this->publish = $publish;
    $this->created_by = $created_by;
    $this->tags = array();
  }

  public function addTag($tag) {
    array_push($this->tags,$tag);
  }
}

// Get post data
$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$doctor_id = $request->doctorSerNum;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$questionnaires = array();

// get question groups
$sql = "SELECT ser_num, name_en, private, publish, created_by FROM questionnaire WHERE private = 0 OR created_by = " . $doctor_id . ";";
$result = $conn->query($sql);
while($row = $result->fetch_assoc()) {
  $questionnaire = new Questionnaire($row["ser_num"], $row["name_en"], $row["private"], $row["publish"], $row["created_by"]);
  array_push($questionnaires, $questionnaire);
  // get tags
  $tagSQL = "SELECT tag_ser_num, name_en FROM questionnaire_tag, tag
  WHERE questionnaire_tag.tag_ser_num = tag.ser_num
  AND questionnaire_tag.questionnaire_ser_num = " . $row["ser_num"] . ";";
  $tagResult = $conn->query($tagSQL);
  while($tagRow = $tagResult->fetch_assoc()) {
    $tag = new Tag($tagRow["tag_ser_num"], $tagRow["name_en"]);
    $questionnaire->addTag($tag);
  }
}

//send back to controller
header('Content-Type: application/json');
//convert to json
echo json_encode($questionnaires, JSON_UNESCAPED_SLASHES);

$conn->close();
?>
