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

// create tables
$sql = "CREATE TABLE IF NOT EXISTS questiongroup_tag (
  questiongroup_ser_num bigint(20) unsigned,
  tag_ser_num int(11) unsigned,
  created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  last_updated timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  created_by int(11) unsigned,
  last_updated_by int(11) unsigned,
  PRIMARY KEY (questiongroup_ser_num, tag_ser_num),
  FOREIGN KEY (questiongroup_ser_num) REFERENCES questiongroup(ser_num),
  FOREIGN KEY (tag_ser_num) REFERENCES tag(ser_num)
);";

if ($conn->query($sql) === TRUE) {
    echo "Table questiongroup_tag created successfully";
}
else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>
