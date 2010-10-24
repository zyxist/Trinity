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
namespace Trinity\Web\Area\MetadataLoader;

/**
 * The concrete implementation of the area metadata loader that loads them
 * from an XML file.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class XmlLoader extends FileLoader
{
	/**
	 * Loads the area metadata from an XML file.
	 */
	protected function _doLoad()
	{
		if($this->_currentFile === null)
		{
			throw new \DomainException('Cannot load area metadata: the file with the area definitions is not defined.');
		}

		$data = \simplexml_load_file($this->findFile($this->_currentFile));

		$output = array();
		foreach($data->area as $area)
		{
			if(!isset($area['name']))
			{
				throw new \DomainException('Cannot load area metadata: missing \'name\' attribute in the <area> element.');
			}
			$areaName = (string)$area['name'];
			if(!\ctype_alnum($areaName))
			{
				throw new \DomainException('Cannot load area metadata: invalid area name: \''.$areaName.'\'.');
			}
			$output[$areaName] = array();
			foreach($area->option as $option)
			{
				if(!isset($option['name']))
				{
					throw new \DomainException('Cannot load area metadata: missing \'name\' attribute in the <option> element.');
				}
				$optionName = (string)$option['name'];
				$value = $option->__toString();
				if(isset($option['type']))
				{
					switch((string)$option['type'])
					{
						case 'boolean':
							$output[$areaName][$optionName] = (boolean)$value;
							break;
						case 'integer':
							$output[$areaName][$optionName] = (int)$value;
							break;
						case 'class':
							if(!\is_callable($value, true))
							{
								throw new \DomainException('Cannot load area metadata: invalid class syntax in option \''.$option['name'].'\' in area \''.$area['name'].'\'');
							}
							$output[$areaName][$optionName] = $value;
							break;
						default:
							$output[$areaName][$optionName] = $value;
					}
				}
				else
				{
					$output[$areaName][$optionName] = $value;
				}
			}
		}

		$this->_metadata = $output;
	} // end _doLoad();
} // end XmlLoader;