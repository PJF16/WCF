<?php
namespace wcf\data\language;
use wcf\system\exception\SystemException;
use wcf\system\io\File;
use wcf\system\language\LanguageFactory;
use wcf\util\XML;

/**
 * SetupLanguage is a modification of Language only for the setup process.
 *
 * @author	Marcel Werk
 * @copyright	2001-2011 WoltLab GmbH
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.woltlab.wcf
 * @subpackage	data.language
 * @category 	Community Framework
 */
class SetupLanguage extends Language {
	/**
	 * @see	DatabaseObject::__construct()
	 */
	public function __construct($languageID, array $row, Language $language = null) {
		if ($row === null) {
			throw new SystemException('SetupLanguage accepts only an existing dataset.');
		}
		
		parent::__construct(null, $row, null);
		
		$this->defineConstants();
	}
	
	/**
	 * @see	Language::loadCategory()
	 */
	protected function loadCategory($category) {
		return false;
	}
	
	/**
	 * Loads the compiled language file.
	 * Compiles the language file before if necessary.
	 */
	public function loadLanguage() {
		$filename = TMP_DIR.'setup/lang/cache/'.$this->languageCode.'_wcf.setup.php';
		
		if (!file_exists($filename)) {
			$xml = new XML();
			$xml->load(TMP_DIR.'setup/lang/setup_'.$this->languageCode.'.xml');
			
			// get language items
			$categoriesToCache = array();
			$items = $xml->xpath()->query('/ns:language/ns:category/ns:item');
			foreach ($items as $item) {
				$categoriesToCache[] = array(
					'name' => $item->getAttribute('name'),
					'cdata' => $item->nodeValue
				);
			}
			
			// update language files here
			if (count($categoriesToCache) > 0) {
				$file = new File($filename);
				$file->write("<?php\n/**\n* WoltLab Community Framework\n* language: ".$this->languageCode."\n* encoding: UTF-8\n* category: WCF Setup\n* generated at ".gmdate("r")."\n* \n* DO NOT EDIT THIS FILE\n*/\n");
				foreach ($categoriesToCache as $value => $name) {
					$file->write("\$this->items['".$name['name']."'] = '".str_replace("'", "\'", $name['cdata'])."';\n");
					
					// compile dynamic language variables
					if (strpos($name['cdata'], '{') !== false) {
						$file->write("\$this->dynamicItems['".$name['name']."'] = '".str_replace("'", "\'", LanguageFactory::getScriptingCompiler()->compileString($name['name'], $name['cdata']))."';\n");
					}
				}
		
				$file->write("?>");
				$file->close();
			}
		}

		include_once($filename);
		$this->setLocale();
	}
	
	/**
	 * Defines all global constants.
	 */
	private function defineConstants() {
		if (!defined('LANGUAGE_CODE')) {
			define('LANGUAGE_CODE', LanguageFactory::fixLanguageCode($this->languageCode));
			mb_internal_encoding('UTF-8');
			if (function_exists('mb_regex_encoding')) mb_regex_encoding('UTF-8');
			mb_language('uni');
		}
	}
	
	/**
	 * Sets the local language.
	 */
	private function setLocale() {
		// set locale for
		// string comparison
		// character classification and conversion
		// date and time formatting
		if (!defined('PAGE_DIRECTION')) define('PAGE_DIRECTION', $this->get('wcf.global.pageDirection'));
		setlocale(LC_COLLATE, $this->get('wcf.global.locale.unix').'.UTF-8', $this->get('wcf.global.locale.unix'), $this->get('wcf.global.locale.win'));
		setlocale(LC_CTYPE, $this->get('wcf.global.locale.unix').'.UTF-8', $this->get('wcf.global.locale.unix'), $this->get('wcf.global.locale.win'));
	}
}