<?php
namespace wcf\system\package\plugin;
use wcf\system\WCF;

/**
 * This PIP installs, updates or deletes object type definitions.
 * 
 * @author	Alexander Ebert
 * @copyright	2001-2011 WoltLab GmbH
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.woltlab.wcf
 * @subpackage	acp.package.plugin
 * @category 	Community Framework
 */
class ObjectTypeDefinitionPackageInstallationPlugin extends AbstractXMLPackageInstallationPlugin {
	/**
	 * @see	wcf\system\package\plugin\AbstractXMLPackageInstallationPlugin::$className
	 */
	public $className = 'wcf\data\object\type\definition\ObjectTypeDefinitionEditor';
	
	/**
	 * @see	wcf\system\package\plugin\AbstractPackageInstallationPlugin::$tableName
	 */
	public $tableName = 'object_type_definition';
	
	/**
	 * @see	wcf\system\package\plugin\AbstractXMLPackageInstallationPlugin::$tagName
	 */	
	public $tagName = 'definition';
	
	/**
	 * @see	wcf\system\package\plugin\AbstractXMLPackageInstallationPlugin::handleDelete()
	 */
	protected function handleDelete(array $items) {
		$sql = "DELETE FROM	wcf".WCF_N."_".$this->tableName."
			WHERE		definitionName = ?
					AND packageID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		foreach ($items as $item) {
			$statement->execute(array(
				$item['attributes']['name'],
				$this->installation->getPackageID()
			));
		}
	}
	
	/**
	 * @see	wcf\system\package\plugin\AbstractXMLPackageInstallationPlugin::prepareImport()
	 */
	protected function prepareImport(array $data) {
		return array(
			'interfaceName' => (isset($data['elements']['interfacename']) ? $data['elements']['interfacename'] : ''),
			'definitionName' => $data['elements']['name']
		);
	}
	
	/**
	 * @see	wcf\system\package\plugin\AbstractXMLPackageInstallationPlugin::findExistingItem()
	 */
	protected function findExistingItem(array $data) {
		$sql = "SELECT	*
			FROM	wcf".WCF_N."_".$this->tableName."
			WHERE	definitionName = ?
				AND packageID = ?";
		$parameters = array(
			$data['definitionName'],
			$this->installation->getPackageID()
		);
		
		return array(
			'sql' => $sql,
			'parameters' => $parameters
		);
	}
}
