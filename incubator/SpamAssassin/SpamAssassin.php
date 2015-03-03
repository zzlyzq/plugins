<?php
/**
 * i-MSCP SpamAssassin plugin
 * Copyright (C) 2013-2015 Sascha Bay <info@space2place.de>
 * Copyright (C) 2013-2015 Rene Schuster <mail@reneschuster.de>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

/**
 * Class iMSCP_Plugin_SpamAssassin
 */
class iMSCP_Plugin_SpamAssassin extends iMSCP_Plugin_Action
{
	/**
	 * Plugin installation
	 *
	 * @throws iMSCP_Plugin_Exception
	 * @param iMSCP_Plugin_Manager $pluginManager
	 * @return void
	 */
	public function install(iMSCP_Plugin_Manager $pluginManager)
	{
		try {
			$this->dbMigrate($pluginManager, 'up');
		} catch (iMSCP_Exception_Database $e) {
			throw new iMSCP_Plugin_Exception(
				sprintf('Unable to create database schema: %s', $e->getMessage()), $e->getCode(), $e
			);
		}
	}

	/**
	 * Plugin uninstallation
	 *
	 * @throws iMSCP_Plugin_Exception
	 * @param iMSCP_Plugin_Manager $pluginManager
	 * @return void
	 */
	public function uninstall(iMSCP_Plugin_Manager $pluginManager)
	{
		// Only there to tell the plugin manager that this plugin can be uninstalled
	}

	/**
	 * Plugin enable
	 *
	 * @throws iMSCP_Plugin_Exception
	 * @param iMSCP_Plugin_Manager $pluginManager
	 * @return void
	 */
	public function enable(iMSCP_Plugin_Manager $pluginManager)
	{
		try {
			$this->addSpamAssassinServicePort();
		} catch (iMSCP_Exception_Database $e) {
			throw new iMSCP_Plugin_Exception($e->getMessage(), $e->getCode(), $e);
		}
	}

	/**
	 * Plugin disable
	 *
	 * @throws iMSCP_Plugin_Exception
	 * @param iMSCP_Plugin_Manager $pluginManager
	 * @return void
	 */
	public function disable(iMSCP_Plugin_Manager $pluginManager)
	{
		try {
			$this->removeSpamAssassinServicePort();
		} catch (iMSCP_Exception_Database $e) {
			throw new iMSCP_Plugin_Exception($e->getMessage(), $e->getCode(), $e);
		}
	}
	
	/**
	 * Plugin update
	 *
	 * @throws iMSCP_Plugin_Exception When update fail
	 * @param iMSCP_Plugin_Manager $pluginManager
	 * @param string $fromVersion Version from which plugin update is initiated
	 * @param string $toVersion Version to which plugin is updated
	 * @return void
	 */
	public function update(iMSCP_Plugin_Manager $pluginManager, $fromVersion, $toVersion)
	{
		try {
			$this->dbMigrate($pluginManager, 'up');
		} catch (iMSCP_Exception_Database $e) {
			throw new iMSCP_Plugin_Exception(tr('Unable to update: %s', $e->getMessage()), $e->getCode(), $e);
		}
	}

	/**
	 * Migrate database
	 *
	 * @throws iMSCP_Exception_Database When migration fail
	 * @param iMSCP_Plugin_Manager $pluginManager
	 * @param string $migrationMode Migration mode (up|down)
	 * @return void
	 */
	protected function dbMigrate(iMSCP_Plugin_Manager $pluginManager, $migrationMode = 'up')
	{
		$pluginName = $this->getName();
		$pluginInfo = $pluginManager->getPluginInfo($pluginName);
		$dbSchemaVersion = (isset($pluginInfo['db_schema_version'])) ? $pluginInfo['db_schema_version'] : '000';
		$migrationFiles = array();

		/** @var $migrationFileInfo DirectoryIterator */
		foreach (new DirectoryIterator(dirname(__FILE__) . '/sql-data') as $migrationFileInfo) {
			if (!$migrationFileInfo->isDot()) {
				$migrationFiles[] = $migrationFileInfo->getRealPath();
			}
		}

		natsort($migrationFiles);

		if($migrationMode != 'up') {
			$migrationFiles = array_reverse($migrationFiles);
		}

		try {
			foreach ($migrationFiles as $migrationFile) {
				if (preg_match('%(\d+)\_.*?\.php$%', $migrationFile, $match)) {
					if(
						($migrationMode == 'up' && $match[1] > $dbSchemaVersion) ||
						($migrationMode == 'down' && $match[1] <= $dbSchemaVersion)
					) {
						$migrationFilesContent = include($migrationFile);
						if(isset($migrationFilesContent[$migrationMode])) {
							execute_query($migrationFilesContent[$migrationMode]);
							$dbSchemaVersion = $match[1];
						}
					}
				}
			}
		} catch(iMSCP_Exception_Database $e) {
			$pluginInfo['db_schema_version'] =  $dbSchemaVersion;
			$pluginManager->updatePluginInfo($pluginName, $pluginInfo);
			throw $e;
		}

		$pluginInfo['db_schema_version'] =  ($migrationMode == 'up') ? $dbSchemaVersion : '000';
		$pluginManager->updatePluginInfo($pluginName, $pluginInfo);
	}

	/**
	 * Add SpamAssassin service port
	 *
	 * @return void
	 */
	protected function addSpamAssassinServicePort()
	{
		$dbConfig = iMSCP_Registry::get('dbConfig');
		$pluginConfig = $this->getConfig();

		preg_match("/port=([0-9]+)/" , $pluginConfig['spamassassinOptions'], $spamAssassinPort);
		
		if(!isset($dbConfig['PORT_SPAMASSASSIN'])) {
			$dbConfig['PORT_SPAMASSASSIN'] = $spamAssassinPort[1] . ';tcp;SPAMASSASSIN;1;127.0.0.1';
		} else {
			$this->removeSpamAssassinServicePort();
			$dbConfig['PORT_SPAMASSASSIN'] = $spamAssassinPort[1] . ';tcp;SPAMASSASSIN;1;127.0.0.1';
		}
	}

	/**
	 * Remove SpamAssassin service port
	 *
	 * @return void
	 */
	protected function removeSpamAssassinServicePort()
	{
		$dbConfig = iMSCP_Registry::get('dbConfig');
		unset($dbConfig['PORT_SPAMASSASSIN']);
	}
}
