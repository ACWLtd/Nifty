'use strict';

var localesAppControllers = angular.module('localesApp.controllers', []);

localesAppControllers.controller('LocalesController', ['$rootScope', '$scope', '$window', '$document', 'Locales', 'BespokeLocales', 'Miscellaneous', function($rootScope, $scope, $window, $document, Locales, BespokeLocales, Miscellaneous) {
	
	initialSettings();

	Locales.get(function(data) {
		var localesArr = [];

		angular.forEach(data.locales, function(value, index) {
			if ( value.id ) {
				value.editing = false;
				value.initial = value.locale;
				value.error = false;
				value.saving = false;

				localesArr.push(value);
			}			
		});

		$scope.locales = localesArr;
	});

	function initialSettings() {
		$scope.locales = [];			
		$scope.currentPage = 1;
	  	$scope.pageSize = 0;		
		$scope.formFields = {};
		$scope.formFields.hasMessage = false;
		$scope.formFields.message = 'Server message';			
		$scope.options = {};
		$scope.options.orderParam = 'locale';	
		$scope.newLocale = {};
		$scope.newLocale.locale = '';
		$scope.newLocale.error = false;
		$scope.savingNewLocale = false;
	}

	$scope.saveLocale = function(newLocale) {
		if ( newLocale.locale.length ) {
			if ( checkUnique(newLocale, true) ) {
				$scope.savingNewLocale = true;

				Locales.save(newLocale, function(response) {
					if ( response.validation ) {
						Miscellaneous.addRemoveClass('.alert.animate', 'alert-info alert-success', 'alert-danger');

						$scope.formFields.hasMessage = true;
						var messages = '';

						angular.forEach( response.validation, function(message, index) {
							messages += "<i class='fa fa-warning'></i> " + message + "<br />";
						});

						$scope.formFields.message = messages;

			    		$scope.newLocale.error = true;
					}

					else if ( response.success ) {
						Miscellaneous.addRemoveClass('.alert.animate', 'alert-info alert-danger', 'alert-success');
						$scope.formFields.hasMessage = true;
						$scope.formFields.message = "<i class='fa fa-check'></i> " + response.success;

						response.locale.editing = false;
						response.locale.initial = response.locale.locale;
						response.locale.error = false;
						response.locale.saving = false;

						$scope.locales.push(response.locale);
						$scope.options.orderParam = 'locale';

						setTimeout(function() {
							Miscellaneous.addRemoveClass('.alert.animate', 'alert-success alert-danger', 'alert-info');
							$scope.formFields.hasMessage = false;
							$scope.$apply();
						}, 2000);

						$scope.newLocale.locale = null;
					}

					$scope.savingNewLocale = false;
				});  
			}

			else {
				$scope.newLocale.error = true;
				$scope.formFields.hasMessage = true;
				Miscellaneous.addRemoveClass('.alert.animate', 'alert-info alert-success', 'alert-danger');
				$scope.formFields.message = "<i class='fa fa-warning'></i> That name is already taken";
			}
		}
	};

    $scope.editLocale = function(locale) {
    	cancelAllEditings();
    	
    	if ( ! locale.saving )
        	locale.editing = true;
    }; 
        
    $scope.doneEditing = function(locale) {
    	if ( locale.locale.length && checkUnique(locale, false) ) {
    		locale.error = false;
        	locale.editing = false;

        	if ( locale.locale != locale.initial ) {
        		locale.saving = true;

				Locales.update({ id:locale.id }, locale, function(response) {
					if ( response.validation ) {
						Miscellaneous.addRemoveClass('.alert.animate', 'alert-info alert-success', 'alert-danger');

						$scope.formFields.hasMessage = true;
						var messages = '';

						angular.forEach( response.validation, function(message, index) {
							messages += "<i class='fa fa-warning'></i> " + message + "<br />";
						});

						$scope.formFields.message = messages;

			    		locale.error = true;
    					locale.locale = locale.initial;
					}

					else if ( response.success ) {
						Miscellaneous.addRemoveClass('.alert.animate', 'alert-info alert-danger', 'alert-success');
						$scope.formFields.hasMessage = true;
						$scope.formFields.message = "<i class='fa fa-check'></i> " + response.success;

						locale.updated_at = response.updated_at.date;

						angular.forEach($scope.locales, function(value, index) {
							value.initial = value.locale;
						});

						setTimeout(function() {
							Miscellaneous.addRemoveClass('.alert.animate', 'alert-success alert-danger', 'alert-info');
							$scope.formFields.hasMessage = false;
							$scope.$apply();
						}, 2000);
					}

					locale.saving = false;
				});      		
        	}
    	}
    	else {
    		locale.error = true;
    		locale.locale = locale.initial;
    	}
    };

    $scope.cancelEditing = function(locale) {
    	locale.editing = false;
    };	

    $document.bind("keypress", function(event) {
        if ( event.key == 'Esc' ) {
        	cancelAllEditings();
        	$scope.$apply();
        }
    });


    function cancelAllEditings() {
    	angular.forEach($scope.locales, function(locale, index) {
    		locale.editing = false;
    		locale.locale = locale.initial;
    	});
    }

    function checkUnique(locale, isNew) {
    	var looping = true;
    	var exists = false;

    	angular.forEach($scope.locales, function(value, index) {
    		if ( looping && (isNew || value.id != locale.id) ) {
    			if ( locale.locale == value.locale ) {
    				exists = true;
    				looping = false;
    			}
    		}
    	});

    	return ! exists;
    }

    $scope.destroy = function(index, id) {
		if ( $window.confirm('Warning: This will delete all translations associated with this locale. Proceed?') ) {
			BespokeLocales.destroy(id, $scope.laravel_token).then(function(response) {
				if ( response.success ) {
					Miscellaneous.addRemoveClass('.alert.animate', 'alert-info alert-danger', 'alert-success');
					$scope.formFields.hasMessage = true;
					$scope.formFields.message = "<i class='fa fa-check'></i> " + response.success;

					$scope.locales.splice(index, 1);

					setTimeout(function() {
						Miscellaneous.addRemoveClass('.alert.animate', 'alert-success alert-danger', 'alert-info');
						$scope.formFields.hasMessage = false;
						$scope.$apply();
					}, 2000);
				}
			});
		}    	
    }

}]);
