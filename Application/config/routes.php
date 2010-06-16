<?php
$router->connect(
	'/:group/:action',
	array('group' => \Trinity\Web\Router_Standard::COMPULSORY, 'action' => \Trinity\Web\Router_Standard::COMPULSORY),
	array()
);