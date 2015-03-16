'use strict';

var postsAppControllers = angular.module('postsApp.controllers', ['ckeditor', 'checklist-model']);

postsAppControllers.controller('PostsController', ['$rootScope', '$scope', '$window', 'Posts', 'BespokePosts', 'Miscellaneous', function($rootScope, $scope, $window, Posts, BespokePosts, Miscellaneous) {
	$rootScope.$on('$stateChangeSuccess', function (ev, to, toParams, from, fromParams) {
		if ( to.name == 'home' ) {
			if ( from.name == 'edit' || from.name == 'create' ) {
				$window.setTimeout(function() {
					$window.location.reload();
				}, 10);
			}
		}
	});

	initialSettings('boot');
    $scope.options.loading = true;

	Posts.get(function(data) {
		var postsArr = [];
		var publishedArr = [];
		var draftsArr = [];
		var trashArr = [];

		angular.forEach(data.posts, function(value, index) {
			if ( value.id ) {
				value.selected = false;
				var categoriesArr = [];
				var categories = '';

				angular.forEach(value.categories, function(category, index) {
					categoriesArr.push(category.name);
				});

				categoriesArr.sort();

				angular.forEach(categoriesArr, function(item, index) {
					categories += item;
					if ( index != (categoriesArr.length - 1) )
						categories += ', ';
				});

				value.category = categories;

				value.translations = [];

				if ( value.posttranslations.length ) {
					angular.forEach(value.posttranslations, function(translation, index) {
						value.translations.push(translation.locale.locale);
					});
				}

				if ( ! value.is_deleted ) {
					postsArr.push(value);

					if ( ! value.is_online )
						draftsArr.push(value);
					else
						publishedArr.push(value);
				}
				else {
					trashArr.push(value);
				}
			}			
		});

		$scope.postCategories.all = postsArr;
		$scope.postCategories.published = publishedArr;
		$scope.postCategories.drafts = draftsArr;
		$scope.postCategories.trash = trashArr;

		$scope.posts = $scope.postCategories.all;

		angular.forEach(data.locales, function(locale, index) {
			$scope.options.locales.push(locale);
		});

        $scope.options.loading = false;
	});


  	$scope.checkAllChange = function(value) {
  		if ( value ) {
  			angular.forEach($scope.posts, function(post, index) {
  				post.selected = true;
  			});

    		$scope.selected = $scope.posts.map(function(post) { return post.id; });

	  		$scope.formFields.hasMessage = true;
	  		$scope.formFields.message = $scope.selected.length + ' items selected';    	
  		}
    	else {
  			angular.forEach($scope.posts, function(post, index) {
  				post.selected = false;
  			});

    		$scope.selected = [];

	  		$scope.formFields.hasMessage = false;
	  		$scope.formFields.message = '0 items selected';  		
    	}

  	};

  	$scope.checkboxChange = function(id, value) {
  		var selectedIndex = -1;
  		var looping = true;

	  	angular.forEach($scope.selected, function(value, index) {
	  		if ( looping ) {
		  		if ( value == id ) {
		  			selectedIndex = index;
		  			looping = false;
		  		}
		  	}
	  	});

	  	if ( ! value && selectedIndex >= 0 )
	  		$scope.selected.splice(selectedIndex, 1);

	  	else if ( value && selectedIndex < 0 )
	  		$scope.selected.push(id);	  	

	  	if ( ! value && $scope.formFields.checkall )
	  		$scope.formFields.checkall = false;

	  	if ( $scope.selected.length == $scope.posts.length )
	  		$scope.formFields.checkall = true;

	  	$scope.formFields.hasMessage = true;
	  	$scope.formFields.message = $scope.selected.length + ' ' + ($scope.selected.length == 1 ? 'item' : 'items') + ' selected';
	};

	$scope.optionLinks = function() {
		return $scope.selected.length ? true : false;
	};

	$scope.preview = function(id) {
		if ( angular.isNumber(id) ) {
			$window.open( BespokePosts.getPreviewLink(id) );			
		}
	};

	$scope.publish = function() {
		if ( $window.confirm('You are about to publish ' + $scope.selected.length + ' ' + ($scope.selected.length == 1 ? 'post' : 'posts') ) ) {
			BespokePosts.doBulkActions('publish', $scope.selected, $scope.laravel_token).then(function(response) {
				if ( response.success ) {
					$window.location.reload();
				}

			});
		}
	};

	$scope.draft = function() {
		if ( $window.confirm('You are about to move ' + $scope.selected.length + ' ' + ($scope.selected.length == 1 ? 'post' : 'posts') + ' to drafts') ) {
			BespokePosts.doBulkActions('draft', $scope.selected, $scope.laravel_token).then(function(response) {
				if ( response.success ) {
					$window.location.reload();
				}

			});
		}
	};

	$scope.trash = function(id) {
		var dataArr = [];

		if ( angular.isNumber(id) )
			dataArr = [id];
		else
			dataArr = $scope.selected;

		if ( $window.confirm('You are about to trash ' + dataArr.length + ' ' + (dataArr.length == 1 ? 'post' : 'posts') ) ) {
			BespokePosts.doBulkActions('trash', dataArr, $scope.laravel_token).then(function(response) {
				if ( response.success ) {
					$window.location.reload();
				}

			});
		}
	};	

	$scope.restore = function(id) {
		var dataArr = [];

		if ( angular.isNumber(id) )
			dataArr = [id];
		else
			dataArr = $scope.selected;

		BespokePosts.doBulkActions('restore', dataArr, $scope.laravel_token).then(function(response) {
			if ( response.success ) {
				$window.location.reload();
			}

		});
	};		

	$scope.destroy = function(id) {
		var dataArr = [];

		if ( angular.isNumber(id) )
			dataArr = [id];
		else
			dataArr = $scope.selected;

		if ( $window.confirm('You are about to destroy ' + dataArr.length + ' ' + (dataArr.length == 1 ? 'post' : 'posts') ) ) {
			BespokePosts.doBulkActions('destroy', dataArr, $scope.laravel_token).then(function(response) {
				if ( response.success ) {
					$window.location.reload();
				}

			});
		}
	};

	$scope.showPosts = function(type) {
		if ( type == 'all' ) {
			$scope.options.activeTab = 'all';
			$scope.posts = $scope.postCategories.all;
		}
		else if ( type == 'published' ) {
			$scope.options.activeTab = 'published';
			$scope.posts = $scope.postCategories.published;
		}
		else if ( type == 'drafts' ) {
			$scope.options.activeTab = 'drafts';
			$scope.posts = $scope.postCategories.drafts;
		}
		else if ( type == 'trash' ) {
			$scope.options.activeTab = 'trash';
			$scope.posts = $scope.postCategories.trash;
		}	

		initialSettings('tabs');
	};

	function initialSettings(situation) {
		if ( situation == 'tabs' ) {		
			$scope.selected = [];
			$scope.options.orderParam = 'default';
			$scope.formFields.checkall = false;	
			$scope.formFields.hasMessage = false;
			$scope.formFields.message = '0 items selected';		

			angular.forEach($scope.posts, function(post, index) {
				post.selected = false;
			});	
		}
		else if ( situation == 'boot' ) {
		  	$scope.postCategories = {};
		  	$scope.postCategories.all = null;
		  	$scope.postCategories.published = null;
		  	$scope.postCategories.drafts = null;
		  	$scope.postCategories.trash = null;
			$scope.posts = null;			
			$scope.currentPage = 1;
		  	$scope.pageSize = 0;		
			$scope.selected = [];
			$scope.formFields = {};
			$scope.formFields.checkall = false;
			$scope.formFields.hasMessage = false;
			$scope.formFields.message = '0 items selected';			
			$scope.options = {};
			$scope.options.activeTab = 'all';
			$scope.options.orderParam = 'default';
			$scope.options.locales = [];	
		}
	};

}]);

