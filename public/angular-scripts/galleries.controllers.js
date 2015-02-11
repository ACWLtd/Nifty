'use strict';

var galleriesAppControllers = angular.module('galleriesApp.controllers', ['ckeditor']);

galleriesAppControllers.controller('GalleriesController', ['$rootScope', '$scope', '$window', '$document', 'Galleries', 'BespokeGalleries', 'Miscellaneous', function($rootScope, $scope, $window, $document, Galleries, BespokeGalleries, Miscellaneous) {
	
	// $rootScope.$on('$stateChangeSuccess', function (ev, to, toParams, from, fromParams) {
	// 	if ( to.name == 'home' ) {
	// 		if ( from.name == 'createTranslation' || from.name == 'editTranslation' ) {
	// 			$window.setTimeout(function() {
	// 				$window.location.reload();
	// 			}, 10);
	// 		}
	// 	}
	// });

	initialSettings();

	Galleries.get(function(data) {
		var galleriesArr = [];

		angular.forEach(data.galleries, function(value, index) {
			if ( value.id ) {
				value.editing = false;
				value.initial = value.name;
				value.error = false;
				value.saving = false;
				value.translations = [];

				galleriesArr.push(value);
			}			
		});

		$scope.galleries = galleriesArr;

	});

	function initialSettings() {
		$scope.categories = null;			
		$scope.currentPage = 1;
	  	$scope.pageSize = 0;		
		$scope.formFields = {};
		$scope.formFields.hasMessage = false;
		$scope.formFields.message = 'Server message';			
		$scope.options = {};
		$scope.options.orderParam = 'default';	
		$scope.newGallery = {};
		$scope.newGallery.name = '';
		$scope.newGallery.error = false;
		$scope.savingNewGallery = false;
	}

	$scope.saveGallery = function(newGallery) {
		if ( newGallery.name.length ) {
			if ( checkUnique(newGallery, true) ) {
				$scope.savingnewGallery = true;

				Galleries.save(newGallery, function(response) {
					if ( response.validation ) {
						Miscellaneous.addRemoveClass('.alert.animate', 'alert-info alert-success', 'alert-danger');

						$scope.formFields.hasMessage = true;
						var messages = '';

						angular.forEach( response.validation, function(message, index) {
							messages += "<i class='fa fa-warning'></i> " + message + "<br />";
						});

						$scope.formFields.message = messages;

			    		$scope.newGallery.error = true;
					}

					else if ( response.success ) {
						Miscellaneous.addRemoveClass('.alert.animate', 'alert-info alert-danger', 'alert-success');
						$scope.formFields.hasMessage = true;
						$scope.formFields.message = "<i class='fa fa-check'></i> " + response.success;

						response.gallery.editing = false;
						response.gallery.initial = response.gallery.name;
						response.gallery.error = false;
						response.gallery.saving = false;

						$scope.galleries.push(response.gallery);
						$scope.options.orderParam = 'name';

						setTimeout(function() {
							Miscellaneous.addRemoveClass('.alert.animate', 'alert-success alert-danger', 'alert-info');
							$scope.formFields.hasMessage = false;
							$scope.$apply();
						}, 2000);

						$scope.newGallery.name = null;
					}

					$scope.savingnewGallery = false;
				});  
			}

			else {
				$scope.newGallery.error = true;
				$scope.formFields.hasMessage = true;
				Miscellaneous.addRemoveClass('.alert.animate', 'alert-info alert-success', 'alert-danger');
				$scope.formFields.message = "<i class='fa fa-warning'></i> That name is already taken";
			}
		}
	};

    $scope.editGallery = function(gallery) {
    	cancelAllEditings();
    	
    	if ( ! gallery.saving )
        	gallery.editing = true;
    }; 
        
    $scope.doneEditing = function(gallery) {
    	if ( gallery.name.length && checkUnique(gallery, false) ) {
    		gallery.error = false;
        	gallery.editing = false;

        	if ( gallery.name != gallery.initial ) {
        		gallery.saving = true;

				Galleries.update({ id:gallery.id }, gallery, function(response) {
					if ( response.validation ) {
						Miscellaneous.addRemoveClass('.alert.animate', 'alert-info alert-success', 'alert-danger');

						$scope.formFields.hasMessage = true;
						var messages = '';

						angular.forEach( response.validation, function(message, index) {
							messages += "<i class='fa fa-warning'></i> " + message + "<br />";
						});

						$scope.formFields.message = messages;

			    		gallery.error = true;
    					gallery.name = gallery.initial;
					}

					else if ( response.success ) {
						Miscellaneous.addRemoveClass('.alert.animate', 'alert-info alert-danger', 'alert-success');
						$scope.formFields.hasMessage = true;
						$scope.formFields.message = "<i class='fa fa-check'></i> " + response.success;

						gallery.slug = response.slug;
						gallery.updated_at = response.updated_at.date;
						
						angular.forEach($scope.galleries, function(value, index) {
							value.initial = value.name;
						});

						setTimeout(function() {
							Miscellaneous.addRemoveClass('.alert.animate', 'alert-success alert-danger', 'alert-info');
							$scope.formFields.hasMessage = false;
							$scope.$apply();
						}, 2000);
					}

					gallery.saving = false;
				});      		
        	}
    	}
    	else {
    		gallery.error = true;
    		gallery.name = gallery.initial;
    	}
    };

    $scope.cancelEditing = function(gallery) {
    	gallery.editing = false;
    };	

    $document.bind("keypress", function(event) {
        if ( event.key == 'Esc' ) {
        	cancelAllEditings();
        	$scope.$apply();
        }
    });


    function cancelAllEditings() {
    	angular.forEach($scope.galleries, function(gallery, index) {
    		gallery.editing = false;
    		gallery.name = gallery.initial;
    	});
    }

    function checkUnique(gallery, isNew) {
    	var looping = true;
    	var exists = false;

    	angular.forEach($scope.galleries, function(value, index) {
    		if ( looping && (isNew || value.id != gallery.id) ) {
    			if ( gallery.name == value.name ) {
    				exists = true;
    				looping = false;
    			}
    		}
    	});

    	return ! exists;
    }

    $scope.destroy = function(index, id) {
		if ( $window.confirm('Warning: This will also delete all images in gallery. Proceed?') ) {
			BespokeGalleries.destroy(id, $scope.laravel_token).then(function(response) {
				if ( response.success ) {
					Miscellaneous.addRemoveClass('.alert.animate', 'alert-info alert-danger', 'alert-success');
					$scope.formFields.hasMessage = true;
					$scope.formFields.message = "<i class='fa fa-check'></i> " + response.success;

					$scope.galleries.splice(index, 1);

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


galleriesAppControllers.controller('GalleryController', ['$scope', '$window', '$stateParams', 'Galleries', 'BespokeGalleries', 'Miscellaneous', function($scope, $window, $stateParams, Galleries, BespokeGalleries, Miscellaneous) {

	initialSettings('boot');

	Galleries.get({ id:$stateParams.galleryId }, function(data) {
		$scope.gallery = data.gallery;

		angular.forEach(data.gallery.images, function(image, index) {
			var url = image.url;
			image.url = "<img src='" + url + "' />";
			image.updating = false;
			image.hiddenCaptionEditor = true;
			image.hiddenUrlEditor = true;
			$scope.images.push(image);
		});

		angular.forEach(data.locales, function(locale, index) {
			$scope.options.locales.push(locale);
		});			

		$scope.options.showEditor = true;

	});

	$scope.showImageForm = function() {
		$scope.options.imageForm = true;
	};

	$scope.hideImageForm = function() {
		$scope.options.imageForm = false;
	};

	$scope.addImage = function(image) {
		var galleryId = $stateParams.galleryId;
		var token = $scope.laravel_token;

		if ( image.url.length ) {
			
			initialSettings('formProcess');
			$scope.image.saving = true;

			BespokeGalleries.saveImage(galleryId, image, token).then(function(response) {
				if ( response.validation ) {
					Miscellaneous.addRemoveClass('.alert.animate', 'alert-info alert-success', 'alert-danger');

					$scope.options.hasMessage = true;
					var messages = '';

					angular.forEach( response.validation, function(message, index) {
						if ( index == 'url' )
							messages += "<i class='fa fa-warning'></i> The URL must be valid.<br />";
						else
							messages += "<i class='fa fa-warning'></i> " + message + "<br />";

						Miscellaneous.highlightInput('#image-' + index, 10000);
					});

					$scope.options.message = messages;
				}

				else if ( response.success ) {
					Miscellaneous.addRemoveClass('.alert.animate', 'alert-info alert-danger', 'alert-success');

					$scope.options.hasMessage = true;
					$scope.options.message = "<i class='fa fa-check'></i> " + response.success;

					var url = response.image.url;
					response.image.url = "<img src='" + url + "' />";
					response.image.updating = false;
					response.image.hiddenCaptionEditor = true;
					response.image.hiddenUrlEditor = true;

					$scope.images.push(response.image);
					$scope.options.imageForm = false;
					
					setTimeout(function() {
						$scope.options.hiddenCaptionEditor = true;
						$scope.options.hiddenUrlEditor = true;

						initialSettings('formProcess');
						initialSettings('images');
						$scope.$apply();
					}, 3000);
				}

				$scope.image.saving = false;
			});
		}
	};

	$scope.updateImage = function($index) {
		var image = $scope.images[$index];
		var galleryId = $stateParams.galleryId;
		var token = $scope.laravel_token;		
		if ( image  && image.url.length ) {
			initialSettings('formProcess');
			$scope.images[$index].updating = true;

			BespokeGalleries.updateImage(galleryId, image, token).then(function(response) {
				if ( response.validation ) {
					Miscellaneous.addRemoveClass('.alert.animate', 'alert-info alert-success', 'alert-danger');

					$scope.options.hasMessage = true;
					var messages = '';

					angular.forEach( response.validation, function(message, index) {
						messages += "<i class='fa fa-warning'></i> " + message + "<br />";
						Miscellaneous.highlightInput('#' + index, 10000);
					});

					$scope.options.message = messages;
				}

				else if ( response.success ) {
					Miscellaneous.addRemoveClass('.alert.animate', 'alert-info alert-danger', 'alert-success');

					$scope.options.hasMessage = true;
					$scope.options.message = "<i class='fa fa-check'></i> " + response.success;

					var url = response.image.url;
					response.image.url = "<img src='" + url + "' />";

					$scope.images[$index].order = response.image.order;
					$scope.images[$index].title = response.image.title;
					$scope.images[$index].caption = response.image.caption;
					$scope.images[$index].url = response.image.url;
					$scope.images[$index].hiddenCaptionEditor = true;
					$scope.images[$index].hiddenUrlEditor = true;
					
					setTimeout(function() {
						initialSettings('formProcess');
						$scope.$apply();
					}, 3000);
				}

				$scope.images[$index].updating = false;
			});
		}		
	};

	$scope.destroyImage = function($index) {
		var image = $scope.images[$index];
		var galleryId = $stateParams.galleryId;
		var token = $scope.laravel_token;

		if ( $window.confirm('You are about to destroy this image. This cannot be undone.') ) {
			if ( image ) {
				BespokeGalleries.destroyImage(image.id, token).then(function(response) {
					if ( response.success ) {
						Miscellaneous.addRemoveClass('.alert.animate', 'alert-info alert-danger', 'alert-success');

						$scope.options.hasMessage = true;
						$scope.options.message = "<i class='fa fa-check'></i> " + response.success;						
						$scope.images.splice($index, 1);

						setTimeout(function() {
							initialSettings('formProcess');
							$scope.$apply();
						}, 3000);						
					}
				});
			}
		}
	};

	$scope.toggleEditor = function(type, state) {
		switch(type) {
			case 'caption':
				$scope.options.hiddenCaptionEditor = state;
				break;
			case 'url':
				$scope.options.hiddenUrlEditor = state;
				break;				
		}
	};

	$scope.toggleImagesEditor = function(type, $index, state) {
		switch(type) {
			case 'caption':
				$scope.images[$index].hiddenCaptionEditor = state;
				break;
			case 'url':
				$scope.images[$index].hiddenUrlEditor = state;
				break;				
		}		
	};

	function initialSettings(situation) {
		if ( situation == 'boot' ) {
		  	$scope.editorOptions = {
		    	customConfig: $scope.ckEditorLight
		  	};

			$scope.gallery = null;

			$scope.options = {};
			$scope.options.galleryId = $stateParams.galleryId; 
			$scope.options.locales = [];
			$scope.options.showEditor = false;
			$scope.options.hasMessage = false;
			$scope.options.message = "<i class='fa fa-clock-o'></i> Server Message";

			$scope.options.imageForm = false;
			$scope.options.hiddenCaptionEditor = true;
			$scope.options.hiddenUrlEditor = true;

			$scope.image = {};
			$scope.image.title = '';
			$scope.image.caption = '';
			$scope.image.url = '';
			$scope.image.order = '';
			$scope.image.saving = false;

			$scope.images = [];
		}

		if ( situation == 'formProcess' ) {
			$scope.options.hasMessage = false;
			$scope.options.message = "<i class='fa fa-clock-o'></i> Server message";
			Miscellaneous.addRemoveClass('.alert.animate', 'alert-success alert-danger', 'alert-info');
		}

		if ( situation == 'images' ) {
			$scope.image = {};
			$scope.image.title = '';
			$scope.image.caption = '';
			$scope.image.url = '';
			$scope.image.order = '';
		}
	};

}]);


galleriesAppControllers.controller('TranslationsController', ['$scope', '$stateParams', '$window', 'BespokeGalleries', 'Miscellaneous', function($scope, $stateParams, $window, BespokeGalleries, Miscellaneous) { 
	initialSettings('boot');

	BespokeGalleries.getImage($stateParams.imageId).then(function(data) { 

		data.image.formTranslations = [];

		angular.forEach( data.locales, function( locale, index) {
			data.image.formTranslations.push({ locale_id: locale.id, locale: locale.locale, title: '', caption: '', image_id: data.image.id, hiddenEditor: true, saving: false });
		});

		if ( data.image.imagetranslations.length ) {
			angular.forEach( data.image.imagetranslations, function( translation, index ) {
				angular.forEach( data.image.formTranslations, function(formTranslation, idx) {
					if ( translation.locale_id == formTranslation.locale_id ) {
						formTranslation.id = translation.id;
						formTranslation.title = translation.title;
						formTranslation.caption = translation.caption;
					}
				});
			});
		}

		$scope.image = data.image;

	});

	$scope.processTranslation = function(translation) {
		if ( translation.title.length || translation.caption.length ) {
			initialSettings('formProcess');

			translation.hiddenEditor = true;
			translation.saving = true;

			var token = $scope.laravel_token;

			BespokeGalleries.processTranslation(translation,token).then(function(response) {
				if ( response.validation ) {
					Miscellaneous.addRemoveClass('.alert.animate', 'alert-info alert-success', 'alert-danger');

					$scope.options.hasMessage = true;
					var messages = '';

					angular.forEach( response.validation, function(message, index) {
						messages += "<i class='fa fa-warning'></i> " + message + "<br />";
						Miscellaneous.highlightInput('#' + index, 10000);
					});

					$scope.options.message = messages;
				}

				else if ( response.success ) {
					translation.id = response.id; 

					Miscellaneous.addRemoveClass('.alert.animate', 'alert-info alert-danger', 'alert-success');

					$scope.options.hasMessage = true;
					$scope.options.message = "<i class='fa fa-check'></i> " + response.success;

					setTimeout(function() {
						initialSettings('formProcess');
						$scope.$apply();
					}, 3000);	
				}

				translation.saving = false;
			});  
		}
	};

	$scope.destroyTranslation = function($index) {
		var translation = $scope.image.formTranslations[$index];

		if ( translation.id ) {
			initialSettings('formProcess');

			translation.hiddenEditor = true;
			translation.saving = true;

			var oldTranslation = angular.copy(translation, oldTranslation);

			var token = $scope.laravel_token;

			BespokeGalleries.destroyTranslation(translation,token).then(function(response) {
				if ( response.success ) {
					$scope.image.formTranslations[$index] = { 
						locale_id: oldTranslation.locale_id, 
						locale: oldTranslation.locale, 
						title: '', 
						caption: '', 
						image_id: oldTranslation.image_id, 
						hiddenEditor: oldTranslation.hiddenEditor
					};

					console.log($scope.image.formTranslations[$index]);

					Miscellaneous.addRemoveClass('.alert.animate', 'alert-info alert-danger', 'alert-success');

					$scope.options.hasMessage = true;
					$scope.options.message = "<i class='fa fa-check'></i> " + response.success;

					setTimeout(function() {
						initialSettings('formProcess');
						$scope.$apply();
					}, 3000);	
				}

				$scope.image.formTranslations[$index].saving = false;
			});  	
		}	
	};

	$scope.toggleEditor = function($index, state) {
		$scope.image.formTranslations[$index].hiddenEditor = state;			
	};

	function initialSettings(situation) {
		if ( situation == 'boot' ) {

			$scope.options = {};
			$scope.options.galleryId = $stateParams.galleryId; 
			$scope.options.showEditor = false;
			$scope.options.hasMessage = false;
			$scope.options.message = "<i class='fa fa-clock-o'></i> Server Message";

			$scope.image = {};
		}

		if ( situation == 'formProcess' ) {
			$scope.options.hasMessage = false;
			$scope.options.message = "<i class='fa fa-clock-o'></i> Server message";
			Miscellaneous.addRemoveClass('.alert.animate', 'alert-success alert-danger', 'alert-info');
		}
	};

}]);
