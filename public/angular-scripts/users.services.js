'use strict';

var usersAppServices = angular.module('usersApp.services', ['ngResource']);

usersAppServices.factory('Users', ['$resource', function($resource){
    return $resource('users/users-resource/:id', {id: '@id'}, {
        'query': { method:'GET', isArray:true },
        'update': { method: 'PUT'},
        'save': { method: 'POST'}
    });
}]);


usersAppServices.factory('BespokeUsers', ['$http', '$q', function ($http, $q) {
    return {
        doAction: function(action, user, token) {
            var defer = $q.defer();

            $http.post('users/' + action + '/do-action', {user:user, _token: token}).success(function (data) {
                defer.resolve(data);
            }).error(function () {
                defer.reject('An error occurred');
            });

            return defer.promise;           
        },
    };
}]);


usersAppServices.factory('Miscellaneous', ['$localStorage', function ($localStorage) {

    return {
        highlightInput: function(selector, duration) { 
            var element = angular.element(selector).parent(); //We are highlighting the form-group which is a parent of the input element.

            element.addClass('has-error');
            setTimeout(function() {
                element.removeClass('has-error');
            }, duration);
        },

        addRemoveClass: function(selector, classesToRemove, classesToAdd) {
            angular.element(selector).removeClass(classesToRemove).addClass(classesToAdd);
        }

    };

}]);