<?php
// Define the environment variables
define('APP_ENVIRONMENT', 'production');
define('APP_PATH', '../Application/');

// Configure the autoloaders
$config = parse_ini_file(dirname(__FILE__).'/../paths.ini', true);
require($config['libraries']['Opl'].'Opl/Base.php');

$nsLoader = new Opl_Loader;
$nsLoader->addLibrary('Trinity', $config['libraries']['Trinity']);
$nsLoader->addLibrary('Doctrine', $config['libraries']['Doctrine']);
$nsLoader->addLibrary('Application', $config['libraries']['Application']);
$nsLoader->register();

$oplLoader = new Opl_Loader('_');
$oplLoader->addLibrary('Opl', $config['libraries']['Opl']);
$oplLoader->addLibrary('Opt', $config['libraries']['Opt']);
$oplLoader->addLibrary('Opc', $config['libraries']['Opc']);
$oplLoader->register();

require($config['libraries']['Trinity'].'Trinity/Basement/Core.php');

// Create application object
$application = new \Trinity\Web\Application(
	'Application',
	APP_ENVIRONMENT,
	APP_PATH.'config/config.ini',
	'../Application/'
);
// Bind autoloaders, so that they could be accessed
$application->addLoader('default', $nsLoader);
$application->addLoader('legacy', $oplLoader);

// Run everything
$application->initialize();