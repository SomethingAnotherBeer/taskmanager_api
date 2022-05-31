<?php
return [
	'DB'=> function(Psr\Container\ContainerInterface $c)
	{
		return new App\Libs\DB();
	},

	'Adapter'=> function(Psr\Container\ContainerInterface $c)
	{
		return new App\Libs\Adapter($c->get('DB'));
	},

];