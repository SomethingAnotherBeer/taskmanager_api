<?php
return [
	'UserMapper'=> function(Psr\Container\ContainerInterface $c)
	{
		return new App\Mappers\UserMapper(new App\Libs\Adapter($c->get('DB')));
	},

	'UsersMapper'=> function(Psr\Container\ContainerInterface $c)
	{
		return new App\Mappers\UsersMapper(new App\Libs\Adapter($c->get('DB')));
	},

	'UserRightsMapper'=> function(Psr\Container\ContainerInterface $c)
	{
		return new App\Mappers\UserRightsMapper(new App\Libs\Adapter($c->get('DB')));
	},

	'TokenMapper'=> function(Psr\Container\ContainerInterface $c)
	{
		return new App\Mappers\TokenMapper(new App\Libs\Adapter($c->get('DB')));
	},

	'TaskMapper'=> function(Psr\Container\ContainerInterface $c)
	{
		return new App\Mappers\TaskMapper(new App\Libs\Adapter($c->get('DB')));
	},

	'TaskStatusMapper'=> function(Psr\Container\ContainerInterface $c)
	{
		return new App\Mappers\TaskStatusMapper(new App\Libs\Adapter($c->get('DB')));
	},

	'TasksMapper'=> function(Psr\Container\ContainerInterface $c)
	{
		return new App\Mappers\TasksMapper(new App\Libs\Adapter($c->get('DB')));
	},

	'GroupMapper'=> function(Psr\Container\ContainerInterface $c)
	{
		return new App\Mappers\GroupMapper(new App\Libs\Adapter($c->get('DB')));
	},

	'GroupsMapper'=> function(Psr\Container\ContainerInterface $c)
	{
		return new App\Mappers\GroupsMapper(new App\Libs\Adapter($c->get('DB')));
	},

	'UserPerGroupMapper'=> function(Psr\Container\ContainerInterface $c)
	{
		return new App\Mappers\UserPerGroupMapper(new App\Libs\Adapter($c->get('DB')));
	},

	'UsersAssociateMapper'=> function(Psr\Container\ContainerInterface $c)
	{
		return new App\Mappers\UsersAssociateMapper(new App\Libs\Adapter($c->get('DB')));
	}

	
];
