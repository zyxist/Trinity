<?php
// Define the environment variables
define('APP_ENVIRONMENT', 'production');
define('APP_PATH', '../application/');

// Configure the autoloader
$config = parse_ini_file(dirname(__FILE__).'/../paths.ini', true);
require($config['libraries']['Opl'].'Base.php');

$nsLoader = new Opl_Loader;
$nsLoader->addLibrary('Trinity', $config['libraries']['Trinity']);
$nsLoader->addLibrary('Application', $config['libraries']['Application']);
$nsLoader->register();

$oplLoader = new Opl_Loader('_');
$oplLoader->addLibrary('Opl', $config['libraries']['Opl']);
$oplLoader->addLibrary('Opt', $config['libraries']['Opt']);
$oplLoader->addLibrary('Opc', $config['libraries']['Opc']);

require($config['libraries']['Trinity'].'Basement/Core.php');

// Run everything
$application = new \Trinity\Hall\Application(
	APP_ENVIRONMENT,
	APP_PATH.'configs/config.ini',
	APP_PATH.'configs/services.ini'
);
$application->initialize();