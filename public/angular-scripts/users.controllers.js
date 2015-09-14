'use strict';

var usersAppControllers = angular.module('usersApp.controllers', []);

usersAppControllers.controller('UsersController', ['$rootScope', '$scope', '$window', 'Users', 'BespokeUsers', 'Miscellaneous', function($rootScope, $scope, $window, Users, BespokeUsers, Miscellaneous) {

	initialSettings('boot');

	Users.get(function(data) {
		var usersArr = [];

		angular.forEach(data.users, function(value, index) {
			if ( value.id ) {
				if ( value.id != data.user.id ) {
					value.deleting = false;

					usersArr.push(value);
				}
			}
		});

		$scope.users = usersArr;

		angular.forEach(data.groups, function(group, index) {
			$scope.options.groups.push({ name: group.name });
		});
	});

	$scope.createUser = function() {
		$scope.options.editingUser = false;
		$scope.options.creatingUser = true;
		$scope.selects.groups = $scope.options.groups[2];
		$scope.userCreate = {};
	};

	$scope.cancelCreate = function() {
		initialSettings('useredit');
	};

	$scope.addUser = function() {
		$scope.options.addingUser = true;
		$scope.userCreate.groups = [$scope.selects.groups.name];

		Users.save($scope.userCreate, function(response) {
			if ( response.validation ) {
				Miscellaneous.addRemoveClass('.alert.animate', 'alert-info alert-success', 'alert-danger');

				$scope.formFields.hasMessage = true;
				var messages = '';

				angular.forEach( response.validation, function(message, index) {
					messages += "<i class='fa fa-warning'></i> " + message + "<br />";
					Miscellaneous.highlightInput('#create_' + index, 10000);
				});

				$scope.formFields.message = messages;
				$scope.options.addingUser = false;
			}

			else if ( response.success ) {
				Miscellaneous.addRemoveClass('.alert.animate', 'alert-info alert-danger', 'alert-success');

				response.user.status = 'Active';
				$scope.users.push(response.user);

				$scope.formFields.hasMessage = true;
				$scope.formFields.message = "<i class='fa fa-check'></i> " + response.success;

				$window.setTimeout(function() {
					initialSettings('useredit');
					$scope.$apply();
				}, 2000);
			}

		});
	};

	$scope.editUser = function(inputUser) {
		var user = angular.copy(inputUser, user);
		$scope.selects.groups = null;

		var looping = true;

		angular.forEach($scope.options.groups, function(group, index) {
			if ( looping ) {
				angular.forEach(user.groups, function(selectedGroup, selectedGroupIndex) {
					if ( looping ) {
						if ( selectedGroup.name == group.name ) {
							$scope.selects.groups = $scope.options.groups[index];
							looping = false;
						}
					}
				});
			}
		});

		$scope.userEdit = user;

		$scope.options.creatingUser = false;
		$scope.options.editingUser = true;
	};

	$scope.cancelEdit = function() {
		initialSettings('useredit');
	};

	$scope.updateUser = function(editUser) {
		$scope.options.updatingUser = true;
		$scope.userEdit.groups = [$scope.selects.groups.name];

		Users.update({ id:$scope.userEdit.id }, $scope.userEdit, function(response) {
			if ( response.validation ) {
				Miscellaneous.addRemoveClass('.alert.animate', 'alert-info alert-success', 'alert-danger');

				$scope.formFields.hasMessage = true;
				var messages = '';

				angular.forEach( response.validation, function(message, index) {
					messages += "<i class='fa fa-warning'></i> " + message + "<br />";
					Miscellaneous.highlightInput('#edit_' + index, 10000);
				});

				$scope.formFields.message = messages;
				$scope.options.updatingUser = false;
			}

			else if ( response.success ) {
				Miscellaneous.addRemoveClass('.alert.animate', 'alert-info alert-danger', 'alert-success');

				var looping = true;
				angular.forEach($scope.users, function(user, index) {
					if ( looping ) {
						if ( user.id == editUser.id ) {
							$scope.users[index] = editUser;
							$scope.users[index].updated_at = response.updated_at;
							$scope.users[index].groups = [];
							$scope.users[index].groups.push({ name:$scope.selects.groups.name });

							looping = false;
						}
					}
				});

				$scope.formFields.hasMessage = true;
				$scope.formFields.message = "<i class='fa fa-check'></i> " + response.success;

				$window.setTimeout(function() {
					initialSettings('useredit');
					$scope.$apply();
				}, 5000);
			}

		});
	};

	//$scope.suspend = function(user) { doAction('suspend', user); };
	//$scope.unSuspend = function(user) { doAction('unSuspend', user); };
	$scope.ban = function(user) { doAction('ban', user); };
	$scope.unBan = function(user) { doAction('unBan', user); };
	$scope.destroy = function(user) { doAction('destroy', user); };

	function doAction(action, user) {

		if ( action == 'destroy' )
			user.deleting = true;

		var token = $scope.laravel_token;

		BespokeUsers.doAction(action, user, token).then(function(response) {
			if ( response.success ) {
				Miscellaneous.addRemoveClass('.alert.animate', 'alert-info alert-danger', 'alert-success');

				$scope.formFields.hasMessage = true;
				$scope.formFields.message = "<i class='fa fa-check'></i> " + response.success;

				switch (action) {
					//case 'suspend':
					//	user.status = 'Suspended';
					//	break;
					//case 'unSuspend':
					//	user.status = 'Active';
					//	break;
					case 'ban':
						user.status = 'Inactive';
						break;
					case 'unBan':
						user.status = 'Active';
						break;
					case 'destroy':
						user.deleting = false;
						var looping = true;
						angular.forEach($scope.users, function(aUser, index) {
							if ( looping ) {
								if ( aUser.id == user.id ) {
									$scope.users.splice(index, 1);
									looping = false;
								}
							}
						});

						break;
				}

				$window.setTimeout(function() {
					initialSettings('useredit');
					$scope.$apply();
				}, 5000);
			}
		});
	}

	function initialSettings(situation) {
		if ( situation == 'boot' ) {
			$scope.users = [];
			$scope.currentPage = 1;
			$scope.pageSize = 0;
			$scope.formFields = {};
			$scope.formFields.hasMessage = false;
			$scope.formFields.message = 'Server message';
			$scope.userCreate = {};
			$scope.options = {};
			$scope.options.orderParam = 'email';
			$scope.options.groups = [];
			$scope.options.creatingUser = false;
			$scope.options.addingUser = false;
			$scope.options.editingUser = false;
			$scope.options.updatingUser = false;

			$scope.selects = {};
			$scope.selects.groups = null;
		}

		else if ( situation == 'useredit' ) {
			$scope.formFields.hasMessage = false;
			$scope.formFields.message = 'Server message';
			$scope.selects.groups = [];
			$scope.userCreate = {};
			$scope.userEdit = {};
			$scope.options.creatingUser = false;
			$scope.options.addingUser = false;
			$scope.options.editingUser = false;
			$scope.options.updatingUser = false;
			Miscellaneous.addRemoveClass('.alert.animate', 'alert-success alert-danger', 'alert-info');
		}
	}

}]);
