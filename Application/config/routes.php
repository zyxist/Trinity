<?php
$router->connect(
	'/:module/:group/:action',
	array('group' => \Trinity\Web\Router\Standard::COMPULSORY, 'group' => \Trinity\Web\Router\Standard::COMPULSORY, 'action' => \Trinity\Web\Router\Standard::COMPULSORY),
	array()
);
$router->connect(
	'/:group/:action',
	array('module' => 'main', 'group' => \Trinity\Web\Router\Standard::COMPULSORY, 'group' => \Trinity\Web\Router\Standard::COMPULSORY, 'action' => \Trinity\Web\Router\Standard::COMPULSORY),
	array()
);