<?php

return [
	'Request'=> function(Psr\Container\ContainerInterface $c)
	{
		return new App\Http\Request();
	},

];