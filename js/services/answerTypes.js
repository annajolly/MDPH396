app.factory('answerTypes', ['$http', function($http) {
  return $http({
      method: "post",
      url: "php/readInAnswerTypes.php"
  })
  .success(function(data) {
    return data;
  })
  .error(function(data) {
    return data;
  });

  /* UNCOMMENT TO IMPORT FROM JSON IF DB NOT AVAILABLE
  return $http.get('/questionnaire-app/json/answer-types.json')
         .success(function(data) {
           return data;
         })
         .error(function(data) {
           return data;
         });
  */
}]);
