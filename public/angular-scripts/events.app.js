'use strict';

var eventsApp = angular.module('eventsApp', ['ui.router', 'ngStorage', 'ngAnimate', 'eventsApp.controllers', 'eventsApp.services', 'nifty.filters', 'nifty.directives', 'angularUtils.directives.dirPagination']);

eventsApp.config(['$stateProvider', '$urlRouterProvider', '$locationProvider', function($stateProvider, $urlRouterProvider, $locationProvider) {

    $stateProvider.state('home', {
        url: '/',
        templateUrl: '../packages/kjamesy/cms/angular-partials/events.html'
    });

    $stateProvider.state('create', {
        url: '/create',
        controller: 'CreateController',
        templateUrl: '../packages/kjamesy/cms/angular-partials/events.create.html'
    });

    $stateProvider.state('edit', {
        url: '/{id:[0-9]+}/edit',
        controller: 'EditController',
        templateUrl: '../packages/kjamesy/cms/angular-partials/events.edit.html'
    });

    $urlRouterProvider.otherwise('/');

    $locationProvider.html5Mode(false);
    $locationProvider.hashPrefix('!');

}]);



