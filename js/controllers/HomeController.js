app.controller('HomeController', ['$scope', 'sharedData', '$location', function($scope, sharedData, $location) {
  $scope.goToCreate = function() {
    $location.path('/create/');
  };
  $scope.goToQuestionBank = function() {
    $location.path('/questionbank/');
  };
  $scope.goToRUD = function() {
    $location.path('/questionnaires/');
  };
  $scope.goToPatients = function() {
    $location.path('/patients/');
  };
}]);
