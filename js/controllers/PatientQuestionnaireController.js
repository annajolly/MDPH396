app.controller('PatientQuestionnaireController', ['$scope', 'sharedData', '$location', '$http', function($scope, sharedData, $location, $http) {
  $scope.doctorSerNum = sharedData.getDoctorSerNum();
  // get patient questionnaire object
  $scope.qp = sharedData.getPatientQuestionnaire();
  // reset variable
  sharedData.setPatientQuestionnaire({});
  //http request to get questionnaire
  var request = $http({
    method: "post",
    url: "php/getPatientCompletedQuestionnaire.php",
    data: {
      qpSerNum: $scope.qp.questionnaire.qp_ser_num,
      qSerNum: $scope.qp.questionnaire.q_ser_num,
      doctorSerNum: $scope.doctorSerNum
    },
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    }
  });
  request.success(function(data) {
    $scope.questionGroups = data;
  });
  request.error(function() {
    alert("Error loading questionnaire.");
  });
  //check if the option is equal to the answer
  $scope.isAnswer = function(option,answer) {
    var patt = new RegExp(answer);
    var res = patt.test(option);
    return res;
  };
  // back button
  $scope.back = function() {
    sharedData.setPatient($scope.qp.patient);
    $location.path('/patients/patient');
  };
}]);
