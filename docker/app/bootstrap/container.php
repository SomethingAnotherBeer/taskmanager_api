<?php

$builder = new DI\ContainerBuilder();
$builder->useAutowiring(false);


$builder->addDefinitions(require __DIR__ .'/dependencies.php');

return $builder->build();