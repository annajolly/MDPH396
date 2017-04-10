app.factory('tags', ['$http', function($http) {
  return $http({
      method: "post",
      url: "php/readInTags.php"
  })
  .success(function(data) {
    return data;
  })
  .error(function(data) {
    return data;
  });
}]);
