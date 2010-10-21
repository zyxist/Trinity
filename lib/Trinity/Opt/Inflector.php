<?php
/*
 *  TRINITY FRAMEWORK <http://www.invenzzia.org>
 *
 * This file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE. It is also available through
 * WWW at this URL: <http://www.invenzzia.org/license/new-bsd>
 *
 * Copyright (c) Invenzzia Group <http://www.invenzzia.org>
 * and other contributors. See website for details.
 */

namespace Trinity\Opt;
use \Opt_Inflector_Exception;
use \Opt_Inflector_Interface;
use \Trinity\Web\Application;
use \Trinity\Basement\Module as Basement_Module;
use \Trinity\Web\Area;

/**
 * A new, custom inflector for Open Power Template that solves the problem
 * of naming collisions between modules and areas in compiled files.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Inflector implements Opt_Inflector_Interface
{
	/**
	 * The current Trinity application.
	 * @var \Trinity\Basement\Application
	 */
	protected $_application;

	/**
	 * The current Trinity module.
	 * @var \Trinity\Basement\Module
	 */
	protected $_module;

	/**
	 * The current Trinity area.
	 * @var \Trinity\Web\Area
	 */
	protected $_area;

	/**
	 * The defined paths.
	 * @var array
	 */
	protected $_paths = array();

	/**
	 * The mappings for compilation.
	 * @var array
	 */
	protected $_mappings = array();

	/**
	 * Creates the inflector.
	 * 
	 * @param Application $application The application
	 */
	public function __construct(Application $application)
	{
		$this->_application = $application;

		$this->_paths = array(
			'system.templates' => __DIR__.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR,
			'system.layouts' => __DIR__.DIRECTORY_SEPARATOR.'layouts'.DIRECTORY_SEPARATOR,
			'application.templates' => $application->getDirectory().'templates'.DIRECTORY_SEPARATOR,
			'application.layouts' => $application->getDirectory().'layouts'.DIRECTORY_SEPARATOR,
			'module.templates' => $application->getDirectory().'templates'.DIRECTORY_SEPARATOR,
			'module.layouts' => $application->getDirectory().'layouts'.DIRECTORY_SEPARATOR,
			'area.templates' => $application->getDirectory().'templates'.DIRECTORY_SEPARATOR,
			'area.layouts' => $application->getDirectory().'layouts'.DIRECTORY_SEPARATOR,
			'current.templates' => $application->getDirectory().'templates'.DIRECTORY_SEPARATOR,
			'current.layouts' => $application->getDirectory().'layouts'.DIRECTORY_SEPARATOR,
		);

		$this->_mappings = array(
			'system.templates' => 'system_templates_',
			'system.layouts' => 'system_layouts_',
			'application.templates' => 'application_templates_',
			'application.layouts' => 'application_layouts_',
			'module.templates' => 'application_templates_',
			'module.layouts' => 'application_layouts_',
			'area.templates' => 'application_templates_',
			'area.layouts' => 'application_layouts_',
			'current.templates' => 'application_templates_',
			'current.layouts' => 'application_layouts_',
		);
	} // end __construct();

	/**
	 * Sets the active module.
	 * 
	 * @param Basement_Module $module The module.
	 */
	public function setModule(Basement_Module $module)
	{
		$this->_module = $module;

		$this->_paths['module.templates'] = $module->getDirectory().'templates'.DIRECTORY_SEPARATOR;
		$this->_paths['module.layouts'] = $module->getDirectory().'layouts'.DIRECTORY_SEPARATOR;
		$this->_mappings['module.templates'] = $module->getName().'_mod_templates_';
		$this->_mappings['module.layouts'] = $module->getName().'_mod_layouts_';

		if($this->_area !== null)
		{
			$areaName = ucfirst($this->_area->getAreaName());
			$this->_paths['current.templates'] = $module->getDirectory().$areaName.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR;
			$this->_paths['current.layouts'] = $module->getDirectory().$areaName.DIRECTORY_SEPARATOR.'layouts'.DIRECTORY_SEPARATOR;
			$this->_mappings['current.templates'] = $module->getName().'_'.$areaName.'_cur_templates_';
			$this->_mappings['current.layouts'] = $module->getName().'_'.$areaName.'_cur_layouts_';
		}
	} // end setModule();

	/**
	 * Sets the active area.
	 * 
	 * @param Area $area The area.
	 */
	public function setArea(Area $area)
	{
		$this->_area = $area;

		$this->_paths['area.templates'] = $area->getDirectory().'templates'.DIRECTORY_SEPARATOR;
		$this->_paths['area.layouts'] = $area->getDirectory().'layouts'.DIRECTORY_SEPARATOR;
		$this->_mappings['area.templates'] = $area->getAreaName().'_a_templates_';
		$this->_mappings['area.layouts'] = $area->getAreaName().'_a_layouts_';

		if($this->_module !== null)
		{
			$areaName = ucfirst($area->getAreaName());
			$this->_paths['current.templates'] = $this->_module->getDirectory().$areaName.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR;
			$this->_paths['current.layouts'] = $this->_module->getDirectory().$areaName.DIRECTORY_SEPARATOR.'layouts'.DIRECTORY_SEPARATOR;
			$this->_mappings['current.templates'] = $this->_module->getName().'_'.$areaName.'_cur_templates_';
			$this->_mappings['current.layouts'] = $this->_module->getName().'_'.$areaName.'_cur_layouts_';
		}
	} // end setArea();

	/**
	 * Returns the actual path to the source template suitable to use with
	 * the PHP filesystem functions.
	 *
	 * @param string $file The template file
	 * @return string
	 */
	public function getSourcePath($file)
	{
		if(!preg_match('/^(([a-zA-Z0-9]+)\.(templates|layouts))\:([a-zA-Z0-9\-\_\.\/\\\\]+\.tpl)$/', $file, $matches))
		{
			throw new Opt_Inflector_Exception('Invalid template path specification: \'location.type:filename.tpl\' expected.');
		}
		if(strpos($matches[4], '../') !== false)
		{
			throw new Opt_Inflector_Exception('Cannot use \'../\' in the template path: '.$matches[4]);
		}
		if(!isset($this->_paths[$matches[1]]))
		{
			throw new Opt_Inflector_Exception('Unknown template location: \''.$matches[1].'\'');
		}
		return $this->_paths[$matches[1]].$matches[4];
	} // end getSourcePath();

	/**
	 * Returns the actual path to the compiled template suitable to use
	 * with the PHP filesystem functions.
	 *
	 * @param string $file The template file
	 * @param array $inheritance The dynamic template inheritance list
	 * @return string
	 */
	public function getCompiledPath($file, array $inheritance)
	{
		if(sizeof($inheritance) > 0)
		{
			$list = $inheritance;
			sort($list);
		}
		else
		{
			$list = array();
		}
		$list[] = $file;
		$path = '';
		$first = false;
		foreach($list as $item)
		{
			if($first === false)
			{
				$first = true;
			}
			else
			{
				$path .= DIRECTORY_SEPARATOR;
			}
			$parts = explode(':', $item);
			
			$path .= $this->_mappings[$parts[0]].strtr($parts[1], '/\\', '__');
		}
		return $path.'.php';
	} // end getCompiledPath();
} // end Inflector;