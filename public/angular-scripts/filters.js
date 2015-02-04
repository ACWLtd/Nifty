'use strict';

var niftyFilters = angular.module('nifty.filters', []);

niftyFilters.filter('dateToISO', function() {
  	return function(input) {
  		if ( input ) {
		    var output = input.replace(/(.+) (.+)/, "$1T$2Z");
	    	return output;
	    }
  	};
});

niftyFilters.filter('trusted', ['$sce', function($sce) {
    return function(text) {
        return $sce.trustAsHtml(text);
    };

}]);