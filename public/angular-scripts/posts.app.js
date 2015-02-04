'use strict';

var postsApp = angular.module('postsApp', ['ui.router', 'ngStorage', 'ngAnimate', 'postsApp.controllers', 'postsApp.services', 'nifty.filters', 'nifty.directives', 'angularUtils.directives.dirPagination']);

postsApp.config(['$stateProvider', '$urlRouterProvider', '$locationProvider', function($stateProvider, $urlRouterProvider, $locationProvider) {

    $stateProvider.state('home', {
        url: '/',
        templateUrl: '../packages/kjamesy/cms/angular-partials/posts.html'
    });

    $stateProvider.state('create', {
        url: '/create',
        controller: 'CreateController',
        templateUrl: '../packages/kjamesy/cms/angular-partials/posts.create.html'
    });

    $stateProvider.state('edit', {
        url: '/{id:[0-9]+}/edit',
        controller: 'EditController',
        templateUrl: '../packages/kjamesy/cms/angular-partials/posts.edit.html'
    });

    $stateProvider.state('createTranslation', {
        url: '/{postId:[0-9]+}/{localeId:[0-9]+}/create-translation',
        controller: 'CreateTranslationController',
        templateUrl: '../packages/kjamesy/cms/angular-partials/posts.translation.create.html'
    });

    $stateProvider.state('editTranslation', {
        url: '/{postId:[0-9]+}/{localeId:[0-9]+}/edit-translation',
        controller: 'EditTranslationController',
        templateUrl: '../packages/kjamesy/cms/angular-partials/posts.translation.edit.html'
    });

    $urlRouterProvider.otherwise('/');

    $locationProvider.html5Mode(false);
    $locationProvider.hashPrefix('!');

}]);



