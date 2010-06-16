<?php
/**
 * The testing application for Trinity Framework
 *
 * @author Tomasz Jędrzejewski
 */
namespace Application\Main\Model;
use \Trinity\Basement\Model as Model;
use \Trinity\Model\Interface_Grid as Interface_Grid;

class Grid implements Model, Interface_Grid
{
	public function getColumnHeaders()
	{
		return array(
			array('title' => '#', 'width' => '30'),
			array('title' => 'Title', 'width' => '*'),
			array('title' => 'Foo', 'width' => '20%'),
		);
	} // end getColumnHeaders();

	public function getItems()
	{
		return array(0 =>
			array('id' => '1', 'title' => 'Item 1', 'foo' => 'Omg'),
			array('id' => '2', 'title' => 'Item 2', 'foo' => 'Lol'),
			array('id' => '3', 'title' => 'Item 3', 'foo' => 'Rotfl'),
			array('id' => '4', 'title' => 'Item 4', 'foo' => 'Lmao'),
			array('id' => '5', 'title' => 'Item 5', 'foo' => 'Joe'),
		);
	} // end getItems();

	public function getMessage($name)
	{
		switch($name)
		{
			case 'noData':
				return 'No data.';
			default:
				return 'No message.';
		}
	} // end getMessage();
} // end Grid;