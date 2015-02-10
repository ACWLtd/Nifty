'use strict';

var galleriesAppServices = angular.module('galleriesApp.services', ['ngResource']);

galleriesAppServices.factory('Galleries', ['$resource', function($resource){
    return $resource('galleries/gallery-resource/:id', {id: '@id'}, {
        'query': { method:'GET', isArray:true },
        'update': { method: 'PUT'},
        'save': { method: 'POST'}
    });
}]);


galleriesAppServices.factory('BespokeGalleries', ['$http', '$q', function ($http, $q) {
    return {
        destroy: function(id, token) {
            var defer = $q.defer();

            $http.post('galleries/destroy', {id:id, _token: token}).success(function (data) {
                defer.resolve(data);
            }).error(function () {
                defer.reject('An error occurred');
            });

            return defer.promise;           
        },

        saveImage: function(galleryId, image, token) {
            var defer = $q.defer();

            $http.post('galleries/images/store', {image: image, galleryId: galleryId, _token: token}).success(function (data) {
                defer.resolve(data);
            }).error(function () {
                defer.reject('An error occurred');
            });

            return defer.promise;           
        },

        updateImage: function(galleryId, image, token) {
            var defer = $q.defer();

            $http.post('galleries/images/update', {galleryId: galleryId, image:image, _token: token}).success(function (data) {
                defer.resolve(data);
            }).error(function () {
                defer.reject('An error occurred');
            });

            return defer.promise;           
        },    

        destroyImage: function(imageId, token)  {
            var defer = $q.defer();

            $http.post('galleries/images/destroy', {imageId:imageId, _token: token}).success(function (data) {
                defer.resolve(data);
            }).error(function () {
                defer.reject('An error occurred');
            });

            return defer.promise;              
        },

        getImage: function(imageId) {
            var defer = $q.defer();

            $http.get('galleries/images/' + imageId + '/show-image').success(function(data) {
                defer.resolve(data);
            }).error(function() {
               defer.reject('An error occurred');
            });

            return defer.promise;
        },

        processTranslation: function(translation, token) {
            var defer = $q.defer();

            $http.post('galleries/images/process-translation', {translation: translation, _token: token}).success(function (data) {
                defer.resolve(data);
            }).error(function () {
                defer.reject('An error occurred');
            });

            return defer.promise;           
        },

        destroyTranslation: function(translation, token) {
            var defer = $q.defer();

            $http.post('galleries/images/destroy-translation', {translation: translation, _token: token}).success(function (data) {
                defer.resolve(data);
            }).error(function () {
                defer.reject('An error occurred');
            });

            return defer.promise;           
        }         
    };
}]);


galleriesAppServices.factory('Miscellaneous', ['$localStorage', function ($localStorage) {

    return {
        addRemoveClass: function(selector, classesToRemove, classesToAdd) {
            angular.element(selector).removeClass(classesToRemove).addClass(classesToAdd);
        },

        highlightInput: function(selector, duration) { 
            var element = angular.element(selector).parent(); //We are highlighting the form-group which is a parent of the input element.

            element.addClass('has-error');
            setTimeout(function() {
                element.removeClass('has-error');
            }, duration);
        },
    };

}]);