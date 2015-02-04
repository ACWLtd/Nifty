'use strict';

var niftyDirectives = angular.module('nifty.directives', []);

niftyDirectives.directive('script', function() {
    return {
        restrict: 'E',
        scope: false,
        link: function(scope, elem, attr) {
            if (attr.type === 'text/js-lazy') {
      		    var code = elem.text();
      		    var f = new Function(code);
          		f();
        	}
      	}
    };
});

niftyDirectives.directive('escKey', function () {
    return function (scope, element, attrs) {
        element.bind('keydown keypress', function (event) {
            if(event.which === 27) {
                scope.$apply(function () {
                    scope.$eval(attrs.escKey);
                });
                event.preventDefault();
            }
        });
    };
});