postsAppControllers.controller('CreateController', ['$scope', '$window', '$state', 'Posts', 'BespokePosts', 'Miscellaneous', function($scope, $window, $state, Posts, BespokePosts, Miscellaneous) {
	$scope.onReady = function () {
		initialSettings('boot');

		BespokePosts.getCategories().then(function(data) {
			angular.forEach(data.categories, function(name, id) {
				$scope.options.categories.push({ id: id, name: name }); 
			});

			$scope.selects.is_online = $scope.options.onlineOptions[0];
			$scope.options.showEditor = true;
		});

	};

	$scope.processForm = function() {
		$scope.create.is_online = $scope.selects.is_online.value;

		initialSettings('formProcess');
		$scope.options.disabledSubmit = true;

		Posts.save($scope.create, function(response) {
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
				$scope.options.message = "<i class='fa fa-check'></i> " + response.success + " Redirecting you in a few seconds...";
				
				setTimeout(function() {
					$state.go('edit', {id:response.id});
				}, 2000);
			}

			$scope.options.disabledSubmit = false;
		});
	};

	function initialSettings(situation) {
		if ( situation == 'boot' ) {
			$scope.create = {};
			$scope.create.title = null;
			$scope.create.slug = null;
			$scope.create.summary = null;
			$scope.create.content = null;
			$scope.create.categories = [];
			$scope.create.is_online = null;
			$scope.create.order = null;
			$scope.create.create_date = null;

			$scope.selects = {};
			$scope.selects.is_online = null;

			$scope.options = {};
			$scope.options.categories = []; 
			$scope.options.onlineOptions = [{label: 'Draft', value: 0}, {label: 'Publish', value: 1}];
			$scope.options.showEditor = false;
			$scope.options.hasMessage = false;
			$scope.options.message = "<i class='fa fa-clock-o'></i> Post not saved";
			$scope.options.disabledSubmit = false;
		}

		if ( situation == 'formProcess' ) {
			$scope.options.hasMessage = false;
			$scope.options.message = "<i class='fa fa-clock-o'></i> Post not saved";
			Miscellaneous.addRemoveClass('.alert.animate', 'alert-success alert-danger', 'alert-info');
		}
	};
}]);


