<?php
$router->connect(
	'/:group/:action',
	array('group' => \Trinity\Web\Router\Standard::COMPULSORY, 'action' => \Trinity\Web\Router\Standard::COMPULSORY),
	array()
);