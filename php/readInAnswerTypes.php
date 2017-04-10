<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "QuestionnaireDB";

class AnswerType {
  public $ser_num;
  public $type;
  public $private;
  public $category;
  public $num_options;
  public $minCaption;
  public $maxCaption;
  public $options;

  public function __construct($ser_num, $type, $private, $category) {
    $this->ser_num = $ser_num;
    $this->type = $type;
    $this->private = $private;
    $this->category = $category;
    $this->num_options = 0;
    $this->options = array();
  }

  function setMinCaption($caption) {
    $this->minCaption = $caption;
  }

  function setMaxCaption($caption) {
    $this->maxCaption = $caption;
  }

  function setAndSwitchMinCaptions($caption) {
    $this->maxCaption = $this->minCaption;
    $this->minCaption = $caption;
  }

  public function addOption($opt) {
    $this->num_options++;
    array_push($this->options,$opt);
  }
}

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$answerTypes = array();

//read in answer types
$qtSQL = "SELECT ser_num, name_en, private, category_en, created_by FROM answertype;";
$qtResult = $conn->query($qtSQL);
while($qtRow = $qtResult->fetch_assoc()) {
  $qtSerNum = $qtRow["ser_num"];
  $qtName = $qtRow["name_en"];
  $qtPrivate = $qtRow["private"];
  $qtCat = $qtRow["category_en"];
  $curr_qt = new AnswerType($qtSerNum, $qtName, $qtPrivate, $qtCat);
  array_push($answerTypes, $curr_qt);
  //read in options for each answer type
  if ($qtCat == 'Linear Scale') {
    $optionSQL = "SELECT text_en, caption_en
    FROM answeroption
    WHERE answeroption.answertype_ser_num = " . $qtSerNum . ";";
    $optionResult = $conn->query($optionSQL);
    $min = null;
    while($optionRow = $optionResult->fetch_assoc()) {
      $curr_qt->addOption($optionRow["text_en"]);
      // set min if first caption
      if ($optionRow["caption_en"] != null && $min == null) {
        $min = $optionRow["text_en"];
        $curr_qt->setMinCaption($optionRow["caption_en"]);
      }
      // check that second caption is max caption and set it
      else if ($optionRow["caption_en"] != null && $min != null) {
        if ($min > $optionRow["text_en"]) {
          $curr_qt->setAndSwitchMinCaptions($optionRow["caption_en"]);
        }
        else {
          $curr_qt->setMaxCaption($optionRow["caption_en"]);
        }
      }
    }
  }
  else {
    $optionSQL = "SELECT text_en
    FROM answeroption
    WHERE answeroption.answertype_ser_num = " . $qtSerNum . ";";
    $optionResult = $conn->query($optionSQL);
    while($optionRow = $optionResult->fetch_assoc()) {
      $curr_qt->addOption($optionRow["text_en"]);
    }
  }
}

header('Content-Type: application/json');
//convert to json
echo json_encode($answerTypes, JSON_UNESCAPED_SLASHES);

$conn->close();
?>
