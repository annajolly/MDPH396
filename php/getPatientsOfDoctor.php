<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "QuestionnaireDB";

class Questionnaire {
  public $qp_ser_num;
  public $q_ser_num;
  public $name;
  public $year;
  public $month;
  public $day;

  public function __construct($qp_ser_num, $q_ser_num, $name, $year, $month, $day) {
    $this->qp_ser_num = $qp_ser_num;
    $this->q_ser_num = $q_ser_num;
    $this->name = $name;
    $this->year = $year;
    switch ($month) {
    case 1:
        $this->month = "January";
        break;
    case 2:
        $this->month = "February";
        break;
    case 3:
        $this->month = "March";
        break;
    case 4:
        $this->month = "April";
        break;
    case 5:
        $this->month = "May";
        break;
    case 6:
        $this->month = "June";
        break;
    case 7:
        $this->month = "July";
        break;
    case 8:
        $this->month = "August";
        break;
    case 9:
        $this->month = "September";
        break;
    case 10:
        $this->month = "October";
        break;
    case 11:
        $this->month = "November";
        break;
    case 12:
        $this->month = "December";
        break;
    default:
        $this->month = "Error";
    }
    $this->day = $day;
  }
}

class Patient {
  public $ser_num;
  public $name;
  public $diagnosis;
  public $questionnaires;

  public function __construct($ser_num) {
    $this->ser_num = $ser_num;
    $this->name = "Patient Name";
    $this->diagnosis = "Patient Diagnosis";
    $this->questionnaires = array();
  }

  public function addQuestionnaire($questionnaire) {
    array_push($this->questionnaires, $questionnaire);
  }
}

// Get post data
$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$doctor_ser_num = $request->doctorSerNum;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$patients = array();

//get patients
$sql = "SELECT patient_ser_num FROM questionnaire_patient
WHERE user_ser_num = " . $doctor_ser_num . " GROUP BY patient_ser_num;";
$result = $conn->query($sql);
while($row = $result->fetch_assoc()) {
  $patient_ser_num = $row["patient_ser_num"];
  $patient = new Patient($patient_ser_num);
  //get patient's questionnaires
  $patientSQL = "SELECT questionnaire_patient.ser_num, questionnaire_patient.questionnaire_ser_num, questionnaire.name_en, questionnaire_patient.submitted
  FROM questionnaire_patient, questionnaire
  WHERE questionnaire_patient.questionnaire_ser_num = questionnaire.ser_num
  AND patient_ser_num = " . $patient_ser_num . "
  AND user_ser_num = " . $doctor_ser_num . ";";
  $patientResult = $conn->query($patientSQL);
  while($patientRow = $patientResult->fetch_assoc()) {
    $qp_ser_num = $patientRow["ser_num"];
    $q_ser_num = $patientRow["questionnaire_ser_num"];
    $q_name = $patientRow["name_en"];
    //parse date
    $parsedDate = date_parse($patientRow["submitted"]);
    $questionnaire = new Questionnaire($qp_ser_num, $q_ser_num, $q_name, $parsedDate["year"], $parsedDate["month"], $parsedDate["day"]);
    $patient->addQuestionnaire($questionnaire);
  }
  //push patient and their questionnaires
  array_push($patients, $patient);
}

//send back to controller
header('Content-Type: application/json');
//convert to json
echo json_encode($patients, JSON_UNESCAPED_SLASHES);

$conn->close();
?>
