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
//use Composer\EventDispatcher\EventSubscriberInterface;

class ComposerInstaller implements PluginInterface /*, EventSubscriberInterface */ {

	/**
	 * Activate this installer
	 *
	 * @param Composer    $composer
	 * @param IOInterface $io
	 */
	public function activate( Composer $composer, IOInterface $io ) {
		$installer = new WordPressRelatedInstallers( $io, $composer );
		$installationManager = $composer->getInstallationManager();
		$composer->setInstallationManager( new WordPressInstallationManager( $installationManager ) );
		$composer->getInstallationManager()->addInstaller( $installer );
	}

//	/**
//	 * @return array
//	 */
//	public static function getSubscribedEvents()
//	{
//		return array(
//			'init'                      => 'onEvent',
//			'command'                   => 'onEvent',
//			'pre-install-cmd'           => 'onEvent',
//			'post-install-cmd'          => 'onEvent',
//			'pre-update-cmd'            => 'onEvent',
//			'post-update-cmd'           => 'onEvent',
//			'post-status-cmd'           => 'onEvent',
//			'pre-archive-cmd'           => 'onEvent',
//			'post-archive-cmd'          => 'onEvent',
//			'pre-autoload-dump'         => 'onEvent',
//			'post-autoload-dump'        => 'onEvent',
//			'post-root-package-install' => 'onEvent',
//			'post-create-project-cmd'   => 'onEvent',
//			'pre-dependencies-solving'  => 'onEvent',
//			'post-dependencies-solving' => 'onEvent',
//			'pre-package-install'       => 'onEvent',
//			'post-package-install'      => 'onEvent',
//			'pre-package-update'        => 'onEvent',
//			'post-package-update'       => 'onEvent',
//			'pre-package-uninstall'     => 'onEvent',
//			'post-package-uninstall'    => 'onEvent',
//			// ^ event name ^         ^ method name ^
//		);
//	}
//
//	/**
//	 * @param Composer\Script\Event $event
//	 */
//	public function onEvent( $event )
//	{
//		echo '';
//	}

}

