app.factory('questionBank', ['$http', function($http) {
  return $http({
      method: "post",
      url: "php/readInDB.php"
  })
  .success(function(data) {
    return data;
  })
  .error(function(data) {
    return data;
  });

  /* UNCOMMENT TO IMPORT FROM JSON IF DB NOT AVAILABLE
  return $http('/questionnaire-app/json/symptoms.json')
         .success(function(data) {
           return data;
         })
         .error(function(data) {
           return data;
         });
  */
}]);