postsAppControllers.controller('EditController', ['$scope', '$window', '$stateParams', 'Posts', 'BespokePosts', 'Miscellaneous', function($scope, $window, $stateParams, Posts, BespokePosts, Miscellaneous) {
	$scope.onReady = function () {
		initialSettings('boot');

		Posts.get({ id:$stateParams.id }, function(data) {
			$scope.edit.title = data.post.title;
			$scope.edit.slug = data.post.slug;
			$scope.edit.summary = data.post.summary;
			$scope.edit.content = data.post.content;
			$scope.edit.order = data.post.order;
			$scope.edit.create_date = data.post.created_at.split(' ')[0];

			angular.forEach(data.categories, function(name, id) {
				$scope.options.categories.push({ name: name, id: id }); 
			});

			angular.forEach($scope.options.categories, function(category, index) {
				angular.forEach(data.post.categories, function(selectedCategory, selectedCategoryIndex) {
					if ( selectedCategory.id == category.id )
						$scope.selects.categories.push($scope.options.categories[index]);
				});
			});

			angular.forEach(data.post.posttranslations, function(translation, index) {
				if ( translation.locale ) {
					$scope.options.translations.push(translation.locale.locale);
				}
			});

			angular.forEach(data.post.postmeta, function(meta, index) {
				meta.updating = false;
				meta.hiddenEditor = true;
				$scope.customFields.push(meta);
			});

			$scope.options.metaKeys = data.metaKeys;

			angular.forEach(data.locales, function(locale, index) {
				$scope.options.locales.push(locale);
			});	

			$scope.selects.is_online = data.post.is_online ? $scope.options.onlineOptions[1] : $scope.options.onlineOptions[0];

			$scope.options.showEditor = true;
		});

	};

	$scope.processForm = function() {
		$scope.edit.is_online = $scope.selects.is_online.value;

		angular.forEach($scope.selects.categories, function(selectedCategory, index) {
			$scope.edit.categories.push(selectedCategory.id);
		});

		initialSettings('formProcess');
		$scope.options.disabledSubmit = true;

		Posts.update({ id:$stateParams.id }, { post:$scope.edit, customFields:$scope.customFields}, function(response) {
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
				$scope.edit.slug = response.slug;
				Miscellaneous.addRemoveClass('.alert.animate', 'alert-info alert-danger', 'alert-success');

				$scope.options.hasMessage = true;
				$scope.options.message = "<i class='fa fa-check'></i> " + response.success;
				
				setTimeout(function() {
					initialSettings('formProcess');
					$scope.$apply();
				}, 10000);
			}

			$scope.options.disabledSubmit = false;
		});
	};

	$scope.showCustomFieldForm = function() {
		$scope.options.customFieldForm = true;
	};

	$scope.hideCustomFieldForm = function() {
		$scope.options.customFieldForm = false;
	};

	$scope.addCustomField = function(customField) {
		var postId = $stateParams.id;
		var token = $scope.laravel_token;
		if ( customField.meta_key.length && customField.meta_value.length ) {
			
			$scope.custom.saving = true;

			BespokePosts.saveCustomField('post', postId, customField, token).then(function(response) {
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

					response.customField.updating = false;
					response.customField.hiddenEditor = true;

					$scope.customFields.push(response.customField);
					$scope.options.customFieldForm = false;

					var idx = $scope.options.metaKeys.indexOf(response.customField.meta_key);
					if ( idx > -1 )
						$scope.options.metaKeys.splice(idx, 1);
					
					setTimeout(function() {
						$scope.options.hiddenEditor = true;

						initialSettings('formProcess');
						initialSettings('customFields');
						$scope.$apply();
					}, 100);
				}

				$scope.custom.saving = false;
			});
		}
	};

	$scope.updateCustomField = function($index) {
		var customField = $scope.customFields[$index];
		var postId = $stateParams.id;
		var token = $scope.laravel_token;		
		if ( customField ) {
			$scope.customFields[$index].updating = true;

			BespokePosts.updateCustomField('post', postId, customField, token).then(function(response) {
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

					$scope.customFields[$index].meta_key = response.metaKey;
					$scope.customFields[$index].hiddenEditor = true;
					
					setTimeout(function() {
						initialSettings('formProcess');
						$scope.$apply();
					}, 100);
				}

				$scope.customFields[$index].updating = false;
			});
		}		
	};

	$scope.destroyCustomField = function($index) {
		var customField = $scope.customFields[$index];
		var postId = $stateParams.id;
		var token = $scope.laravel_token;

		if ( $window.confirm('You are about to destroy this custom field. This cannot be undone.') ) {
			if ( customField ) {
				BespokePosts.destroyCustomField('post', postId, customField, token).then(function(response) {
					if ( response.success ) {
						$scope.options.metaKeys.push(customField.meta_key);
						$scope.options.metaKeys.sort();
						$scope.customFields.splice($index, 1);
					}
				});
			}
		}
	};

	$scope.toggleSelect = function(state) {
		$scope.options.isSelect = state;
	};

	$scope.toggleEditor = function(state) {
		$scope.options.hiddenEditor = state;
	};

	$scope.toggleFieldsEditor = function($index, state) {
		$scope.customFields[$index].hiddenEditor = state;
	};

	function initialSettings(situation) {
		if ( situation == 'boot' ) {
		  	$scope.editorOptions = {
		    	customConfig: $scope.ckEditorLight
		  	};			
			$scope.edit = {};
			$scope.edit.title = null;
			$scope.edit.slug = null;
			$scope.edit.summary = null;
			$scope.edit.content = null;
			$scope.edit.categories = [];
			$scope.edit.is_online = null;
			$scope.edit.order = null;
			$scope.edit.create_date = null;

			$scope.selects = {};
			$scope.selects.categories = [];
			$scope.selects.is_online = null;

			$scope.options = {};
			$scope.options.id = $stateParams.id;
			$scope.options.locales = [];
			$scope.options.translations = [];			
			$scope.options.categories = []; 
			$scope.options.onlineOptions = [{label: 'Draft', value: 0}, {label: 'Publish', value: 1}];
			$scope.options.showEditor = false;
			$scope.options.hasMessage = false;
			$scope.options.message = "<i class='fa fa-clock-o'></i> Post not saved";
			$scope.options.disabledSubmit = false;

			$scope.options.customFieldForm = false;
			$scope.options.isSelect = true;
			$scope.options.metaKeys = [];
			$scope.options.hiddenEditor = true;

			$scope.custom = {};
			$scope.custom.meta_key = '';
			$scope.custom.meta_value = '';
			$scope.custom.saving = false;

			$scope.customFields = [];			
		}

		if ( situation == 'formProcess' ) {
			$scope.options.hasMessage = false;
			$scope.options.message = "<i class='fa fa-clock-o'></i> Post not saved";
			Miscellaneous.addRemoveClass('.alert.animate', 'alert-success alert-danger', 'alert-info');
		}

		if ( situation == 'customFields' ) {
			$scope.custom = {};
			$scope.custom.meta_key = '';
			$scope.custom.meta_value = '';		
			$scope.options.isSelect = true;	
		}		
	};
}]);


