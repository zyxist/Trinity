<?php
$start = microtime(true);
// Configure the autoloaders
$libs = parse_ini_file(dirname(__FILE__).'/../paths.ini', true);
require($libs['Opl'].'Opl/Loader.php');

$nsLoader = new Opl_Loader;
// Libraries that use PHP 5.3 namespaces go here.
$nsLoader->addLibrary('Trinity', $libs['Trinity']);
$nsLoader->addLibrary('Doctrine', $libs['Doctrine']);
$nsLoader->addLibrary('Symfony', $libs['Symfony']);
$nsLoader->addLibrary('Application', $libs['Application']);
$nsLoader->addLibrary('Addon', $libs['Addon']);
$nsLoader->addLibrary('Opc', $libs['Opc']);
$nsLoader->register();

$oplLoader = new Opl_Loader('_');
// The libraries below will not be ported to namespaces in 2.x version
// due to backward compatibility issues.
$oplLoader->addLibrary('Opl', $libs['Opl']);
$oplLoader->addLibrary('Opt', $libs['Opt']);
// Note that OPF is being currently ported to namespaces.
$oplLoader->addLibrary('Opf', $libs['Opf']);
$oplLoader->register();

// Create the application object
$application = new \Application\Module('production', false);
$application->setAreaModule(new \Application\Frontend\Module);
$application->launch();
echo 'Time: '.(microtime(true) - $start).'<br/>';
