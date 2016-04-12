'use strict';

/**
 * Factory in the todoWordpressApp.
 */
angular.module('todoWordpressApp')
  .factory('wpFactory', function ($http, $q) {
    var url = 'http://localhost/wp-rest-api/wp-json/wp/v2/';

    function getPosts(number) {
      return ($http.get(url + 'posts?per_page=' + number).then(handleSuccess, handleError));
    }

    function getMediaDataForId(id) {
      return ($http.get(url + 'media/' + id, {ignoreLoadingBar: true}).then(handleSuccess, handleError));
    }

    function handleSuccess(response) {
      return response.data;
    }

    function handleError(response) {
      if (!angular.isObject(response.data) || !response.data.message) {
        return ($q.reject("An unknown error occured."));
      }
      return ($q.reject(response.data.message));
    }
    return ({
      getPosts: getPosts,
      getMediaDataForId: getMediaDataForId
    });
  });