postsAppControllers.controller('CreateTranslationController', ['$scope', '$window', '$state', '$stateParams', 'Posts', 'BespokePosts', 'Miscellaneous', function($scope, $window, $state, $stateParams, Posts, BespokePosts, Miscellaneous) {
	$scope.onReady = function () {
		initialSettings('boot');

		BespokePosts.getLocale($stateParams.localeId).then(function(data) {
			if ( data.locale ) {
				$scope.options.locale = data.locale.locale;
				$scope.selects.is_online = $scope.options.onlineOptions[0];
				$scope.options.showEditor = true;
			}
		});

	};

	$scope.processForm = function() {
		$scope.create.is_online = $scope.selects.is_online.value;
		initialSettings('formProcess');

		$scope.options.disabledSubmit = true;

		var postId = $stateParams.postId;
		var localeId = $stateParams.localeId;

		BespokePosts.saveTranslation($scope.create, postId, localeId, $scope.laravel_token).then(function(response) {
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
				$scope.options.message = "<i class='fa fa-check'></i> " + response.success + ". Redirecting you in a few seconds...";
				
				setTimeout(function() {
					$state.go('editTranslation', {postId:postId, localeId:localeId});
				}, 2000);
			}

			$scope.options.disabledSubmit = false;
		});
	};

	function initialSettings(situation) {
		if ( situation == 'boot' ) {
			$scope.create = {};
			$scope.create.title = null;
			$scope.create.slug = null;
			$scope.create.summary = null;
			$scope.create.content = null;
			$scope.create.is_online = null;
			$scope.create.create_date = null;

			$scope.selects = {};
			$scope.selects.is_online = null;

			$scope.options = {};
			$scope.options.postId = $stateParams.postId;
			$scope.options.onlineOptions = [{label: 'Draft', value: 0}, {label: 'Publish', value: 1}];
			$scope.options.showEditor = false;
			$scope.options.hasMessage = false;
			$scope.options.message = "<i class='fa fa-clock-o'></i> Post not saved";
			$scope.options.disabledSubmit = false;
		}

		if ( situation == 'formProcess' ) {
			$scope.options.hasMessage = false;
			$scope.options.message = "<i class='fa fa-clock-o'></i> Post not saved";
			Miscellaneous.addRemoveClass('.alert.animate', 'alert-success alert-danger', 'alert-info');
		}
	};
}]);

