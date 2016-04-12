'use strict';

/**
 * Controller of the todoWordpressApp
 */
angular.module('todoWordpressApp')
  .controller('MainCtrl', function ($scope, wpFactory) {
    $scope.posts = [];
    $scope.images = {};

    wpFactory.getPosts(5).then(function (succ) {
      $scope.posts = succ;
      angular.forEach(succ, function(value, index) {
        $scope.setUrlForImage(index, value.featured_image);
      });
    }, function (err) {
      console.log('Error: ', err);
    });

    $scope.setUrlForImage = function(index, id) {
      wpFactory.getMediaDataForId(id).then(function (succ) {
        $scope.images[index] = succ.source_url;
      });
    };
  });
