'use strict';

var eventsAppServices = angular.module('eventsApp.services', ['ngResource']);

eventsAppServices.factory('Events', ['$resource', function($resource){
    return $resource('events/event-resource/:id', {id: '@id'}, {
        'query': { method:'GET', isArray:true },
        'update': { method: 'PUT'},
        'save': { method: 'POST'}
    });
}]);

eventsAppServices.factory('BespokeEvents', ['$http', '$q', function ($http, $q) {
    return {
        doBulkActions: function(action,ids, token) {
            var defer = $q.defer();

            $http.post('events/' + action + '/bulk-actions', {events:ids, _token: token}).success(function (data) {
                defer.resolve(data);
            }).error(function () {
                defer.reject('An error occurred');
            });

            return defer.promise;           
        }
    };
}]);


eventsAppServices.factory('Miscellaneous', ['$localStorage', function ($localStorage) {

    return {
        storeUser: function(userObj) {
            delete $localStorage.user;

            $localStorage.$default({
                user: userObj
            });
        },

        getUser: function() {
            return $localStorage.user;
        },

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