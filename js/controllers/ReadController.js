app.controller('ReadController', ['$scope', 'sharedData', '$location', '$http', function($scope, sharedData, $location, $http) {
  $scope.doctorSerNum = sharedData.getDoctorSerNum();
  // get questionnaire info
  $scope.questionnaire = sharedData.getQuestionnaire();
  // reset questionnaire info
  var q = {
    ser_num: -1,
    name: ""
  };
  sharedData.setQuestionnaire(q);
  //http request to get questiongroups
  var request = $http({
    method: "post",
    url: "php/getQuestionGroups.php",
    data: {
      id: $scope.questionnaire.ser_num
    },
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    }
  });
  request.success(function(data) {
    $scope.questionGroups = data;
  })
  request.error(function() {
    alert("Looks like something went wrong.");
  });
  $scope.back = function() {
    $location.path('/questionnaires/');
  };
}]);
