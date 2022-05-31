<?php

return [
	'UserService'=> function(Psr\Container\ContainerInterface $c)
	{
		return new App\Services\DomainServices\UserService($c->get('UserMapper'), $c->get('UserRightsMapper'));
	},
	'UsersService'=> function(Psr\Container\ContainerInterface $c)
	{
		return new App\Services\DomainServices\UsersService($c->get('UsersMapper'), $c->get('PaginationService'));
	},
	
	'FormulatedTaskService'=> function(Psr\Container\ContainerInterface $c)
	{
		return new App\Services\DomainServices\FormulatedTaskService($c->get('TaskMapper'),$c->get('UserMapper'), $c->get('TaskStatusMapper'));
	},

	'EntrustedTaskService'=> function(Psr\Container\ContainerInterface $c)
	{
		return new App\Services\DomainServices\EntrustedTaskService($c->get('TaskMapper'), $c->get('UserMapper'), $c->get('TaskStatusMapper'));
	},

	'TasksService'=> function(Psr\Container\ContainerInterface $c)
	{
		return new App\Services\DomainServices\TasksService($c->get('TasksMapper'),$c->get('TaskStatusMapper'), $c->get('PaginationService'));
	},

	'GroupService'=> function(Psr\Container\ContainerInterface $c)
	{
		return new App\Services\DomainServices\GroupService($c->get('GroupMapper'),$c->get('UserPerGroupMapper'));
	},

	'GroupsService'=> function(Psr\Container\ContainerInterface $c)
	{
		return new App\Services\DomainServices\GroupsService($c->get('GroupsMapper'),$c->get('UserMapper'),$c->get('PaginationService'));
	},

	'UserPerGroupService'=> function(Psr\Container\ContainerInterface $c)
	{
		return new App\Services\DomainServices\UserPerGroupService($c->get('UserPerGroupMapper'), $c->get('UserMapper'), $c->get('GroupMapper'));
	},

	'UsersAssociateService'=> function(Psr\Container\ContainerInterface $c)
	{
		return new App\Services\DomainServices\UsersAssociateService($c->get('UsersAssociateMapper'), $c->get('UserMapper'));
	}

	
];