<?php

/**
 * WP DevOps Installer Plugin - A Composer Installer for WP DevOps
 * Copyright (C) 2017 NewClarity Consulting LLC
 * License: GPLv3
 */

namespace WPLib;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

class ComposerPackageInstaller implements PluginInterface {

	/**
	 * Activate this installer
	 *
	 * @param Composer    $composer
	 * @param IOInterface $io
	 */
	public function activate( Composer $composer, IOInterface $io ) {
		$installer = new WordPressRelatedInstallers( $io, $composer );
		$composer->getInstallationManager()->addInstaller( $installer );
	}

}

