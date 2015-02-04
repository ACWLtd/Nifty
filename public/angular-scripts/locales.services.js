'use strict';

var localesAppServices = angular.module('localesApp.services', ['ngResource']);

localesAppServices.factory('Locales', ['$resource', function($resource){
    return $resource('locales/locale-resource/:id', {id: '@id'}, {
        'query': { method:'GET', isArray:true },
        'update': { method: 'PUT'},
        'save': { method: 'POST'}
    });
}]);


localesAppServices.factory('BespokeLocales', ['$http', '$q', function ($http, $q) {
    return {
        destroy: function(id, token) {
            var defer = $q.defer();

            $http.post('locales/destroy', {id:id, _token: token}).success(function (data) {
                defer.resolve(data);
            }).error(function () {
                defer.reject('An error occurred');
            });

            return defer.promise;           
        }
    };
}]);


localesAppServices.factory('Miscellaneous', ['$localStorage', function ($localStorage) {

    return {
        addRemoveClass: function(selector, classesToRemove, classesToAdd) {
            angular.element(selector).removeClass(classesToRemove).addClass(classesToAdd);
        }

    };

}]);