'use strict';

var categoriesApp = angular.module('categoriesApp', ['ui.router', 'ngStorage', 'ngAnimate', 'categoriesApp.controllers', 'categoriesApp.services', 'nifty.filters', 'nifty.directives', 'angularUtils.directives.dirPagination']);

categoriesApp.config(['$stateProvider', '$urlRouterProvider', '$locationProvider', function($stateProvider, $urlRouterProvider, $locationProvider) {

    $stateProvider.state('home', {
        url: '/',
        templateUrl: '../../packages/kjamesy/cms/angular-partials/categories.html'
    });

    $stateProvider.state('createTranslation', {
        url: '/{categoryId:[0-9]+}/{localeId:[0-9]+}/create-translation',
        controller: 'CreateTranslationController',
        templateUrl: '../../packages/kjamesy/cms/angular-partials/categories.translation.create.html'
    });

    $stateProvider.state('editTranslation', {
        url: '/{categoryId:[0-9]+}/{localeId:[0-9]+}/edit-translation',
        controller: 'EditTranslationController',
        templateUrl: '../../packages/kjamesy/cms/angular-partials/categories.translation.edit.html'
    });

    $urlRouterProvider.otherwise('/');

    $locationProvider.html5Mode(false);
    $locationProvider.hashPrefix('!');

}]);



