<?php

return [
	
	'UserController'=> function(Psr\Container\ContainerInterface $c)
	{
		return new App\Http\Controllers\UserController($c->get('Request'), $c->get('AuthenticationService'), $c->get('AuthorizationService'), $c->get('UserService'));
	},

	'UsersController'=> function(Psr\Container\ContainerInterface $c)
	{
		return new App\Http\Controllers\UsersController($c->get('Request'), $c->get('AuthenticationService'), $c->get('UsersService'));
	},

	'AuthenticationController'=> function(Psr\Container\ContainerInterface $c)
	{
		return new App\Http\Controllers\AuthenticationController($c->get('Request'),$c->get('AuthenticationService'));
	},

	'FormulatedTaskController'=> function(Psr\Container\ContainerInterface $c)
	{
		return new App\Http\Controllers\FormulatedTaskController($c->get('Request'),$c->get('AuthenticationService'),$c->get('FormulatedTaskService'));
	},

	'EntrustedTaskController'=> function(Psr\Container\ContainerInterface $c)
	{
		return new App\Http\Controllers\EntrustedTaskController($c->get('Request'),$c->get('AuthenticationService'),$c->get('EntrustedTaskService'));
	},

	'TasksController'=> function(Psr\Container\ContainerInterface $c)
	{
		return new App\Http\Controllers\TasksController($c->get('Request'),$c->get('AuthenticationService'),$c->get('TasksService'));
	},

	'GroupController'=> function(Psr\Container\ContainerInterface $c)
	{
		return new App\Http\Controllers\GroupController($c->get('Request'),$c->get('AuthenticationService'),$c->get('GroupService'));
	},
	'GroupsController'=> function(Psr\Container\ContainerInterface $c)
	{
		return new App\Http\Controllers\GroupsController($c->get('Request'),$c->get('AuthenticationService'),$c->get('GroupsService'));
	},

	'UserPerGroupController'=> function(Psr\Container\ContainerInterface $c)
	{
		return new App\Http\Controllers\UserPerGroupController($c->get('Request'),$c->get('AuthenticationService'),$c->get('UserPerGroupService'));
	},

	'UsersAssociateController'=> function(Psr\Container\ContainerInterface $c)
	{
		return new App\Http\Controllers\UsersAssociateController($c->get('Request'),$c->get('AuthenticationService'),$c->get('UsersAssociateService'));
	}

];