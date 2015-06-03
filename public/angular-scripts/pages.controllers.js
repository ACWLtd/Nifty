'use strict';

var pagesAppControllers = angular.module('pagesApp.controllers', ['ckeditor']);

pagesAppControllers.controller('PagesController', ['$rootScope', '$scope', '$window', 'Pages', 'BespokePages', 'Miscellaneous', function($rootScope, $scope, $window, Pages, BespokePages, Miscellaneous) {
	
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

	Pages.get(function(data) {
		var pagesArr = [];
		var publishedArr = [];
		var draftsArr = [];
		var trashArr = [];

		angular.forEach(data.pagesTree, function(value, index) {
			if ( value.id ) {
				value.level = 1;
				value.selected = false;
				value.translations = [];

				if ( value.pagetranslations.length ) {
					angular.forEach(value.pagetranslations, function(translation, index) {
						value.translations.push(translation.locale.locale);
					});
				}

				if ( ! value.is_deleted ) {
					pagesArr.push(value);

					if ( ! value.is_online )
						draftsArr.push(value);
					else
						publishedArr.push(value);
				}
				else {
					trashArr.push(value);
				}
			}
			else {
				Miscellaneous.childIterator( pagesArr, publishedArr, draftsArr, trashArr, value, 2 );
			}			
		});

		$scope.pageCategories.all = pagesArr;
		$scope.pageCategories.published = publishedArr;
		$scope.pageCategories.drafts = draftsArr;
		$scope.pageCategories.trash = trashArr;

		$scope.pages = $scope.pageCategories.all;

		angular.forEach(data.locales, function(locale, index) {
			$scope.options.locales.push(locale);
		});

		$scope.options.loading = false;
	});

	function initialSettings(situation) {
		if ( situation == 'tabs' ) {		
			$scope.selected = [];
			$scope.options.orderParam = 'lft';
			$scope.formFields.checkall = false;	
			$scope.formFields.hasMessage = false;
			$scope.formFields.message = '0 items selected';		

			angular.forEach($scope.pages, function(page, index) {
				page.selected = false;
			});	
		}
		else if ( situation == 'boot' ) {
		  	$scope.pageCategories = {};
		  	$scope.pageCategories.all = null;
		  	$scope.pageCategories.published = null;
		  	$scope.pageCategories.drafts = null;
		  	$scope.pageCategories.trash = null;
			$scope.pages = null;			
			$scope.currentPage = 1;
		  	$scope.pageSize = 0; //all		
			$scope.selected = [];
			$scope.formFields = {};
			$scope.formFields.checkall = false;
			$scope.formFields.hasMessage = false;
			$scope.formFields.message = '0 items selected';			
			$scope.options = {};
			$scope.options.activeTab = 'all';
			$scope.options.orderParam = 'lft';	
			$scope.options.locales = [];
		}
	};

	$scope.jsonParse = function(string) {
		return angular.fromJson(string);
	};

	$scope.childCrumbs = function(num) {
		return Miscellaneous.childCrumbs(num, '&raquo;');
	};

	$scope.showChildCrumbs = function(level,sort) {
		if ( level > 1 && sort == 'lft' && $scope.options.activeTab == 'all' )
			return true;
		else
			return false;
	};	

  	$scope.checkAllChange = function(value) {
  		if ( value ) {
  			angular.forEach($scope.pages, function(page, index) {
  				page.selected = true;
  			});

    		$scope.selected = $scope.pages.map(function(page) { return page.id; });

	  		$scope.formFields.hasMessage = true;
	  		$scope.formFields.message = $scope.selected.length + ' items selected';    	
  		}
    	else {
  			angular.forEach($scope.pages, function(page, index) {
  				page.selected = false;
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

	  	if ( $scope.selected.length == $scope.pages.length )
	  		$scope.formFields.checkall = true;

	  	$scope.formFields.hasMessage = true;
	  	$scope.formFields.message = $scope.selected.length + ' ' + ($scope.selected.length == 1 ? 'item' : 'items') + ' selected';
	};

	$scope.optionLinks = function() {
		return $scope.selected.length ? true : false;
	};

	$scope.publish = function() {
		if ( $window.confirm('You are about to publish ' + $scope.selected.length + ' ' + ($scope.selected.length == 1 ? 'page' : 'pages') ) ) {
			BespokePages.doBulkActions('publish', $scope.selected, $scope.laravel_token).then(function(response) {
				if ( response.success ) {
					$window.location.reload();
				}

			});
		}
	};

	$scope.draft = function() {
		if ( $window.confirm('You are about to move ' + $scope.selected.length + ' ' + ($scope.selected.length == 1 ? 'page' : 'pages') + ' to drafts') ) {
			BespokePages.doBulkActions('draft', $scope.selected, $scope.laravel_token).then(function(response) {
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

		if ( $window.confirm('Warning: This will also trash all descendant pages.') ) {
			BespokePages.doBulkActions('trash', dataArr, $scope.laravel_token).then(function(response) {
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

		if ( $window.confirm('Warning: This will also restore respective parent pages in trash.') ) {
			BespokePages.doBulkActions('restore', dataArr, $scope.laravel_token).then(function(response) {
				if ( response.success ) {
					$window.location.reload();
				}

			});
		}
	};		

	$scope.destroy = function(id) {
		var dataArr = [];

		if ( angular.isNumber(id) )
			dataArr = [id];
		else
			dataArr = $scope.selected;

		if ( $window.confirm('Warning: This will also destroy respective descendant pages in trash.') ) {
			BespokePages.doBulkActions('destroy', dataArr, $scope.laravel_token).then(function(response) {
				if ( response.success ) {
					$window.location.reload();
				}

			});
		}
	};

	$scope.preview = function(id) {
		if ( angular.isNumber(id) ) {
			$window.open( BespokePages.getPreviewLink(id) );			
		}
	};

	$scope.showPages = function(type) {
		if ( type == 'all' ) {
			$scope.options.activeTab = 'all';
			$scope.pages = $scope.pageCategories.all;
		}
		else if ( type == 'published' ) {
			$scope.options.activeTab = 'published';
			$scope.pages = $scope.pageCategories.published;
		}
		else if ( type == 'drafts' ) {
			$scope.options.activeTab = 'drafts';
			$scope.pages = $scope.pageCategories.drafts;
		}
		else if ( type == 'trash' ) {
			$scope.options.activeTab = 'trash';
			$scope.pages = $scope.pageCategories.trash;
		}	

		initialSettings('tabs');
	};

}]);

pagesAppControllers.controller('CreateController', ['$scope', '$window', '$state', 'Pages', 'BespokePages', 'Miscellaneous', function($scope, $window, $state, Pages, BespokePages, Miscellaneous) {

	initialSettings('boot');
	$scope.options.loading = true;

	$scope.onReady = function() {
		$scope.options.showEditor = true;
	};

	Pages.get(function(data) {
		var pagesArr = [];
		angular.forEach(data.pagesTree, function(value) {
			if ( value.id ) {
				value.level = 1;
				if ( ! value.is_deleted )
					pagesArr.push(value);
			}
			else {
				Miscellaneous.childSelectsIterator( pagesArr, value, 0, 2 );
			}
		});

		var parents = [{ label : '(no parent)', value: 0 }];

		angular.forEach(pagesArr, function(page) {
			var preLbl = '';
			for( var i = 1; i < page.level; i++ ) {
				preLbl += '—';
			}

			parents.push({ label: preLbl + page.title, value: page.id});
		});

		$scope.options.parents = parents;
		$scope.selects.parent_id = $scope.options.parents[0];
		$scope.selects.is_online = $scope.options.onlineOptions[0];
		$scope.options.loading = false;
	});

	$scope.processForm = function() {
		$scope.create.parent_id = $scope.selects.parent_id.value;
		$scope.create.is_online = $scope.selects.is_online.value;

		initialSettings('formProcess');
		$scope.options.disabledSubmit = true;

		Pages.save($scope.create, function(response) {
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
			$scope.create.parent_id = null;
			$scope.create.is_online = null;
			$scope.create.order = null;
			$scope.create.create_date = null;

			$scope.selects = {};
			$scope.selects.parent_id = null;
			$scope.selects.is_online = null;

			$scope.options = {};
			$scope.options.parents = []; 
			$scope.options.onlineOptions = [{label: 'Draft', value: 0}, {label: 'Publish', value: 1}];
			$scope.options.showEditor = false;
			$scope.options.hasMessage = false;
			$scope.options.message = "<i class='fa fa-clock-o'></i> Page not saved";
			$scope.options.disabledSubmit = false;
		}

		if ( situation == 'formProcess' ) {
			$scope.options.hasMessage = false;
			$scope.options.message = "<i class='fa fa-clock-o'></i> Page not saved";
			Miscellaneous.addRemoveClass('.alert.animate', 'alert-success alert-danger', 'alert-info');
		}
	};

}]);


pagesAppControllers.controller('EditController', ['$scope', '$window', '$stateParams', 'Pages', 'BespokePages', 'Miscellaneous', function($scope, $window, $stateParams, Pages, BespokePages, Miscellaneous) {

	initialSettings('boot');
	$scope.options.loading = true;
	$scope.onReady = function() {
		$scope.options.showEditor = true;
	};

	Pages.get({ id:$stateParams.id }, function(data) {
		$scope.edit.title = data.page.title;
		$scope.edit.slug = data.page.slug;
		$scope.edit.summary = data.page.summary;
		$scope.edit.content = data.page.content;
		$scope.edit.order = data.page.order;
		$scope.edit.create_date = data.page.created_at.split(' ')[0];

		var pagesArr = [];
		angular.forEach(data.pagesTree, function(value) {
			if ( value.id ) {
				value.level = 1;
				if ( ! value.is_deleted && value.id != $stateParams.id )
					pagesArr.push(value);
			}
			else {
				Miscellaneous.childSelectsIterator( pagesArr, value, $stateParams.id, 2 );
			}
		});

		var parents = [{ label : '(no parent)', value: 0 }];

		angular.forEach(pagesArr, function(page) {
			var preLbl = '';
			for( var i = 1; i < page.level; i++ ) {
				preLbl += '—';
			}

			parents.push({ label: preLbl + page.title, value: page.id});
		});

		$scope.options.parents = parents;

		angular.forEach($scope.options.parents, function(parent, index) {
			if ( parent.value == data.page.parent_id )
				$scope.selects.parent_id = $scope.options.parents[index];
		});

		angular.forEach(data.page.pagetranslations, function(translation, index) {
			if ( translation.locale ) {
				$scope.options.translations.push(translation.locale.locale);
			}
		});

		angular.forEach(data.page.pagemeta, function(meta, index) {
			meta.updating = false;
			meta.hiddenEditor = true;
			$scope.customFields.push(meta);
		});

		$scope.options.metaKeys = data.metaKeys;

		angular.forEach(data.locales, function(locale, index) {
			$scope.options.locales.push(locale);
		});

		if ( ! $scope.selects.parent_id )
			$scope.selects.parent_id = $scope.options.parents[0];

		$scope.selects.is_online = data.page.is_online ? $scope.options.onlineOptions[1] : $scope.options.onlineOptions[0];

		$scope.options.showEditor = true;
		$scope.options.loading = false;

	});

	$scope.processForm = function() {
		$scope.edit.parent_id = $scope.selects.parent_id.value;
		$scope.edit.is_online = $scope.selects.is_online.value;

		initialSettings('formProcess');
		$scope.options.disabledSubmit = true;

		Pages.update({ id:$stateParams.id }, { page:$scope.edit, customFields:$scope.customFields}, function(response) {
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
		// initialSettings('customFields');
	};

	$scope.addCustomField = function(customField) {
		var pageId = $stateParams.id;
		var token = $scope.laravel_token;
		if ( customField.meta_key.length && customField.meta_value.length ) {
			
			$scope.custom.saving = true;

			BespokePages.saveCustomField('page', pageId, customField, token).then(function(response) {
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
		var pageId = $stateParams.id;
		var token = $scope.laravel_token;		
		if ( customField ) {
			$scope.customFields[$index].updating = true;

			BespokePages.updateCustomField('page', pageId, customField, token).then(function(response) {
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
		var pageId = $stateParams.id;
		var token = $scope.laravel_token;

		if ( $window.confirm('You are about to destroy this custom field. This cannot be undone.') ) {
			if ( customField ) {
				BespokePages.destroyCustomField('page', pageId, customField, token).then(function(response) {
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
			$scope.edit.parent_id = null;
			$scope.edit.is_online = null;
			$scope.edit.order = null;
			$scope.edit.create_date = null;

			$scope.selects = {};
			$scope.selects.parent_id = null;
			$scope.selects.is_online = null;

			$scope.options = {};
			$scope.options.id = $stateParams.id;
			$scope.options.parents = []; 
			$scope.options.locales = [];
			$scope.options.translations = [];
			$scope.options.onlineOptions = [{label: 'Draft', value: 0}, {label: 'Publish', value: 1}];
			$scope.options.showEditor = false;
			$scope.options.hasMessage = false;
			$scope.options.message = "<i class='fa fa-clock-o'></i> Page not saved";
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
			$scope.options.message = "<i class='fa fa-clock-o'></i> Page not saved";
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


pagesAppControllers.controller('CreateTranslationController', ['$scope', '$window', '$state', '$stateParams', 'BespokePages', 'Miscellaneous', function($scope, $window, $state, $stateParams, BespokePages, Miscellaneous) {

	$scope.onReady = function () {
		initialSettings('boot');

		BespokePages.getLocale($stateParams.localeId).then(function(data) {
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

		var pageId = $stateParams.pageId;
		var localeId = $stateParams.localeId;

		BespokePages.saveTranslation($scope.create, pageId, localeId, $scope.laravel_token).then(function(response) {
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
					$state.go('editTranslation', {pageId:pageId, localeId:localeId});
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
			$scope.options.pageId = $stateParams.pageId;
			$scope.options.onlineOptions = [{label: 'Draft', value: 0}, {label: 'Publish', value: 1}];
			$scope.options.showEditor = false;
			$scope.options.hasMessage = false;
			$scope.options.message = "<i class='fa fa-clock-o'></i> Page not saved";
			$scope.options.disabledSubmit = false;
		}

		if ( situation == 'formProcess' ) {
			$scope.options.hasMessage = false;
			$scope.options.message = "<i class='fa fa-clock-o'></i> Page not saved";
			Miscellaneous.addRemoveClass('.alert.animate', 'alert-success alert-danger', 'alert-info');
		}
	};

}]);


pagesAppControllers.controller('EditTranslationController', ['$scope', '$window', '$state', '$stateParams', 'BespokePages', 'Miscellaneous', function($scope, $window, $state, $stateParams, BespokePages, Miscellaneous) {

	$scope.onReady = function () {
		initialSettings('boot');

		BespokePages.getTranslation($stateParams.pageId, $stateParams.localeId).then(function(data) {
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

				angular.forEach(data.translation.pagetranslationmeta, function(meta, index) {
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

		BespokePages.updateTranslation($scope.edit, $scope.customFields, $scope.laravel_token).then(function(response) {
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
		var pageId = $scope.options.pageId;
		var token = $scope.laravel_token;

		if ( $window.confirm('You are about to destroy this translation. This cannot be undone.') ) {
			BespokePages.destroyTranslation(id, token).then(function(response) {
				if ( response.success ) {

				Miscellaneous.addRemoveClass('.alert.animate', 'alert-info alert-danger', 'alert-success');
				$scope.options.hasMessage = true;
				$scope.options.message = "<i class='fa fa-check'></i> " + response.success + " Redirecting you in a few seconds...";

					setTimeout(function() {
						$state.go('edit', {id:pageId});
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
		var pageId = $scope.edit.id;
		var token = $scope.laravel_token;

		if ( customField.meta_key.length && customField.meta_value.length ) {
			
			$scope.custom.saving = true;

			BespokePages.saveCustomField('translation', pageId, customField, token).then(function(response) {
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
		var pageId = $scope.edit.id;
		var token = $scope.laravel_token;		
		
		if ( customField ) {
			$scope.customFields[$index].updating = true;

			BespokePages.updateCustomField('translation', pageId, customField, token).then(function(response) {
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
		var pageId = $scope.edit.id;
		var token = $scope.laravel_token;

		if ( $window.confirm('You are about to destroy this custom field. This cannot be undone.') ) {
			if ( customField ) {
				BespokePages.destroyCustomField('translation', pageId, customField, token).then(function(response) {
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
			$scope.edit.is_online = null;
			$scope.edit.create_date = null;

			$scope.selects = {};
			$scope.selects.is_online = null;

			$scope.options = {};
			$scope.options.pageId = $stateParams.pageId;
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