<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "QuestionnaireDB2017";

class Tag {
  public $ser_num;
  public $name;

  public function __construct($ser_num, $name) {
    $this->ser_num = $ser_num;
    $this->name = $name;
  }
}

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
  public $selected;

  public function __construct($ser_num, $name, $type, $selected) {
    $this->ser_num = $ser_num;
    $this->question = $name;
    $this->type = $type;
    $this->selected = $selected;
  }
}

class Grouping {
  public $ser_num;
  public $name;
  public $questions;
  public $include;
  public $tags;
  public $reveal;

  public function __construct($ser_num, $name) {
    $this->ser_num = $ser_num;
    $this->name = $name;
    $this->questions = array();
    $this->include = false;
    $this->tags = array();
    $this->reveal = false;
  }

  public function addQuestion($ques) {
    array_push($this->questions,$ques);
  }

  public function addTag($tag) {
    array_push($this->tags,$tag);
  }
}

class Category {
  public $category;
  public $groupings;
  public $tags;

  public function __construct($name) {
    $this->category = $name;
    $this->groupings = array();
    $this->tags = array();
  }

  public function addGrouping($group) {
    array_push($this->groupings,$group);
  }

  public function addTag($t) {
    //check if already exists
    $alreadyExists = false;
    foreach ($this->tags as $tag) {
      if ($t->ser_num == $tag->ser_num) {
        $alreadyExists = true;
        break;
      }
    }
    if(!$alreadyExists) {
      array_push($this->tags,$t);
    }
  }
}

class Library {
  public $ser_num;
  public $name;
  public $categories;
  public $privateBool;
  public $created_by;
  public $tags;

  public function __construct($ser_num, $name, $private, $created_by) {
    $this->ser_num = $ser_num;
    $this->name = $name;
    $this->categories = array();
    $this->privateBool = $private;
    $this->created_by = $created_by;
    $this->tags = array();
  }

  public function addCategory($cat) {
    array_push($this->categories,$cat);
  }

  public function addTag($t) {
    //check if already exists
    $alreadyExists = false;
    foreach ($this->tags as $tag) {
      if ($t->ser_num == $tag->ser_num) {
        $alreadyExists = true;
        break;
      }
    }
    if(!$alreadyExists) {
      array_push($this->tags,$t);
    }
  }
}

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$groupings = array();

