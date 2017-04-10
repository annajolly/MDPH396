app.controller('PatientController', ['$scope', 'sharedData', '$location', '$http', function($scope, sharedData, $location, $http) {
  $scope.doctorSerNum = sharedData.getDoctorSerNum();
  
  // get patient object
  $scope.patient = sharedData.getPatient();
  // reset variable
  sharedData.setPatient({});

  $scope.viewQuestionnaire = function(p,q) {
    //push questionnaire and patient info to global stack
    var qp = {
      patient: p,
      questionnaire: q
    };
    sharedData.setPatientQuestionnaire(qp);
    $location.path('/patients/patient/questionnaire/');
  };
  // back button
  $scope.back = function() {
    $location.path('/patients/');
  };

}]);
