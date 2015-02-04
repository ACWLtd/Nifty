'use strict';

var usersApp = angular.module('usersApp', ['ui.router', 'ngStorage', 'ngAnimate', 'usersApp.controllers', 'usersApp.services', 'nifty.filters', 'nifty.directives', 'angularUtils.directives.dirPagination']);

usersApp.config(['$stateProvider', '$urlRouterProvider', '$locationProvider', function($stateProvider, $urlRouterProvider, $locationProvider) {

    $stateProvider.state('home', {
        url: '/',
        templateUrl: '../packages/kjamesy/cms/angular-partials/users.html'
    });

    $urlRouterProvider.otherwise('/');

    $locationProvider.html5Mode(false);
    $locationProvider.hashPrefix('!');

}]);



