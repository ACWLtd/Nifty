'use strict';

var pagesApp = angular.module('pagesApp', ['ui.router', 'ngStorage', 'ngAnimate', 'pagesApp.controllers', 'pagesApp.services', 'nifty.filters', 'nifty.directives', 'angularUtils.directives.dirPagination']);

pagesApp.config(['$stateProvider', '$urlRouterProvider', '$locationProvider', function($stateProvider, $urlRouterProvider, $locationProvider) {

    $stateProvider.state('home', {
        url: '/',
        templateUrl: '../packages/kjamesy/cms/angular-partials/pages.html'
    });

    $stateProvider.state('create', {
        url: '/create',
        controller: 'CreateController',
        templateUrl: '../packages/kjamesy/cms/angular-partials/pages.create.html'
    });

    $stateProvider.state('edit', {
        url: '/{id:[0-9]+}/edit',
        controller: 'EditController',
        templateUrl: '../packages/kjamesy/cms/angular-partials/pages.edit.html'
    });
    
    $stateProvider.state('createTranslation', {
        url: '/{pageId:[0-9]+}/{localeId:[0-9]+}/create-translation',
        controller: 'CreateTranslationController',
        templateUrl: '../packages/kjamesy/cms/angular-partials/pages.translation.create.html'
    });

    $stateProvider.state('editTranslation', {
        url: '/{pageId:[0-9]+}/{localeId:[0-9]+}/edit-translation',
        controller: 'EditTranslationController',
        templateUrl: '../packages/kjamesy/cms/angular-partials/pages.translation.edit.html'
    });

    $urlRouterProvider.otherwise('/');

    $locationProvider.html5Mode(false);
    $locationProvider.hashPrefix('!');

}]);



