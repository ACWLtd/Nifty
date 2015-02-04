'use strict';

var localesApp = angular.module('localesApp', ['ui.router', 'ngStorage', 'ngAnimate', 'localesApp.controllers', 'localesApp.services', 'nifty.filters', 'nifty.directives', 'angularUtils.directives.dirPagination']);

localesApp.config(['$stateProvider', '$urlRouterProvider', '$locationProvider', function($stateProvider, $urlRouterProvider, $locationProvider) {

    $stateProvider.state('home', {
        url: '/',
        templateUrl: '../packages/kjamesy/cms/angular-partials/locales.html'
    });

    $urlRouterProvider.otherwise('/');

    $locationProvider.html5Mode(false);
    $locationProvider.hashPrefix('!');

}]);



