<?php
// Define the environment variables
define('APP_ENVIRONMENT', 'production');
define('APP_PATH', '../Application/');

// Configure the autoloaders
$libs = parse_ini_file(dirname(__FILE__).'/../paths.ini', true);
require($libs['Opl'].'Opl/Loader.php');

$nsLoader = new Opl_Loader;
// Libraries that use PHP 5.3 namespaces go here.
$nsLoader->addLibrary('Trinity', $libs['Trinity']);
$nsLoader->addLibrary('Doctrine', $libs['Doctrine']);
$nsLoader->addLibrary('Symfony', $libs['Symfony']);
$nsLoader->addLibrary('Application', $libs['Application']);
$nsLoader->register();

$oplLoader = new Opl_Loader('_');
// The libraries below will not be ported to namespaces in 2.x version
// due to backward compatibility issues.
$oplLoader->addLibrary('Opl', $libs['Opl']);
$oplLoader->addLibrary('Opt', $libs['Opt']);
// Note that OPC is going to be ported soon to namespaces.
$oplLoader->addLibrary('Opc', $libs['Opc']);
// Note that OPF is being currently ported to namespaces.
$oplLoader->addLibrary('Opf', $libs['Opf']);
$oplLoader->register();

require($libs['Trinity'].'Trinity/Basement/Core.php');

// Create application object
$application = new \Trinity\Web\Application(
	'Application',
	APP_ENVIRONMENT,
	APP_PATH.'config/config.ini',
	'../'
);
// Bind autoloaders, so that they could be accessed
$application->addLoader('default', $nsLoader);
$application->addLoader('legacy', $oplLoader);

// Run everything
$application->initialize();