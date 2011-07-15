<?php
namespace wcf\data\event\listener;
use wcf\data\DatabaseObjectList;

/**
 * Represents a list of event listener.
 * 
 * @author 	Alexander Ebert
 * @copyright	2001-2011 WoltLab GmbH
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.woltlab.wcf
 * @subpackage	data.event
 * @category 	Community Framework
 */
class EventListenerList extends DatabaseObjectList {
	/**
	 * @see	DatabaseObjectList::$className
	 */
	public $className = 'wcf\data\event\listener\EventListener';
}