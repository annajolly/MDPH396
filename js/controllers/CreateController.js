app.controller('CreateController', ['$scope', 'sharedData', '$location', 'questionBank', 'answerTypes', 'tags', '$http', function($scope, sharedData, $location, questionBank, answerTypes, tags, $http) {
  $scope.doctorSerNum = sharedData.getDoctorSerNum();
  answerTypes.error(function(data) {
    alert(data);
  });
  answerTypes.success(function(data) {
    $scope.answerTypes = data;
    questionBank.error(function(data) {
      alert(data);
    });
    questionBank.success(function(data) {
      $scope.questionBank = data;
      tags.error(function(data) {
        alert(data);
      });
      tags.success(function(data) {
        $scope.tags = data;
        $scope.selectedTags = [];
        $scope.questionnaireTags = [];
        $scope.check = false;

        //------------------------------------------------------------------------
        // get questionnaire name and privacy if they have been saved previously
        $scope.questionnaireName = sharedData.getQuestionnaireName();
        $scope.questionnairePriv = sharedData.getQuestionnairePrivacy();
        $scope.questionnaireTags = sharedData.getQuestionnaireTags();
        // reset them
        sharedData.setQuestionnaireName("");
        sharedData.setQuestionnairePrivacy(-1);
        sharedData.setQuestionnaireTags([]);

        // if we came here from question-bank.html
        if (sharedData.getCameFrom() === '/questionbank/') {
          // reset variable
          sharedData.setCameFrom('');
          // get questionBank, name, and privacy if saved
          if (sharedData.getQuestionBank().length > 0) {
            $scope.questionBank = sharedData.getQuestionBank();
            // reset questionBank
            sharedData.setQuestionBank([]);
          }
        }

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

        //check if a library has one of the selected tags
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

        $scope.selectQuestionnaireTag = function() {
          //find tag object
          var tag;
          for (var i=0; i<$scope.tags.length; i++) {
            if ($scope.tags[i].name === $scope.selectedQTag) {
              tag = $scope.tags[i];
              break;
            }
          }
          $scope.questionnaireTags.push(tag);
          //put placeholder back
          $scope.selectedQTag = null;
        };
        $scope.unselectQuestionnaireTag = function(tag) {
          //find index of tag and remove it from array
          var removeIndex = $scope.questionnaireTags.map(function(item) {
            return item.ser_num;
          })
          .indexOf(tag.ser_num);
          ~removeIndex && $scope.questionnaireTags.splice(removeIndex, 1);
        };

        // check all button
        $scope.checkAll = function(category, check) {
          for (var i=0; i<category.groupings.length; i++) {
            category.groupings[i].include = check;
          }
        }

        // go to questionbank to create a questions
        $scope.goToQuestionBank = function() {
          sharedData.setQuestionnaireName($scope.questionnaireName);
          sharedData.setQuestionnairePrivacy($scope.questionnairePriv);
          sharedData.setQuestionnaireTags($scope.questionnaireTags);
          sharedData.setQuestionBank($scope.questionBank);
          sharedData.setBackTo('/create/');
          // change view
          $location.path('/questionbank/');
        }

        //---------------------------------------------------------------------

        // process the form
        $scope.submitQuestionnaire = function() {
          // check that the questionnaire is tagged
          if ($scope.questionnaireTags.length == 0) {
            $('#submitNoTagsModal').modal('show');
            return;
          }
          // get just included question groups
          $scope.questionGroups = [];
          // loop through libraries
          for(var i=0; i<$scope.questionBank.length; i++) {
            // loop through categories
            for (var j=0; j<$scope.questionBank[i].categories.length; j++) {
              // loop through groups
              for (var k=0; k<$scope.questionBank[i].categories[j].groupings.length; k++) {
                if ($scope.questionBank[i].categories[j].groupings[k].include == true) {
                  $scope.questionGroups.push($scope.questionBank[i].categories[j].groupings[k]);
                }
              }
            }
          }
          var request = $http({
              method: "post",
              url: "php/createQuestionnaire.php",
              data: {
                questionGroups: $scope.questionGroups,
                name: $scope.questionnaireName,
                privacy: $scope.questionnairePriv,
                tags: $scope.questionnaireTags,
                doctorSerNum: $scope.doctorSerNum
              },
              headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
              }
            });
          request.error(function() {
            alert("Looks like something went wrong. Please try again.");
          });
          // reset variables
          sharedData.setQuestionnaireName("");
          sharedData.setQuestionnairePrivacy(-1);
          // change view
          $location.path('/');
        };
      });
    });
  });
  // back button
  $scope.back = function() {
    $location.path('/');
  };
}]);
