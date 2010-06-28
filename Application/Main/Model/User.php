<?php
/**
 * The testing application for Trinity Framework
 *
 * @author Tomasz Jędrzejewski
 */
namespace Application\Main\Model;
use \Trinity\Basement\Model as Model;
use \Trinity\Model\Interfaces\Grid as Interface_Grid;
use Doctrine\ORM\Query;

class User implements Model, Interface_Grid
{
	private $_application;

	public function __construct($application)
	{
		$this->_application = $application;
	} // end __construct();

	public function getColumnHeaders()
	{
		return array(
			array('title' => '#', 'width' => '30'),
			array('title' => 'Name', 'width' => '*'),
			array('title' => 'Age', 'width' => '20%'),
		);
	} // end getColumnHeaders();

	public function getItems()
	{
		$entityManager = $this->_application->getServiceLocator()->get('model.Doctrine_ORM');
		$query = $entityManager->createQuery('SELECT u.id, u.name, u.age FROM MainEntity\User u');
		return $query->getResult(Query::HYDRATE_ARRAY);
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
} // end User;