//read in libraries
$librarySQL = "SELECT ser_num, name_en, private, created_by FROM library;";
$libraryResult = $conn->query($librarySQL);
while($libraryRow = $libraryResult->fetch_assoc()) {
  $librarySerNum = $libraryRow["ser_num"];
  $libraryName = $libraryRow["name_en"];
  $curr_library = new Library($librarySerNum, $libraryName,$libraryRow["private"],$libraryRow["created_by"]);
  array_push($groupings, $curr_library);
  //read in categories for each library
  $catSQL = "SELECT category_en
  FROM questiongroup_library, questiongroup, library
  WHERE questiongroup_library.questiongroup_ser_num = questiongroup.ser_num
  AND questiongroup_library.library_ser_num = library.ser_num
  AND library.ser_num = " . $librarySerNum . " GROUP BY questiongroup.category_en;";
  $catResult = $conn->query($catSQL);
  while($catRow = $catResult->fetch_assoc()) {
    $catName = $catRow["category_en"];
    $curr_cat = new Category($catName);
    $curr_library->addCategory($curr_cat);
    //read in all the question groups for each category
    $groupSQL = "SELECT questiongroup.ser_num, questiongroup.name_en
    FROM questiongroup, questiongroup_library
    WHERE questiongroup.ser_num = questiongroup_library.questiongroup_ser_num
    AND questiongroup.category_en = " . "'" . $catName . "'" . "AND questiongroup_library.library_ser_num = " . $librarySerNum . ";";
    $groupResult = $conn->query($groupSQL);
    while($groupRow = $groupResult->fetch_assoc()) {
      $groupSerNum = $groupRow["ser_num"];
      $groupName = $groupRow["name_en"];
      $curr_group = new Grouping($groupSerNum, $groupName);
      /*if (strcmp($groupName,"OtherY/N") == 0) {
        $curr_group->include = true;
      }*/
      $curr_cat->addGrouping($curr_group);
      //read in all the questions with options for each question group
      // BUG BELOW --> ONLY RETURNS ENTRIES W/ ANSWEROPTIONS
      $questionSQL = "SELECT question.ser_num, question.text_en AS qtext, answertype.ser_num AS at_ser_num, answeroption.text_en AS qotext
      FROM question, questiongroup, answertype, answeroption
      WHERE question.questiongroup_ser_num = questiongroup.ser_num
      AND question.answertype_ser_num = answertype.ser_num
      AND answertype.ser_num = answeroption.answertype_ser_num
      AND questiongroup.ser_num = " . $groupSerNum . " AND answeroption.position = 1;";
      $questionResult = $conn->query($questionSQL);
      while($questionRow = $questionResult->fetch_assoc()) {
        $serNum = $questionRow["ser_num"];
        $question = $questionRow["qtext"];
        $type_ser_num = $questionRow["at_ser_num"];
        $selected = $questionRow["qotext"];
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
        $curr_question = new Question($serNum, $question, $curr_qt, $selected);
        $curr_group->addQuestion($curr_question);
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
        else if ($qtCat == 'Short Answer' || $qtCat == 'Date' || $qtCat == 'Time') {
          // no options
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
      // if the question wasn't read in because it has type date, time, or short answer
      $questionSQL = "SELECT question.ser_num, question.text_en AS qtext, answertype.ser_num AS at_ser_num
      FROM question, questiongroup, answertype
      WHERE question.questiongroup_ser_num = questiongroup.ser_num
      AND question.answertype_ser_num = answertype.ser_num
      AND (answertype.ser_num = 36
      OR answertype.ser_num = 37
      OR answertype.ser_num = 38)
      AND questiongroup.ser_num = " . $groupSerNum . ";";
      $questionResult = $conn->query($questionSQL);
      while($questionRow = $questionResult->fetch_assoc()) {
        $serNum = $questionRow["ser_num"];
        $question = $questionRow["qtext"];
        $type_ser_num = $questionRow["at_ser_num"];
        // read in answer type
        $qtSQL = "SELECT ser_num, name_en, private, category_en, created_by FROM answertype
        WHERE ser_num = " . $type_ser_num . ";";
        $qtResult = $conn->query($qtSQL);
        $qtRow = $qtResult->fetch_assoc();
        $qtSerNum = $qtRow["ser_num"];
        $qtName = $qtRow["name_en"];
        $qtPrivate = $qtRow["private"];
        $qtCat = $qtRow["category_en"];
        $curr_qt = new AnswerType($qtSerNum, $qtName, $qtPrivate, $qtCat);
        $curr_question = new Question($serNum, $question, $curr_qt, "");
        $curr_group->addQuestion($curr_question);
      }
      //read in all the tags for each question group
      $tagSQL = "SELECT tag.ser_num, tag.name_en
      FROM questiongroup, questiongroup_tag, tag
      WHERE questiongroup.ser_num = questiongroup_tag.questiongroup_ser_num
      AND questiongroup_tag.tag_ser_num = tag.ser_num
      AND questiongroup.ser_num = " . $groupSerNum . ";";
      $tagResult = $conn->query($tagSQL);
      while($tagRow = $tagResult->fetch_assoc()) {
        $tag_ser_num = $tagRow["ser_num"];
        $tag_name = $tagRow["name_en"];
        $tag = new Tag($tag_ser_num, $tag_name);
        $curr_group->addTag($tag);
        //add tag to category and library as well
        $curr_cat->addTag($tag);
        $curr_library->addTag($tag);
      }
    }
  }
}

header('Content-Type: application/json');
//convert to json
echo json_encode($groupings, JSON_UNESCAPED_SLASHES);

$conn->close();
?>
