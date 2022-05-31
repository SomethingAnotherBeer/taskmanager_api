<?php
return [
	'AuthenticationService'=> function(Psr\Container\ContainerInterface $c)
	{
		return new App\Services\AppServices\AuthenticationService($c->get('UserMapper'), $c->get('TokenMapper'));
	},

	'AuthorizationService'=> function(Psr\Container\ContainerInterface $c)
	{
		return new App\Services\AppServices\AuthorizationService($c->get('UserMapper'), $c->get('UserRightsMapper'));
	},

	'PaginationService'=> function(Psr\Container\ContainerInterface $c)
	{
		return new App\Services\AppServices\PaginationService();
	}

];