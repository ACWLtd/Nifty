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

niftyDirectives.directive('jqdatepickerfrom', function() {
    return {
        restrict: 'A',
        require: 'ngModel',
        link: function(scope, element, attrs, ctrl) {
            $(element).datepicker({             
                dateFormat: 'yy-mm-dd',
                minDate: "-0",
                onSelect: function(date) {
                    ctrl.$setViewValue(date);
                    ctrl.$render();
                    scope.$apply();
                },
                onClose: function( selectedDate ) {
                    $( "#end_date" ).datepicker( "option", "minDate", selectedDate );
                }                 
            });
        }
    };
});

niftyDirectives.directive('jqdatepickerto', function() {
    return {
        restrict: 'A',
        require: 'ngModel',
        link: function(scope, element, attrs, ctrl) {
            $(element).datepicker({
                dateFormat: 'yy-mm-dd',
                onSelect: function(date) {
                    ctrl.$setViewValue(date);
                    ctrl.$render();
                    scope.$apply();
                },
                onClose: function( selectedDate ) {
                    $( "#start_date" ).datepicker( "option", "maxDate", selectedDate );
                }                
            });
        }
    };
});
