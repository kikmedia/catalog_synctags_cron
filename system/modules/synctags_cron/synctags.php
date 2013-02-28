<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
	* Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  Cyperspectrum
 * @author     Christian Schiffler
 * @package    synctags
 * @license    LGPL
 * @filesource
 */


/**
 * Class LetLucinaSleepAgain
 *
 * @copyright  Cybersprectrum
 * @author     Christian Schiffler  
 * @package    SyncTags
 */
class LetLucinaSleepAgain extends Backend
{
	public function syncTags()
	{
		$objTagFields=$this->Database
			->prepare('SELECT f.*, (SELECT tableName FROM tl_catalog_types WHERE id=f.pid) AS tableName FROM tl_catalog_fields AS f ORDER BY f.pid')
			->execute();
		$intPid = 0;
		$arrCatalogs = array();
		$arrCatalog = array();
		while($objTagFields->next())
		{
			if ($arrCatalog && ($objTagFields->pid != $intPid))
			{
				$arrCatalogs[$objTagFields->tableName] = $arrCatalog;
				$arrCatalog = array();
				$intPid = $objTagFields->pid;
			}
			$arrCatalog[] = $objTagFields->row();
		}

		foreach ($arrCatalogs as $strTableName => $arrFields)
		{
			$objAllItems = $this->Database->prepare('SELECT * FROM ' . $strTableName)->execute();
			while($objAllItems->next())
			{
				foreach ($arrFields as $arrField)
				{
					Catalog::setTags($arrField['pid'], $arrField['id'], $objAllItems->id, explode(',', $objAllItems->{$arrField['colName']}));
				}
			}
		}

	}
}
?>