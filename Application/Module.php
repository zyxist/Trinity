<?php
/**
 * The testing application for Trinity Framework
 *
 * @author Tomasz Jędrzejewski
 */

namespace Application;
use \Trinity\Utils\Module as TrinityModule;
use \Trinity\Basement\EventSubscriber as EventSubscriber;

class Module extends TrinityModule implements EventSubscriber
{
} // end Module;