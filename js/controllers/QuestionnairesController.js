app.controller('QuestionnairesController', ['$scope', 'sharedData', 'tags', '$location', '$http', function($scope, sharedData, tags, $location, $http) {
  $scope.doctorSerNum = sharedData.getDoctorSerNum();
  tags.error(function(data) {
    alert(data);
  });
  tags.success(function(data) {
    $scope.tags = data;
    $scope.selectedTags = [];
    $scope.noTags = true;

    $scope.selectTag = function() {
      // find tag object
      var tag;
      for (var i=0; i<$scope.tags.length; i++) {
        if ($scope.tags[i].name === $scope.selectedTag) {
          tag = $scope.tags[i];
          break;
        }
      }
      $scope.selectedTags.push(tag);
      // put placeholder back
      $scope.selectedTag = null;
    };
    $scope.unselectTag = function(tag) {
      // find index of tag and remove it from array
      var removeIndex = $scope.selectedTags.map(function(item) {
        return item.ser_num;
      })
      .indexOf(tag.ser_num);
      ~removeIndex && $scope.selectedTags.splice(removeIndex, 1);
    };

    //check if a questionnaire has one of the selected tags
    $scope.hasTag = function(obj) {
      for (var i=0; i<obj.tags.length; i++) {
        for (var j=0; j<$scope.selectedTags.length; j++) {
          if (obj.tags[i].ser_num == $scope.selectedTags[j].ser_num) {
            return true;
          }
        }
      }
      return false;
    };

    // read in flag for success messages
    $scope.successPublish = sharedData.getSuccessPublish();
    // reset flag
    sharedData.setSuccessPublish(false);
    //http request to get questionnaires
    var request = $http({
      method: "post",
      url: "php/getQuestionnaires.php",
      data: {
        doctorSerNum: $scope.doctorSerNum
      },
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    });
    request.success(function(data) {
      $scope.questionnaires = data;
    });
    request.error(function() {
      alert("Error loading questionnaires.");
    });
    // go to edit.html
    $scope.goToEdit = function(questionnaireSerNum, questionnaireName, qPublish, qPrivate) {
      var q = {
        ser_num: questionnaireSerNum,
        name: questionnaireName,
        publish: qPublish,
        private: qPrivate
      };
      sharedData.setQuestionnaire(q);
      $location.path('/questionnaires/edit/');
    };
    // go to read.html
    $scope.goToRead = function(questionnaireSerNum, questionnaireName) {
      var q = {
        ser_num: questionnaireSerNum,
        name: questionnaireName
      };
      sharedData.setQuestionnaire(q);
      $location.path('/questionnaires/read/');
    };
    // back button
    $scope.back = function() {
      $location.path('/');
    };
  });
}]);
