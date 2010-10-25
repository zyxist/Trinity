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
namespace Trinity\Navigation\Loader;
use \SimpleXmlElement;
use \SplQueue;
use \Trinity\Navigation\Page;

/**
 * The concrete implementation of the navigation structure loader.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class XmlLoader extends FileLoader
{
	protected $_translation = array(
		'always' => Page::RENDER_ALWAYS,
		'onActive' => Page::RENDER_ON_ACTIVE,
		'onInactive' => Page::RENDER_ON_INACTIVE,
		'selectionSpecific' => Page::RENDER_SELECTION_SPECIFIC,
		'never' => Page::RENDER_NEVER,
		'ifParent' => Page::RENDER_IF_PARENT,
		'children' => Page::RENDER_CHILDREN,
	);

	/**
	 * Builds a navigation tree from an XML file.
	 *
	 * @return \Trinity\Navigation\Page
	 */
	public function buildNavigationTree()
	{
		if($this->_currentFile === null)
		{
			throw new \DomainException('Cannot load navigation structure: the file with the structure is not defined.');
		}

		$data = \simplexml_load_file($this->findFile($this->_currentFile));

		$queue = new SplQueue;
		$root = $this->_pageFactory($data, $queue);

		while($queue->count() > 0)
		{
			list($parentPage, $pageDesc) = $queue->dequeue();
			$parentPage->appendChild($this->_pageFactory($pageDesc, $queue));
		}

		return $root;
	} // end buildNavigationTree();

	/**
	 * A single page factory from the XML definition.
	 * 
	 * @param SimpleXmlElement $xmlElement The element to scan.
	 * @param SplQueue $queue The queue to push new elements.
	 * @return Page
	 */
	protected function _pageFactory(SimpleXmlElement $xmlElement, SplQueue $queue)
	{
		if(!isset($xmlElement->options))
		{
			throw new \DomainException('Cannot load navigation structure: <options> element missing in the <page> element');
		}
		
		$page = new Page;
		foreach($xmlElement->options->option as $option)
		{
			if(!isset($option['name']))
			{
				throw new \DomainException('Cannot load navigation structure: missing "name" attribute in the <option> element.');
			}
			$name = (string)$option['name'];
			$page->$name = $option->__toString();
		}

		if(isset($xmlElement->rendering))
		{
			foreach($xmlElement->rendering->{'class'} as $renderClass)
			{
				$page->setRenderClass((string)$renderClass['name'], $this->_translation[(string)$renderClass]);
			}
		}

		if(isset($xmlElement->pages))
		{
			foreach($xmlElement->pages->page as $xmlPage)
			{
				$queue->enqueue(array($page, $xmlPage));
			}
		}

		return $page;
	} // end _decodePage();
} // end XmlLoader;