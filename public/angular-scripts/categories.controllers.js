'use strict';

var categoriesAppControllers = angular.module('categoriesApp.controllers', []);

categoriesAppControllers.controller('CategoriesController', ['$rootScope', '$scope', '$window', '$document', 'Categories', 'BespokeCategories', 'Miscellaneous', function($rootScope, $scope, $window, $document, Categories, BespokeCategories, Miscellaneous) {
	
	$rootScope.$on('$stateChangeSuccess', function (ev, to, toParams, from, fromParams) {
		if ( to.name == 'home' ) {
			if ( from.name == 'createTranslation' || from.name == 'editTranslation' ) {
				$window.setTimeout(function() {
					$window.location.reload();
				}, 10);
			}
		}
	});

	initialSettings();

	Categories.get(function(data) {
		var categoriesArr = [];

		angular.forEach(data.categories, function(value, index) {
			if ( value.id ) {
				value.editing = false;
				value.initial = value.name;
				value.error = false;
				value.saving = false;
				value.translations = [];

				if ( value.categorytranslations.length ) {
					angular.forEach(value.categorytranslations, function(translation, index) {
						value.translations.push(translation.locale.locale);
					});
				}

				if ( value.posts_count[0] )
					value.posts = value.posts_count[0].count;
				else
					value.posts = 0;

				categoriesArr.push(value);
			}			
		});

		$scope.categories = categoriesArr;

		angular.forEach(data.locales, function(locale, index) {
			$scope.options.locales.push(locale);
		});			

	});

	function initialSettings() {
		$scope.categories = null;			
		$scope.currentPage = 1;
	  	$scope.pageSize = 0;		
		$scope.formFields = {};
		$scope.formFields.hasMessage = false;
		$scope.formFields.message = 'Server message';			
		$scope.options = {};
		$scope.options.locales = [];
		$scope.options.orderParam = 'default';	
		$scope.newCat = {};
		$scope.newCat.name = '';
		$scope.newCat.error = false;
		$scope.savingNewCat = false;
	}

	$scope.saveCategory = function(newCat) {
		if ( newCat.name.length ) {
			if ( checkUnique(newCat, true) ) {
				$scope.savingNewCat = true;

				Categories.save(newCat, function(response) {
					if ( response.validation ) {
						Miscellaneous.addRemoveClass('.alert.animate', 'alert-info alert-success', 'alert-danger');

						$scope.formFields.hasMessage = true;
						var messages = '';

						angular.forEach( response.validation, function(message, index) {
							messages += "<i class='fa fa-warning'></i> " + message + "<br />";
						});

						$scope.formFields.message = messages;

			    		$scope.newCat.error = true;
					}

					else if ( response.success ) {
						Miscellaneous.addRemoveClass('.alert.animate', 'alert-info alert-danger', 'alert-success');
						$scope.formFields.hasMessage = true;
						$scope.formFields.message = "<i class='fa fa-check'></i> " + response.success;

						response.category.posts = 0;
						response.category.editing = false;
						response.category.initial = response.category.name;
						response.category.error = false;
						response.category.saving = false;

						$scope.categories.push(response.category);
						$scope.options.orderParam = 'name';

						setTimeout(function() {
							Miscellaneous.addRemoveClass('.alert.animate', 'alert-success alert-danger', 'alert-info');
							$scope.formFields.hasMessage = false;
							$scope.$apply();
						}, 2000);

						$scope.newCat.name = null;
					}

					$scope.savingNewCat = false;
				});  
			}

			else {
				$scope.newCat.error = true;
				$scope.formFields.hasMessage = true;
				Miscellaneous.addRemoveClass('.alert.animate', 'alert-info alert-success', 'alert-danger');
				$scope.formFields.message = "<i class='fa fa-warning'></i> That name is already taken";
			}
		}
	};

    $scope.editCategory = function(category) {
    	cancelAllEditings();
    	
    	if ( ! category.saving )
        	category.editing = true;
    }; 
        
    $scope.doneEditing = function(category) {
    	if ( category.name.length && checkUnique(category, false) ) {
    		category.error = false;
        	category.editing = false;

        	if ( category.name != category.initial ) {
        		category.saving = true;

				Categories.update({ id:category.id }, category, function(response) {
					if ( response.validation ) {
						Miscellaneous.addRemoveClass('.alert.animate', 'alert-info alert-success', 'alert-danger');

						$scope.formFields.hasMessage = true;
						var messages = '';

						angular.forEach( response.validation, function(message, index) {
							messages += "<i class='fa fa-warning'></i> " + message + "<br />";
						});

						$scope.formFields.message = messages;

			    		category.error = true;
    					category.name = category.initial;
					}

					else if ( response.success ) {
						Miscellaneous.addRemoveClass('.alert.animate', 'alert-info alert-danger', 'alert-success');
						$scope.formFields.hasMessage = true;
						$scope.formFields.message = "<i class='fa fa-check'></i> " + response.success;

						category.updated_at = response.updated_at.date;
						
						angular.forEach($scope.categories, function(value, index) {
							value.initial = value.name;
						});

						setTimeout(function() {
							Miscellaneous.addRemoveClass('.alert.animate', 'alert-success alert-danger', 'alert-info');
							$scope.formFields.hasMessage = false;
							$scope.$apply();
						}, 2000);
					}

					category.saving = false;
				});      		
        	}
    	}
    	else {
    		category.error = true;
    		category.name = category.initial;
    	}
    };

    $scope.cancelEditing = function(category) {
    	category.editing = false;
    };	

    $document.bind("keypress", function(event) {
        if ( event.key == 'Esc' ) {
        	cancelAllEditings();
        	$scope.$apply();
        }
    });


    function cancelAllEditings() {
    	angular.forEach($scope.categories, function(category, index) {
    		category.editing = false;
    		category.name = category.initial;
    	});
    }

    function checkUnique(category, isNew) {
    	var looping = true;
    	var exists = false;

    	angular.forEach($scope.categories, function(value, index) {
    		if ( looping && (isNew || value.id != category.id) ) {
    			if ( category.name == value.name ) {
    				exists = true;
    				looping = false;
    			}
    		}
    	});

    	return ! exists;
    }

    $scope.destroy = function(index, id) {
		if ( $window.confirm('Warning: This cannot be undone. Proceed?') ) {
			BespokeCategories.destroy(id, $scope.laravel_token).then(function(response) {
				if ( response.success ) {
					Miscellaneous.addRemoveClass('.alert.animate', 'alert-info alert-danger', 'alert-success');
					$scope.formFields.hasMessage = true;
					$scope.formFields.message = "<i class='fa fa-check'></i> " + response.success;

					$scope.categories.splice(index, 1);

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

categoriesAppControllers.controller('CreateTranslationController', ['$scope', '$stateParams', '$window', 'Categories', 'BespokeCategories', 'Miscellaneous', function($scope, $stateParams, $window, Categories, BespokeCategories, Miscellaneous) { 
	initialSettings();

	Categories.get({ id:$stateParams.categoryId }, function(data) { 
		if ( data.category ) {
			$scope.options.cat = data.category.name;
		}

	});

	BespokeCategories.getLocale( $stateParams.localeId ).then(function(data) {
		$scope.options.locale = data.locale.locale;
	});

	$scope.saveCatTranslation = function(newCat) {
		if ( newCat.name.length ) {
			$scope.savingNewCat = true;

			var categoryId = $stateParams.categoryId;
			var localeId = $stateParams.localeId;
			var token = $scope.laravel_token;

			BespokeCategories.saveTranslation(newCat, categoryId, localeId, token).then(function(response) {
				if ( response.validation ) {
					Miscellaneous.addRemoveClass('.alert.animate', 'alert-info alert-success', 'alert-danger');

					$scope.formFields.hasMessage = true;
					var messages = '';

					angular.forEach( response.validation, function(message, index) {
						messages += "<i class='fa fa-warning'></i> " + message + "<br />";
					});

					$scope.formFields.message = messages;

		    		$scope.newCat.error = true;
				}

				else if ( response.success ) {
					Miscellaneous.addRemoveClass('.alert.animate', 'alert-info alert-danger', 'alert-success');
					$scope.formFields.hasMessage = true;
					$scope.formFields.message = "<i class='fa fa-check'></i> " + response.success;

					$scope.newCat.error = false;

					setTimeout(function() {
						Miscellaneous.addRemoveClass('.alert.animate', 'alert-success alert-danger', 'alert-info');
						$scope.formFields.hasMessage = false;
						$scope.$apply();
					}, 2000);
				}

				$scope.savingNewCat = false;
			});  
		}
	};


	function initialSettings() {		
		$scope.formFields = {};
		$scope.formFields.hasMessage = false;
		$scope.formFields.message = 'Server message';			
		$scope.options = {};
		$scope.options.cat = null;
		$scope.options.locale = null;
		$scope.newCat = {};
		$scope.newCat.name = '';
		$scope.newCat.error = false;
		$scope.savingNewCat = false;
	}

}]);


categoriesAppControllers.controller('EditTranslationController', ['$scope', '$state', '$stateParams', '$window', 'Categories', 'BespokeCategories', 'Miscellaneous', function($scope, $state, $stateParams, $window, Categories, BespokeCategories, Miscellaneous) { 
	initialSettings();

	BespokeCategories.getTranslation( $stateParams.categoryId, $stateParams.localeId ).then(function(data) {
		if ( data.translation ) {
			$scope.editCat.id = data.translation.id;
			$scope.editCat.name = data.translation.name;
			$scope.options.locale = data.translation.locale.locale;
		}
	});

	$scope.updateTranslation = function(editCat) {
		if ( editCat.name.length ) {
			$scope.savingCat = true;

			var token = $scope.laravel_token;

			BespokeCategories.updateTranslation(editCat, token).then(function(response) {
				if ( response.validation ) {
					Miscellaneous.addRemoveClass('.alert.animate', 'alert-info alert-success', 'alert-danger');

					$scope.formFields.hasMessage = true;
					var messages = '';

					angular.forEach( response.validation, function(message, index) {
						messages += "<i class='fa fa-warning'></i> " + message + "<br />";
					});

					$scope.formFields.message = messages;

		    		$scope.editCat.error = true;
				}

				else if ( response.success ) {
					Miscellaneous.addRemoveClass('.alert.animate', 'alert-info alert-danger', 'alert-success');
					$scope.formFields.hasMessage = true;
					$scope.formFields.message = "<i class='fa fa-check'></i> " + response.success;

					$scope.editCat.error = false;

					setTimeout(function() {
						Miscellaneous.addRemoveClass('.alert.animate', 'alert-success alert-danger', 'alert-info');
						$scope.formFields.hasMessage = false;
						$scope.$apply();
					}, 2000);
				}

				$scope.savingCat = false;
			});  
		}
	};

	$scope.destroy = function() {
		var id = $scope.editCat.id;
		var token = $scope.laravel_token;

		if ( $window.confirm('You are about to destroy this translation. This cannot be undone.') ) {
			BespokeCategories.destroyTranslation(id, token).then(function(response) {
				if ( response.success ) {

				Miscellaneous.addRemoveClass('.alert.animate', 'alert-info alert-danger', 'alert-success');
				$scope.formFields.hasMessage = true;
				$scope.formFields.message = "<i class='fa fa-check'></i> " + response.success + ". Redirecting you in a few seconds...";

					setTimeout(function() {
						$state.go('home');
					}, 2000);
				}
			});
		}
	};

	function initialSettings() {		
		$scope.formFields = {};
		$scope.formFields.hasMessage = false;
		$scope.formFields.message = 'Server message';			
		$scope.options = {};
		$scope.options.locale = null;
		$scope.editCat = {};
		$scope.editCat.name = '';
		$scope.editCat.error = false;
		$scope.savingCat = false;
	}

}]);