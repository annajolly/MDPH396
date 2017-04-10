app.controller('EditController', ['$scope', 'sharedData', '$location', '$http', function($scope, sharedData, $location, $http) {
  $scope.doctorSerNum = sharedData.getDoctorSerNum();
  $scope.questionnaire = sharedData.getQuestionnaire();
  // reset variable
  sharedData.setQuestionnaire({
    id: -1,
    name: ""
  });
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
  // delete a question group
  $scope.deleteGrouping = function(questionGroupSerNum) {
    //remove question group from database
    var request = $http({
      method: "post",
      url: "php/removeQuestionGroupFromQuestionnaire.php",
      data: {
        questionnaire_id: $scope.questionnaire.ser_num,
        questiongroup_id: questionGroupSerNum,
        doctor_id: $scope.doctorSerNum
      },
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    });
    request.success(function(data) {
      //remove question group from local data structure
      var removeIndex = $scope.questionGroups.map(function(item) {
        return item.ser_num;
      }).indexOf(questionGroupSerNum);
      //if removeIndex is not -1, splice out question group
      ~removeIndex && $scope.questionGroups.splice(removeIndex, 1);
    })
    request.error(function(data) {
      console.log(data);
    });
  };
  // publish questionnaire and close modal
  $scope.publish = function(questionnaire) {
    // publish questionnaire
    var request = $http({
      method: "post",
      url: "php/publishQuestionnaire.php",
      data: {
        questionnaire_ser_num: questionnaire.ser_num
      },
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    });
    request.error(function(data) {
      alert(data);
    });
    // change local DS
    questionnaire.publish = 1;
    // close modal
    $('#publishModal').modal('hide');
    // set flag true to display success message
    sharedData.setSuccessPublish(true);
    // reset questionnaire DS
    sharedData.setQuestionnaire({
      id: -1,
      name: ""
    });
    //back to rud.html
    $location.path('/questionnaires/');
  };
  $scope.cancelPublish = function() {
    $('#publishModal').modal('hide');
  };

  // add question groups
  $scope.addQuestionGroups = function() {
    // store questionnaire info
    sharedData.setQuestionnaire($scope.questionnaire);
    // store questiongroups
    sharedData.setQuestionnaireOptions($scope.questionGroups);
    // set cameFrom
    sharedData.setBackTo('/questionnaires/edit/');
    // go to question-bank.html
    $location.path('/questionbank/');
  }

  // delete questionnaire, close modal, and go back to rud.html
  $scope.deleteQuestionnaire = function(questionnaireSerNum) {
    // publish questionnaire
    var request = $http({
      method: "post",
      url: "php/deleteQuestionnaire.php",
      data: {
        questionnaire_ser_num: questionnaireSerNum
      },
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    });
    request.error(function(data) {
      alert(data);
    });
    // close modal
    $('#deleteModal').modal('hide');
    // reset questionnaire DS
    sharedData.setQuestionnaire({
      id: -1,
      name: ""
    });
    //go back to rud.html
    $location.path('/questionnaires/');
  };

  $scope.cancelDelete = function() {
    $('#deleteModal').modal('hide');
  };

  $scope.back = function() {
    // reset questionnaire DS
    sharedData.setQuestionnaire({
      id: -1,
      name: ""
    });
    // go to rud.html
    $location.path('/questionnaires/');
  };

}]);
