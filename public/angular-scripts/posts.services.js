'use strict';

var postsAppServices = angular.module('postsApp.services', ['ngResource']);

postsAppServices.factory('Posts', ['$resource', function($resource){
    return $resource('posts/post-resource/:id', {id: '@id'}, {
        'query': { method:'GET', isArray:true },
        'update': { method: 'PUT'},
        'save': { method: 'POST'}
    });
}]);

postsAppServices.factory('BespokePosts', ['$http', '$q', function ($http, $q) {
    return {
        doBulkActions: function(action,ids, token) {
            var defer = $q.defer();

            $http.post('posts/' + action + '/bulk-actions', {posts:ids, _token: token}).success(function (data) {
                defer.resolve(data);
            }).error(function () {
                defer.reject('An error occurred');
            });

            return defer.promise;           
        },

        getPreviewLink: function(id) {
            return 'posts/' + id + '/preview';           
        },

        getCategories: function() {
            var defer = $q.defer();

            $http.get('posts/get-category-options').success(function(data) {
                defer.resolve(data);
            }).error(function() {
                defer.reject('An error occurred');
            });

            return defer.promise;
        },

        getLocale: function(localeId) {
            var defer = $q.defer();

            $http.get('posts/' + localeId + '/get-locale').success(function(data) {
                defer.resolve(data);
            }).error(function() {
               defer.reject('An error occurred');
            });

            return defer.promise;
        },

        saveTranslation: function(translation, postId, localeId, token) {
            var defer = $q.defer();

            $http.post('posts/translations/store', {translation: translation, postId: postId, localeId: localeId, _token: token}).success(function (data) {
                defer.resolve(data);
            }).error(function () {
                defer.reject('An error occurred');
            });

            return defer.promise;           
        },

        getTranslation: function(postId, localeId) {
            var defer = $q.defer();

            $http.get('posts/' + postId + '/' + localeId + '/get-translation').success(function(data) {
                defer.resolve(data);
            }).error(function() {
               defer.reject('An error occurred');
            });

            return defer.promise;
        },

        updateTranslation: function(translation, customFields, token) {
            var defer = $q.defer();

            $http.post('posts/translations/update', {translation:translation, customFields:customFields, _token: token}).success(function (data) {
                defer.resolve(data);
            }).error(function () {
                defer.reject('An error occurred');
            });

            return defer.promise;           
        },

        destroyTranslation: function(translationId, token)  {
            var defer = $q.defer();

            $http.post('posts/translations/destroy', {translationId:translationId, _token: token}).success(function (data) {
                defer.resolve(data);
            }).error(function () {
                defer.reject('An error occurred');
            });

            return defer.promise;              
        },

        saveCustomField: function(type, postId, customField, token)  {
            var defer = $q.defer();

            $http.post('posts/custom-field/' + type + '/store', {postId:postId, customField:customField, _token: token}).success(function (data) {
                defer.resolve(data);
            }).error(function () {
                defer.reject('An error occurred');
            });

            return defer.promise;              
        },

        updateCustomField: function(type, postId, customField, token)  {
            var defer = $q.defer();

            $http.post('posts/custom-field/' + type + '/update', {postId:postId, customField:customField, _token: token}).success(function (data) {
                defer.resolve(data);
            }).error(function () {
                defer.reject('An error occurred');
            });

            return defer.promise;              
        },

        destroyCustomField: function(type, postId, customField, token)  {
            var defer = $q.defer();

            $http.post('posts/custom-field/' + type + '/destroy', {postId:postId, customField:customField, _token: token}).success(function (data) {
                defer.resolve(data);
            }).error(function () {
                defer.reject('An error occurred');
            });

            return defer.promise;              
        }            
    };
}]);

postsAppServices.factory('Miscellaneous', ['$localStorage', function ($localStorage) {

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