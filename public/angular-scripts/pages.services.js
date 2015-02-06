'use strict';

var pagesAppServices = angular.module('pagesApp.services', ['ngResource']);

pagesAppServices.factory('Pages', ['$resource', function($resource){
    return $resource('pages/page-resource/:id', {id: '@id'}, {
        'query': { method:'GET', isArray:true },
        'update': { method: 'PUT'},
        'save': { method: 'POST'}
    });
}]);

pagesAppServices.factory('BespokePages', ['$http', '$q', function ($http, $q) {
    return {
        doBulkActions: function(action,ids, token) {
            var defer = $q.defer();

            $http.post('pages/' + action + '/bulk-actions', {pages:ids, _token: token}).success(function (data) {
                defer.resolve(data);
            }).error(function () {
                defer.reject('An error occurred');
            });

            return defer.promise;           
        },

        getPreviewLink: function(id) {
            return 'pages/' + id + '/preview';           
        },

        getParents: function() {
            var defer = $q.defer();

            $http.get('pages/get-parent-options').success(function(data) {
                defer.resolve(data);
            }).error(function() {
                defer.reject('An error occurred');
            });

            return defer.promise;
        },

        getLocale: function(localeId) {
            var defer = $q.defer();

            $http.get('pages/' + localeId + '/get-locale').success(function(data) {
                defer.resolve(data);
            }).error(function() {
               defer.reject('An error occurred');
            });

            return defer.promise;
        },

        saveTranslation: function(translation, pageId, localeId, token) {
            var defer = $q.defer();

            $http.post('pages/translations/store', {translation: translation, pageId: pageId, localeId: localeId, _token: token}).success(function (data) {
                defer.resolve(data);
            }).error(function () {
                defer.reject('An error occurred');
            });

            return defer.promise;           
        },

        getTranslation: function(pageId, localeId) {
            var defer = $q.defer();

            $http.get('pages/' + pageId + '/' + localeId + '/get-translation').success(function(data) {
                defer.resolve(data);
            }).error(function() {
               defer.reject('An error occurred');
            });

            return defer.promise;
        },

        updateTranslation: function(translation, customFields, token) {
            var defer = $q.defer();

            $http.post('pages/translations/update', {translation:translation, customFields:customFields, _token: token}).success(function (data) {
                defer.resolve(data);
            }).error(function () {
                defer.reject('An error occurred');
            });

            return defer.promise;           
        },

        destroyTranslation: function(translationId, token)  {
            var defer = $q.defer();

            $http.post('pages/translations/destroy', {translationId:translationId, _token: token}).success(function (data) {
                defer.resolve(data);
            }).error(function () {
                defer.reject('An error occurred');
            });

            return defer.promise;              
        },

        saveCustomField: function(type, pageId, customField, token)  {
            var defer = $q.defer();

            $http.post('pages/custom-field/' + type + '/store', {pageId:pageId, customField:customField, _token: token}).success(function (data) {
                defer.resolve(data);
            }).error(function () {
                defer.reject('An error occurred');
            });

            return defer.promise;              
        },

        updateCustomField: function(type, pageId, customField, token)  {
            var defer = $q.defer();

            $http.post('pages/custom-field/' + type + '/update', {pageId:pageId, customField:customField, _token: token}).success(function (data) {
                defer.resolve(data);
            }).error(function () {
                defer.reject('An error occurred');
            });

            return defer.promise;              
        },

        destroyCustomField: function(type, pageId, customField, token)  {
            var defer = $q.defer();

            $http.post('pages/custom-field/' + type + '/destroy', {pageId:pageId, customField:customField, _token: token}).success(function (data) {
                defer.resolve(data);
            }).error(function () {
                defer.reject('An error occurred');
            });

            return defer.promise;              
        }

    };
}]);


pagesAppServices.factory('Miscellaneous', ['$localStorage', function ($localStorage) {

    return {
        resetLocalStorage: function() {
            $localStorage.$reset();
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
        },
        
        objSize: function(obj) {
            var size = 0, key;

            for (key in obj) {
                if (obj.hasOwnProperty(key)) 
                    size++;
            }

            return size;
        },

        //For use when using Baum's toHierarchy() method on models
        pushIterator: function(pushArr, obj) {
            if ( typeof obj.children == 'object' && this.objSize(obj.children) > 0 ) {
                angular.forEach( obj.children, function(value, index) {
                    pushArr.push(value);
                    pushIterator( pushArr, value );
                });
            }
        },

        //For use in the general case
        childIterator: function(pagesArr, publishedArr, draftsArr, trashArr, obj, level) {
            var _this = this;

            angular.forEach(obj, function(value, index) {
                if ( value.id ) {
                    value.level = level;
                    value.selected = false;
                    value.translations = [];

                    if ( value.pagetranslations.length ) {
                        angular.forEach(value.pagetranslations, function(translation, index) {
                            value.translations.push(translation.locale.locale);
                        });
                    }
                    
                    if ( ! value.is_deleted ) {
                        pagesArr.push(value);

                        if ( ! value.is_online )
                            draftsArr.push(value);
                        else
                            publishedArr.push(value);
                    }
                    else {
                        trashArr.push(value);
                    }
                }
                else {
                    _this.childIterator( pagesArr, publishedArr, draftsArr, trashArr, value, level+1 );
                }
            });
        },

        childCrumbs: function(num, html) {
            
            var output = '';

            for (var i = num - 2; i >= 0; i--) {
                output += html;
            };

            return output + ' ';
        } 

    };

}]);