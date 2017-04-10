app.controller('PatientsController', ['$scope', 'sharedData', '$location', '$http', function($scope, sharedData, $location, $http) {
  $scope.doctorSerNum = sharedData.getDoctorSerNum();
  //http request to get patients and their questionnaires
  var request = $http({
    method: "post",
    url: "php/getPatientsOfDoctor.php",
    data: {
      doctorSerNum: $scope.doctorSerNum
    },
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    }
  });
  request.success(function(data) {
    $scope.patients = data;
  });
  request.error(function() {
    alert("Error loading patients.");
  });
  $scope.select = function(patient) {
    sharedData.setPatient(patient);
    $location.path('patients/patient');
  };
  // back button
  $scope.back = function() {
    $location.path('/');
  };
}]);
