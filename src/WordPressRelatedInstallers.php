<?php

/**
 * WP Composer Installers - A collection of Composer Installers for WordPress
 * Copyright (C) 2017 NewClarity Consulting LLC
 * License: GPLv3
 */

namespace WPLib;

use Composer\Config;
use Composer\Package\PackageInterface;
use Composer\Installer\LibraryInstaller;

class WordPressRelatedInstallers extends LibraryInstaller
{
	protected $locations = array(
		'wordpress-core'        => '{{WEBROOT_PATH}}{{CORE_PATH}}',
		'wordpress-plugin'      => '{{WEBROOT_PATH}}{{CONTENT_PATH}}plugins/{$name}/',
		'wordpress-theme'       => '{{WEBROOT_PATH}}{{CONTENT_PATH}}themes/{$name}/',
		'wordpress-muplugin'    => '{{WEBROOT_PATH}}{{CONTENT_PATH}}mu-plugins/{$name}/',
		'wordpress-library'     => '{{WEBROOT_PATH}}{{CONTENT_PATH}}libraries/{$name}/',
		'wordpress-devops-core' => 'devops/core/',
	);

	/**
	 * {@inheritDoc}
	 */
	public function getInstallPath(PackageInterface $package)
	{

		do {

			$composer = $this->composer;

			$packageType = $package->getType();
			$prettyName  = $package->getPrettyName();

			if ( ! isset( $this->locations[ $packageType ] ) ) {
				$message = sprintf( 'Package type [%s] in not supported by wp-composer-installers.', $packageType );
				throw new \InvalidArgumentException( $message );
			}

			$dirPropertyName = $this->dirPropertyName( $package );

			if ( 'wordpress-install-dir' === $dirPropertyName ) {
				/**
				 * Support same as John P Bloch's WordPress Core Installer
				 * @see https://github.com/johnpbloch/wordpress-core-installer
				 */
				$dirPropertyName = 'wordpress-core-install-dir';
			}

			/**
			 * Get `composer.extra` from project's local composer.json
			 */
			if ( $composer->getPackage() ) {
				$composerPackage = $composer->getPackage();
				$extra = $composerPackage->getExtra();
				if ( ! empty( $extra[ $dirPropertyName ] ) ) {
					$installDir = $extra[ $dirPropertyName ];
					break;
				}
			}

			/**
			 * Allow WordPress' core path to changed
			 * Defaults to `wp/`
			 */
			$corePath = ! empty( $extra[ 'wordpress-core-path' ] )
				? rtrim( $extra[ 'wordpress-core-path' ], '/' ) . '/'
				: 'wp/';

			/**
			 * Allow WordPress' content path to changed
			 * Defaults to `content/`
			 */
			$contentPath = ! empty( $extra[ 'wordpress-content-path' ] )
				? rtrim( $extra[ 'wordpress-content-path' ], '/' ) . '/'
				: 'content/';

			/**
			 * Allow WordPress' content path to changed
			 * Defaults to `content/`
			 */
			$webrootPath = ! empty( $extra[ 'wordpress-webroot-path' ] )
				? rtrim( $extra[ 'wordpress-webroot-path' ], '/' ) . '/'
				: 'www/';

			/**
			 * Get the `composer.extra` from package's composer.json
			 */
			$extra = $package->getExtra();

			/**
			 * Capture the install directory from the package itself
			 */
			if ( ! empty( $extra[ $dirPropertyName ] ) ) {
				$installDir = $extra[ $dirPropertyName ];
				break;
			}

			/**
			 * Default the install dir to value specified above in $this->locations
			 */
			$installDir = $this->locations[ $packageType ];

			/**
			 * Allow replacement of CORE, CONTENT and WEBROOT_PATH paths
			 */
			$installDir = str_replace( '{{CORE_PATH}}', $corePath, $installDir );
			$installDir = str_replace( '{{CONTENT_PATH}}', $contentPath, $installDir );
			$installDir = str_replace( '{{WEBROOT_PATH}}', $webrootPath, $installDir );

			$message = null;

			$vendorDir = $composer->getConfig()->get( 'vendor-dir', Config::RELATIVE_PATHS ) ?: 'vendor';

			if ( '.' === $installDir || $vendorDir === $installDir ) {
				$message = sprintf('Cannot install %s in [%s] directory.',$prettyName,$installDir);
			}

			if ( $message ) {
				throw new \InvalidArgumentException( $message );
			}

		} while ( false );

		return $installDir;
	}

	/**
	 * {@inheritDoc}
	 */
	public function supports($packageType)
	{
		$locations = implode('|',array_keys( $this->locations));
		return preg_match("#^($locations)$#",$packageType);
	}

	protected function dirPropertyName( PackageInterface $package )
	{
		return $package->getType() . '-install-dir';
	}

}