postsAppControllers.controller('EditTranslationController', ['$scope', '$window', '$state', '$stateParams', 'BespokePosts', 'Miscellaneous', function($scope, $window, $state, $stateParams, BespokePosts, Miscellaneous) {

	$scope.onReady = function () {
		initialSettings('boot');

		BespokePosts.getTranslation($stateParams.postId, $stateParams.localeId).then(function(data) {
			if ( data.translation ) {
				$scope.edit.id = data.translation.id;
				$scope.edit.title = data.translation.title;
				$scope.edit.slug = data.translation.slug;
				$scope.edit.summary = data.translation.summary;
				$scope.edit.content = data.translation.content;
				$scope.edit.create_date = data.translation.created_at.split(' ')[0];

				$scope.selects.is_online = data.translation.is_online ? $scope.options.onlineOptions[1] : $scope.options.onlineOptions[0];
				$scope.options.locale = data.translation.locale.locale;
				$scope.options.showEditor = true;

				angular.forEach(data.translation.posttranslationmeta, function(meta, index) {
					meta.updating = false;
					meta.hiddenEditor = true;
					$scope.customFields.push(meta);
				});

				$scope.options.metaKeys = data.metaKeys;
			}
		});
	};

	$scope.processForm = function() {
		$scope.edit.is_online = $scope.selects.is_online.value;
		initialSettings('formProcess');

		$scope.options.disabledSubmit = true;

		BespokePosts.updateTranslation($scope.edit, $scope.customFields, $scope.laravel_token).then(function(response) {
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
				$scope.edit.slug = response.slug;
				Miscellaneous.addRemoveClass('.alert.animate', 'alert-info alert-danger', 'alert-success');

				$scope.options.hasMessage = true;
				$scope.options.message = "<i class='fa fa-check'></i> " + response.success;
				
				setTimeout(function() {
					initialSettings('formProcess');
					$scope.$apply();
				}, 10000);
			}

			$scope.options.disabledSubmit = false;
		});

	};

	$scope.destroy = function() {
		var id = $scope.edit.id;
		var postId = $scope.options.postId;
		var token = $scope.laravel_token;

		if ( $window.confirm('You are about to destroy this translation. This cannot be undone.') ) {
			BespokePosts.destroyTranslation(id, token).then(function(response) {
				if ( response.success ) {

				Miscellaneous.addRemoveClass('.alert.animate', 'alert-info alert-danger', 'alert-success');
				$scope.options.hasMessage = true;
				$scope.options.message = "<i class='fa fa-check'></i> " + response.success + ". Redirecting you in a few seconds...";

					setTimeout(function() {
						$state.go('edit', {id:postId});
					}, 2000);
				}
			});
		}
	};

	$scope.showCustomFieldForm = function() {
		$scope.options.customFieldForm = true;
	};

	$scope.hideCustomFieldForm = function() {
		$scope.options.customFieldForm = false;
	};

	$scope.addCustomField = function(customField) {
		var postId = $scope.edit.id;
		var token = $scope.laravel_token;
		if ( customField.meta_key.length && customField.meta_value.length ) {
			
			$scope.custom.saving = true;

			BespokePosts.saveCustomField('translation', postId, customField, token).then(function(response) {
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

					response.customField.updating = false;
					response.customField.hiddenEditor = true;

					$scope.customFields.push(response.customField);
					$scope.options.customFieldForm = false;

					var idx = $scope.options.metaKeys.indexOf(response.customField.meta_key);
					if ( idx > -1 )
						$scope.options.metaKeys.splice(idx, 1);
					
					setTimeout(function() {
						$scope.options.hiddenEditor = true;

						initialSettings('formProcess');
						initialSettings('customFields');
						$scope.$apply();
					}, 100);
				}

				$scope.custom.saving = false;
			});
		}
	};

	$scope.updateCustomField = function($index) {
		var customField = $scope.customFields[$index];
		var postId = $scope.edit.id;
		var token = $scope.laravel_token;		

		if ( customField ) {
			$scope.customFields[$index].updating = true;

			BespokePosts.updateCustomField('translation', postId, customField, token).then(function(response) {
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

					$scope.customFields[$index].meta_key = response.metaKey;
					$scope.customFields[$index].hiddenEditor = true;
					
					setTimeout(function() {
						initialSettings('formProcess');
						$scope.$apply();
					}, 100);
				}

				$scope.customFields[$index].updating = false;
			});
		}		
	};

	$scope.destroyCustomField = function($index) {
		var customField = $scope.customFields[$index];
		var postId = $scope.edit.id;
		var token = $scope.laravel_token;

		if ( $window.confirm('You are about to destroy this custom field. This cannot be undone.') ) {
			if ( customField ) {
				BespokePosts.destroyCustomField('translation', postId, customField, token).then(function(response) {
					if ( response.success ) {
						$scope.options.metaKeys.push(customField.meta_key);
						$scope.options.metaKeys.sort();
						$scope.customFields.splice($index, 1);
					}
				});
			}
		}
	};

	$scope.toggleSelect = function(state) {
		$scope.options.isSelect = state;
	};

	$scope.toggleEditor = function(state) {
		$scope.options.hiddenEditor = state;
	};

	$scope.toggleFieldsEditor = function($index, state) {
		$scope.customFields[$index].hiddenEditor = state;
	};

	function initialSettings(situation) {
		if ( situation == 'boot' ) {
		  	$scope.editorOptions = {
		    	customConfig: $scope.ckEditorLight
		  	};			
			$scope.edit = {};
			$scope.edit.id = null;
			$scope.edit.title = null;
			$scope.edit.slug = null;
			$scope.edit.summary = null;
			$scope.edit.content = null;
			$scope.edit.create_date = null;
			$scope.edit.is_online = null;

			$scope.selects = {};
			$scope.selects.is_online = null;

			$scope.options = {};
			$scope.options.postId = $stateParams.postId;
			$scope.options.locale = null;
			$scope.options.onlineOptions = [{label: 'Draft', value: 0}, {label: 'Publish', value: 1}];
			$scope.options.showEditor = false;
			$scope.options.hasMessage = false;
			$scope.options.message = "<i class='fa fa-clock-o'></i> Translation not saved";
			$scope.options.disabledSubmit = false;

			$scope.options.customFieldForm = false;
			$scope.options.isSelect = true;
			$scope.options.metaKeys = [];
			$scope.options.hiddenEditor = true;

			$scope.custom = {};
			$scope.custom.meta_key = '';
			$scope.custom.meta_value = '';
			$scope.custom.saving = false;

			$scope.customFields = [];			
		}

		if ( situation == 'formProcess' ) {
			$scope.options.hasMessage = false;
			$scope.options.message = "<i class='fa fa-clock-o'></i> Translation not saved";
			Miscellaneous.addRemoveClass('.alert.animate', 'alert-success alert-danger', 'alert-info');
		}

		if ( situation == 'customFields' ) {
			$scope.custom = {};
			$scope.custom.meta_key = '';
			$scope.custom.meta_value = '';		
			$scope.options.isSelect = true;	
		}			
	};

}]);