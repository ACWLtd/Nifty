'use strict';

var galleriesApp = angular.module('galleriesApp', ['ui.router', 'ngStorage', 'ngAnimate', 'galleriesApp.controllers', 'galleriesApp.services', 'nifty.filters', 'nifty.directives', 'angularUtils.directives.dirPagination']);

galleriesApp.config(['$stateProvider', '$urlRouterProvider', '$locationProvider', function($stateProvider, $urlRouterProvider, $locationProvider) {

    $stateProvider.state('home', {
        url: '/',
        templateUrl: '../packages/kjamesy/cms/angular-partials/galleries.html'
    });

    $stateProvider.state('showGallery', {
        url: '/{galleryId:[0-9]+}/show',
        controller: 'GalleryController',
        templateUrl: '../packages/kjamesy/cms/angular-partials/galleries.show.html'
    });

    $stateProvider.state('showTranslations', {
        url: '/{galleryId:[0-9]+}/show/{imageId:[0-9]+}/translations',
        controller: 'TranslationsController',
        templateUrl: '../packages/kjamesy/cms/angular-partials/galleries.translations.html'
    });

    $urlRouterProvider.otherwise('/');

    $locationProvider.html5Mode(false);
    $locationProvider.hashPrefix('!');

}]);



