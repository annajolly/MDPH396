app.controller('QuestionBankController', ['$scope', 'sharedData', '$location', 'questionBank', 'answerTypes', 'tags', '$http', function($scope, sharedData, $location, questionBank, answerTypes, tags, $http) {
  $scope.doctorSerNum = sharedData.getDoctorSerNum();
  answerTypes.error(function() {
    alert("Could not read in answer types");
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
        //------------------------------------------------------------------------

        // answer type categories
        $scope.aTypeCategories = ["Multiple Choice", "Checkboxes", "Dropdown", "Linear Scale"];
        // some initializations
        $scope.newOptionsMinMax = {
          'min': 0,
          'max': 10
        };
        $scope.rangeValue = 5;
        $scope.ScaleCaptions = {
          'minCaption': null,
          'maxCaption': null
        };

        // if we came here from create.html
        if (sharedData.getBackTo() === '/create/') {
          $scope.cameFromCreate = true;
          // get questionBank
          $scope.questionBank = sharedData.getQuestionBank();
        }
        // if we came here from edit.html
        else if (sharedData.getBackTo() === '/questionnaires/edit/') {
          $scope.cameFromEdit = true;
          // get questionnaire info
          $scope.questionnaire = sharedData.getQuestionnaire();
          var questionGroups = sharedData.getQuestionnaireOptions();
          $scope.groupsToAdd = [];
          // mark questions we already have to disable them
          for (var i=0; i<$scope.questionBank.length; i++) {
            // loop through categories
            for (var j=0; j<$scope.questionBank[i].categories.length; j++) {
              // loop through question groups
              for (var k=0; k<$scope.questionBank[i].categories[j].groupings.length; k++) {
                for (var l=0; l<questionGroups.length; l++) {
                  if ($scope.questionBank[i].categories[j].groupings[k].ser_num === questionGroups[l].ser_num) {
                    $scope.questionBank[i].categories[j].groupings[k].include = true;
                  }
                }
              }
            }
          }
        }

        $scope.updatedCat = function() {
          // update groupings placeholder
          if ($scope.newQuestionCat != null) {
            if ($scope.newQuestionCat.groupings != null) {
              $scope.newQuestionGroup = $scope.newQuestionCat.groupings[0];
            }
          }
          else {
            $scope.newQuestionGroup = [];
          }
        };

        $scope.updatedLibrary = function() {
          // update category placeholder
          if ($scope.newQuestionLibrary.categories != null) {
            $scope.newQuestionCat = $scope.newQuestionLibrary.categories[0];
          }
          else {
            $scope.newQuestionCat = [];
          }
          $scope.updatedCat();
        };

        // find first available library
        for (var i=0; i<$scope.questionBank.length; i++) {
          if ($scope.questionBank[i].privateBool == 1 && $scope.questionBank[i].created_by == $scope.doctorSerNum) {
            $scope.newQuestionLibrary = $scope.questionBank[i];
            break;
          }
        }
        if ($scope.newQuestionLibrary != null) {
          $scope.updatedLibrary();
        }
        $scope.newQuestionType = $scope.answerTypes[0];
        $scope.selected = $scope.newQuestionType.options[0];
        $scope.newATypeCategory = $scope.aTypeCategories[0];
        $scope.newOptions = [{text:""}];

        $scope.addOption = function() {
          $scope.newOptions.push({text:""});
        }

        $scope.removeOption = function(index) {
          $scope.newOptions.splice(index,1);
        }

        $scope.registerOptions = function() {
          var filteredOptions = [];
          // if linear scale
          if ($scope.newATypeCategory == 'Linear Scale') {
            for (var i=$scope.newOptionsMinMax.min; i<=$scope.newOptionsMinMax.max; i++) {
              var string_i = i.toString();
              filteredOptions.push(string_i);
            }
          }
          else {
            // filter out empty options
            for (var i=0; i<$scope.newOptions.length; i++) {
              if ($scope.newOptions[i].text !== '') {
                filteredOptions.push($scope.newOptions[i].text);
              }
            }
          }
          // add new answer type and options to database
          var request = $http({
            method: "post",
            url: "php/addNewAnswerType.php",
            data: {
              category: $scope.newATypeCategory,
              options: filteredOptions,
              minCaption: $scope.ScaleCaptions.minCaption,
              maxCaption: $scope.ScaleCaptions.maxCaption,
              doctorSerNum: $scope.doctorSerNum
            },
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded'
            }
          });
          request.success(function(data) {
            // update type
            var currType = {
              ser_num: data,
              type: "Custom answer type",
              category: $scope.newATypeCategory,
              private: 1,
              num_options: filteredOptions.length,
              minCaption: $scope.ScaleCaptions.minCaption,
              maxCaption: $scope.ScaleCaptions.maxCaption,
              options: filteredOptions
            };
            $scope.answerTypes.push(currType);
            $scope.newQuestionType = currType;
          });
          request.error(function(data) {
            alert(data);
          });
          // reset variables
          //$scope.newATypeCategory = $scope.aTypeCategories[0];
          //$scope.newOptions = [{text:""}];
        }

        // TODO: make user add tags themselves
        var mainTag = {
          "ser_num": 1,
          "name": "Oncology"
        };

        // add a new library
        $scope.addNewLibrary = function(newLibraryName) {
          var nc = {
            "name": newLibraryName,
            "categories": [],
            "privateBool": 1,
            "created_by": $scope.doctorSerNum,
            "tags": [mainTag]
          };
          $scope.questionBank.push(nc);
          $scope.newQuestionLibrary = nc;
          $scope.newLibraryName = '';
          $scope.priv = 1;
          // add new library to database
          var request = $http({
            method: "post",
            url: "php/addLibrary.php",
            data: {
              name: newLibraryName,
              privacy: 1,
              doctorSerNum: $scope.doctorSerNum
            },
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded'
            }
          });
          request.success(function(data) {
            nc.ser_num = data;
          });
          request.error(function(data) {
            alert(data);
          });
          $scope.updatedLibrary();
        };

        // add a new category
        $scope.addNewCategory = function(newCatName) {
          var newcat = {
            "category": newCatName,
            "groupings": [],
            "tags": [mainTag]
          };
          $scope.newQuestionLibrary.categories.push(newcat);
          $scope.newQuestionCat = newcat;
          $scope.newCatName = '';
          $scope.updatedCat();
        };

        // add a new grouping
        $scope.addNewGrouping = function(newGroupName) {
          var newgroup = {
            "name": newGroupName,
            "questions": [],
            "tags": [mainTag],
            "include": true
          };
          $scope.newQuestionCat.groupings.push(newgroup);
          $scope.newQuestionGroup = newgroup;
          $scope.newGroupName = '';
          // add new library to database
          var request = $http({
            method: "post",
            url: "php/addQuestionGroup.php",
            data: {
              name: newGroupName,
              category: $scope.newQuestionCat.category,
              library: $scope.newQuestionLibrary.ser_num,
              privacy: 1,
              doctorSerNum: $scope.doctorSerNum
            },
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded'
            }
          });
          request.success(function(data) {
            newgroup.ser_num = data;
          });
          request.error(function() {
            alert("Looks like something went wrong.");
          });
        };

        // add a new question
        $scope.addNewQuestion = function(newQuestionName) {
          var newquestion = {
            "question": newQuestionName,
            "type": $scope.newQuestionType,
            "selected": $scope.selected
          };
          $scope.newQuestionGroup.questions.push(newquestion);
          $scope.newQuestionName = '';
          $scope.newQuestionType = $scope.answerTypes[0];
          // add new question to database
          var request = $http({
            method: "post",
            url: "php/addQuestion.php",
            data: {
              name: newquestion.question,
              type: newquestion.type.ser_num,
              group: $scope.newQuestionGroup.ser_num,
              doctorSerNum: $scope.doctorSerNum
            },
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded'
            }
          });
          request.success(function(data) {
            newquestion.ser_num = data;
            if ($scope.cameFromEdit) {
              //add question group to groupsToAdd
              $scope.groupsToAdd.push($scope.newQuestionGroup.ser_num);
            }
          });
          request.error(function() {
            alert("Looks like something went wrong.");
          });
          // collapse tabs
          $scope.showP = false;
          $scope.showNewLibrary = false;
          $scope.showNewCat = false;
          $scope.showNewGroup = false;
          $scope.showSuccess = !$scope.showSuccess;
          // scroll to top of page
          window.scrollTo(0, 0);
        };

        $scope.selectTag = function() {
          //find tag object
          var tag;
          for (var i=0; i<$scope.tags.length; i++) {
            if ($scope.tags[i].name === $scope.selectedTag) {
              tag = $scope.tags[i];
              break;
            }
          }
          $scope.selectedTags.push(tag);
          //put placeholder back
          $scope.selectedTag = null;
        };
        $scope.unselectTag = function(tag) {
          //find index of tag and remove it from array
          var removeIndex = $scope.selectedTags.map(function(item) {
            return item.ser_num;
          })
          .indexOf(tag.ser_num);
          ~removeIndex && $scope.selectedTags.splice(removeIndex, 1);
        };

        //check if an object has one of the selected tags
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

        // change button from plus to minus or vice-verse
        $scope.changeButton = function(toggle,id) {
          if (toggle) {
            // change to minus
            $(id).removeClass("fa-plus");
            $(id).addClass("fa-minus");
          }
          else {
            // change to plus
            $(id).removeClass("fa-minus");
            $(id).addClass("fa-plus");
          }
        }

        // show/hide questions when user clicks on dropdown button
        $scope.revealQuestions = function(grouping, f, ppid, pid, id) {
          // change icon from down to up or vice-verse
          var id = "#id" + "-" + f + "-" + ppid + "-" + pid + "-" + id;
          if ($(id).hasClass("fa-chevron-circle-down")) {
            // change to up
            $(id).removeClass("fa-chevron-circle-down");
            $(id).addClass("fa-chevron-circle-up");
          }
          else {
            // change to plus
            $(id).removeClass("fa-chevron-circle-up");
            $(id).addClass("fa-chevron-circle-down");
          }
          // reveal questions
          grouping.reveal = !grouping.reveal;
        }

        $scope.addGroupsToQuestionnaire = function() {
          // loop through libraries
          for(var i=0; i<$scope.questionBank.length; i++) {
            // loop through categories
            for (var j=0; j<$scope.questionBank[i].categories.length; j++) {
              // loop through question groups
              for (var k=0; k<$scope.questionBank[i].categories[j].groupings.length; k++) {
                if ($scope.questionBank[i].categories[j].groupings[k].add === true) {
                  // add to database
                  var request = $http({
                    method: "post",
                    url: "php/addQuestionGroupToQuestionnaire.php",
                    data: {
                      questionnaire_id: $scope.questionnaire.ser_num,
                      questiongroup_ser_num: $scope.questionBank[i].categories[j].groupings[k].ser_num,
                      doctor_id: $scope.doctorSerNum
                    },
                    headers: {
                      'Content-Type': 'application/x-www-form-urlencoded'
                    }
                  });
                  request.error(function(data) {
                    console.log(data);
                    alert("Looks like something went wrong.");
                  });
                }
              }
            }
          }
          // change view
          $location.path('/questionnaires/edit/');
        };

        // go back to create.html
        $scope.backToCreate = function() {
          // set cameFrom
          sharedData.setCameFrom('/questionbank/');
          // reset backTo
          sharedData.setBackTo('');
          // set questionbank
          sharedData.setQuestionBank($scope.questionBank);
          // go back to create.html
          $location.path('/create/');
        };

        // go back to create.html
        $scope.backToEdit = function() {
          // set cameFrom
          sharedData.setCameFrom('/questionbank/');
          // reset backTo
          sharedData.setBackTo('');
          // set questionnaire
          sharedData.setQuestionnaire($scope.questionnaire);
          // reset questionnaire options
          sharedData.setQuestionnaireOptions([]);
          // go back to rud.html
          $location.path('/questionnaires/edit/');
        };

      });
    });
  });
  // back button
  $scope.back = function() {
    if ($scope.cameFromCreate) {
      // set cameFrom
      sharedData.setCameFrom('/questionbank/');
      //reset backTo
      sharedData.setBackTo('');
      $location.path('/create/');
    }
    else if ($scope.cameFromEdit) {
      // set cameFrom
      sharedData.setCameFrom('/questionbank/');
      //reset backTo
      sharedData.setBackTo('');
      $location.path('/questionnaires/edit/');
    }
    else {
      $location.path('/');
    }
  };
}]);
