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

class Question {
  public $ser_num;
  public $question;
  public $type;

  public function __construct($ser_num, $name, $type) {
    $this->ser_num = $ser_num;
    $this->question = $name;
    $this->type = $type;
  }
}

class QuestionGroup {
  public $ser_num;
  public $name;
  public $questions;

  public function __construct($ser_num, $name_en) {
    $this->ser_num = $ser_num;
    $this->name = $name_en;
    $this->questions = array();
  }

  public function addQuestion($ques) {
    array_push($this->questions, $ques);
  }
}

// Get post data
$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$questionnaire_id = $request->id;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$questionGroups = array();

//get question groups
$sql = "SELECT questiongroup.ser_num, questiongroup.name_en
FROM questionnaire, questionnaire_questiongroup, questiongroup
WHERE questionnaire.ser_num = questionnaire_questiongroup.questionnaire_ser_num
AND questionnaire_questiongroup.questiongroup_ser_num = questiongroup.ser_num
AND questionnaire.ser_num = " . $questionnaire_id . ";";
$result = $conn->query($sql);
while($row = $result->fetch_assoc()) {
  $questionGroup_id = $row["ser_num"];
  $questionGroup_name = $row["name_en"];
  $questionGroup = new QuestionGroup($questionGroup_id, $questionGroup_name);
  array_push($questionGroups, $questionGroup);
  //get questions for each question group
  $qSQL = "SELECT question.ser_num, question.text_en, question.answertype_ser_num
  FROM question, answertype WHERE question.answertype_ser_num = answertype.ser_num
  AND question.questiongroup_ser_num = " . $questionGroup_id . ";";
  $qResult = $conn->query($qSQL);
  while($qRow = $qResult->fetch_assoc()) {
    $q_ser_num = $qRow["ser_num"];
    $type_ser_num = $qRow["answertype_ser_num"];
    // read in answer type and options for each question
    // read in answer types
    $qtSQL = "SELECT ser_num, name_en, private, category_en, created_by FROM answertype
    WHERE ser_num = " . $type_ser_num . ";";
    $qtResult = $conn->query($qtSQL);
    $qtRow = $qtResult->fetch_assoc();
    $qtSerNum = $qtRow["ser_num"];
    $qtName = $qtRow["name_en"];
    $qtPrivate = $qtRow["private"];
    $qtCat = $qtRow["category_en"];
    $curr_qt = new AnswerType($qtSerNum, $qtName, $qtPrivate, $qtCat);
    $question = new Question($q_ser_num, $qRow["text_en"], $curr_qt);
    $questionGroup->addQuestion($question);
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
}

//send back to controller
header('Content-Type: application/json');
//convert to json
echo json_encode($questionGroups, JSON_UNESCAPED_SLASHES);

$conn->close();
?>
