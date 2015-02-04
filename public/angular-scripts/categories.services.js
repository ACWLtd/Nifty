'use strict';

var categoriesAppServices = angular.module('categoriesApp.services', ['ngResource']);

categoriesAppServices.factory('Categories', ['$resource', function($resource){
    return $resource('categories/category-resource/:id', {id: '@id'}, {
        'query': { method:'GET', isArray:true },
        'update': { method: 'PUT'},
        'save': { method: 'POST'}
    });
}]);


categoriesAppServices.factory('BespokeCategories', ['$http', '$q', function ($http, $q) {
    return {
        destroy: function(id, token) {
            var defer = $q.defer();

            $http.post('categories/destroy', {id:id, _token: token}).success(function (data) {
                defer.resolve(data);
            }).error(function () {
                defer.reject('An error occurred');
            });

            return defer.promise;           
        },

        getLocale: function(localeId) {
            var defer = $q.defer();

            $http.get('categories/' + localeId + '/get-locale').success(function(data) {
                defer.resolve(data);
            }).error(function() {
               defer.reject('An error occurred');
            });

            return defer.promise;
        },

        saveTranslation: function(translation, categoryId, localeId, token) {
            var defer = $q.defer();

            $http.post('categories/translations/store', {translation: translation, categoryId: categoryId, localeId: localeId, _token: token}).success(function (data) {
                defer.resolve(data);
            }).error(function () {
                defer.reject('An error occurred');
            });

            return defer.promise;           
        },

        getTranslation: function(categoryId, localeId) {
            var defer = $q.defer();

            $http.get('categories/' + categoryId + '/' + localeId + '/get-translation').success(function(data) {
                defer.resolve(data);
            }).error(function() {
               defer.reject('An error occurred');
            });

            return defer.promise;
        },

        updateTranslation: function(translation, token) {
            var defer = $q.defer();

            $http.post('categories/translations/update', {translation:translation, _token: token}).success(function (data) {
                defer.resolve(data);
            }).error(function () {
                defer.reject('An error occurred');
            });

            return defer.promise;           
        },

        destroyTranslation: function(translationId, token)  {
            var defer = $q.defer();

            $http.post('categories/translations/destroy', {translationId:translationId, _token: token}).success(function (data) {
                defer.resolve(data);
            }).error(function () {
                defer.reject('An error occurred');
            });

            return defer.promise;              
        }
    };
}]);


categoriesAppServices.factory('Miscellaneous', ['$localStorage', function ($localStorage) {

    return {
        addRemoveClass: function(selector, classesToRemove, classesToAdd) {
            angular.element(selector).removeClass(classesToRemove).addClass(classesToAdd);
        }

    };

}]);