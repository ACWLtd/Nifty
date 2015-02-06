'use strict';

var eventsAppControllers = angular.module('eventsApp.controllers', []);

eventsAppControllers.controller('EventsController', ['$rootScope', '$scope', '$window', 'Events', 'BespokeEvents', 'Miscellaneous', function($rootScope, $scope, $window, Events, BespokeEvents, Miscellaneous) {
	
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

	Events.get(function(data) {
		var eventsArr = [];
		var approvedArr = [];
		var pendingApprovalArr = [];

		angular.forEach(data.events, function(value, index) {
			if ( value.id ) {
				value.selected = false;

				eventsArr.push(value);

				if ( ! value.is_approved )
					pendingApprovalArr.push(value);
				else
					approvedArr.push(value);
			}		
		});

		$scope.eventCategories.all = eventsArr;
		$scope.eventCategories.approved = approvedArr;
		$scope.eventCategories.pending = pendingApprovalArr;
		$scope.eventCategories.past = data.past_events;

		$scope.events = $scope.eventCategories.all;	

		Miscellaneous.storeUser(data.user);
	});

	function initialSettings(situation) {
		if ( situation == 'tabs' ) {		
			$scope.selected = [];
			$scope.options.orderParam = 'default';
			$scope.formFields.checkall = false;	
			$scope.formFields.hasMessage = false;
			$scope.formFields.message = '0 items selected';		

			angular.forEach($scope.events, function(anEvent, index) {
				anEvent.selected = false;
			});	
		}
		else if ( situation == 'boot' ) {
		  	$scope.eventCategories = {};
		  	$scope.eventCategories.all = [];
		  	$scope.eventCategories.approved = [];
		  	$scope.eventCategories.pending = [];
		  	$scope.eventCategories.past = null;

			$scope.events = null;

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
		}
	};

  	$scope.checkAllChange = function(value) {
  		if ( value ) {
  			angular.forEach($scope.events, function(anEvent, index) {
  				anEvent.selected = true;
  			});

    		$scope.selected = $scope.events.map(function(anEvent) { return anEvent.id; });

	  		$scope.formFields.hasMessage = true;
	  		$scope.formFields.message = $scope.selected.length + ' items selected';    	
  		}
    	else {
  			angular.forEach($scope.events, function(anEvent, index) {
  				anEvent.selected = false;
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

	  	if ( $scope.selected.length == $scope.events.length )
	  		$scope.formFields.checkall = true;

	  	$scope.formFields.hasMessage = true;
	  	$scope.formFields.message = $scope.selected.length + ' ' + ($scope.selected.length == 1 ? 'item' : 'items') + ' selected';
	};

	$scope.optionLinks = function() {
		return $scope.selected.length ? true : false;
	};

	$scope.approve = function() {
		if ( $window.confirm('You are about to approve ' + $scope.selected.length + ' ' + ($scope.selected.length == 1 ? 'event' : 'events') ) ) {
			BespokeEvents.doBulkActions('approve', $scope.selected, $scope.laravel_token).then(function(response) {
				if ( response.success ) {
					$window.location.reload();
				}

			});
		}
	};

	$scope.unApprove = function() {
		if ( $window.confirm('You are about to un-approve ' + $scope.selected.length + ' ' + ($scope.selected.length == 1 ? 'event' : 'events')) ) {
			BespokeEvents.doBulkActions('unapprove', $scope.selected, $scope.laravel_token).then(function(response) {
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

		if ( $window.confirm('Warning: This cannot be undone.') ) {
			BespokeEvents.doBulkActions('destroy', dataArr, $scope.laravel_token).then(function(response) {
				if ( response.success ) {
					$window.location.reload();
				}

			});
		}
	};

	$scope.showEvents = function(type) {
		if ( type == 'all' ) {
			$scope.options.activeTab = 'all';
			$scope.events = $scope.eventCategories.all;
		}
		else if ( type == 'approved' ) {
			$scope.options.activeTab = 'approved';
			$scope.events = $scope.eventCategories.approved;
		}
		else if ( type == 'pending' ) {
			$scope.options.activeTab = 'pending';
			$scope.events = $scope.eventCategories.pending;
		}

		else if ( type == 'past' ) {
			$scope.options.activeTab = 'past';
			$scope.events = $scope.eventCategories.past;
		}

		initialSettings('tabs');
	};

}]);

eventsAppControllers.controller('CreateController', ['$scope', '$window', '$localStorage', '$state', 'Events', 'BespokeEvents', 'Miscellaneous', function($scope, $window, $localStorage, $state, Events, BespokeEvents, Miscellaneous) {

	initialSettings('boot');
	$scope.selects.type = $scope.options.typeOptions[0];

	$scope.processForm = function() {
		$scope.newEvent.type = $scope.selects.type.type;

		initialSettings('formProcess');
		$scope.options.disabledSubmit = true;

		Events.save($scope.newEvent, function(response) {
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
			$scope.options = {};
			$scope.options.typeOptions = [{type: 'RBM Events'}, {type: 'Advocacy'}, {type: 'Conferences'}, {type: 'Training/Education'}, {type: 'Other Events'}];
			$scope.options.hasMessage = false;
			$scope.options.message = "<i class='fa fa-clock-o'></i> Event not saved";
			$scope.options.disabledSubmit = false;
			$scope.options.user = Miscellaneous.getUser();

			$scope.newEvent = {};
			$scope.newEvent.first_name = $scope.options.user.first_name;
			$scope.newEvent.last_name = $scope.options.user.last_name;
			$scope.newEvent.email = $scope.options.user.email;
			$scope.newEvent.organisation = null;
			$scope.newEvent.title = null;
			$scope.newEvent.type = null;
			$scope.newEvent.venue = null;
			$scope.newEvent.description = null;
			$scope.newEvent.start_date = null;
			$scope.newEvent.end_date = null;
			$scope.newEvent.is_approved = false;

			$scope.selects = {};
			$scope.selects.type = null;			
		}

		if ( situation == 'formProcess' ) {
			$scope.options.hasMessage = false;
			$scope.options.message = "<i class='fa fa-clock-o'></i> Event not saved";
			Miscellaneous.addRemoveClass('.alert.animate', 'alert-success alert-danger', 'alert-info');
		}
	};

}]);


eventsAppControllers.controller('EditController', ['$scope', '$window', '$stateParams', 'Events', 'BespokeEvents', 'Miscellaneous', function($scope, $window, $stateParams, Events, BespokeEvents, Miscellaneous) {

	initialSettings('boot');

	Events.get({ id:$stateParams.id }, function(data) {
		$scope.editEvent.first_name = data.event.first_name;
		$scope.editEvent.last_name = data.event.last_name;
		$scope.editEvent.email = data.event.email;
		$scope.editEvent.organisation = data.event.organisation;
		$scope.editEvent.title = data.event.title;
		$scope.editEvent.venue = data.event.venue;
		$scope.editEvent.description = data.event.description;
		$scope.editEvent.start_date = data.event.start_date;
		$scope.editEvent.end_date = data.event.end_date;
		$scope.editEvent.is_approved = data.event.is_approved ? true : false;

		angular.forEach($scope.options.typeOptions, function(option, index) {
			if ( option.type == data.event.type )
				$scope.selects.type = $scope.options.typeOptions[index]; 
		});
	});


	$scope.processForm = function() {
		$scope.editEvent.type = $scope.selects.type.type;

		initialSettings('formProcess');
		$scope.options.disabledSubmit = true;

		Events.update({ id:$stateParams.id }, $scope.editEvent, function(response) {
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
				
				setTimeout(function() {
					initialSettings('formProcess');
					$scope.$apply();
				}, 10000);
			}

			$scope.options.disabledSubmit = false;
		});
	};

	function initialSettings(situation) {
		if ( situation == 'boot' ) {
			$scope.editEvent = {};
			$scope.editEvent.first_name = null;
			$scope.editEvent.last_name = null;
			$scope.editEvent.email = null;
			$scope.editEvent.organisation = null;
			$scope.editEvent.title = null;
			$scope.editEvent.type = null;
			$scope.editEvent.venue = null;
			$scope.editEvent.description = null;
			$scope.editEvent.start_date = null;
			$scope.editEvent.end_date = null;
			$scope.editEvent.is_approved = false;

			$scope.selects = {};
			$scope.selects.type = null;

			$scope.options = {};
			$scope.options.id = $stateParams.id;
			$scope.options.typeOptions = [{type: 'RBM Events'}, {type: 'Advocacy'}, {type: 'Conferences'}, {type: 'Training/Education'}, {type: 'Other Events'}];
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
