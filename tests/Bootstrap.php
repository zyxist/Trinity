<?php
/**
 * The bootstrap file for unit tests.
 *
 * @author Tomasz "Zyx" Jędrzejewski
 * @copyright Copyright (c) 2009 Invenzzia Group
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */

if($_SERVER['SCRIPT_FILENAME'] !== '')
{
	echo "Loading bootstrap...\n";

	$config = parse_ini_file(dirname(__FILE__).'/../paths.ini', true);
	require($config['libraries']['Opl'].'Base.php');
	$nsLoader = new Opl_Loader;
	$nsLoader->addLibrary('Trinity', $config['libraries']['Trinity']);
	$nsLoader->register();

	$oplLoader = new Opl_Loader('_');
	$oplLoader->addLibrary('Opl', $config['libraries']['Opl']);
	$oplLoader->addLibrary('Opt', $config['libraries']['Opt']);
	$oplLoader->addLibrary('Opc', $config['libraries']['Opc']);

	require($config['libraries']['Trinity'].'Basement/Core.php');
}