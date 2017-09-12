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
		'wordpress-core'        => '{$webroot_path}{$core_path}/',
		'wordpress-plugin'      => '{$webroot_path}{$content_path}plugins/{$name}/',
		'wordpress-theme'       => '{$webroot_path}{$content_path}themes/{$name}/',
		'wordpress-muplugin'    => '{$webroot_path}{$content_path}mu-plugins/{$name}/',
		'wordpress-library'     => '{$webroot_path}{$content_path}libraries/{$name}/',
		'wordpress-devops-core' => 'devops/core/',
	);

	/**
	 * {@inheritDoc}
	 */
	public function getInstallPath(PackageInterface $package)
	{

		do {

			$packageType = $package->getType();

			if (!isset($this->locations[$packageType])) {
				$message = sprintf( 'Package type [%s] in not supported by wp-composer-installers.', $packageType );
				throw new \InvalidArgumentException( $message );
			}

			$prettyName  = $package->getPrettyName();
			$packageName = $package->getName();

			$corePath    = 'wp/';
			$contentPath = 'content/';
			$webrootPath = 'www/';
			$packagePath = basename( $package->getName() );

			$dirPropertyName = $this->dirPropertyName( $package );

			if ( 'wordpress-install-dir' === $dirPropertyName ) {
				/**
				 * Support same as John P Bloch's WordPress Core Installer
				 * @see https://github.com/johnpbloch/wordpress-core-installer
				 */
				$dirPropertyName = 'wordpress-core-install-dir';
			}

			$composer = $this->composer;

			/*
			 * Get `composer.extra` from project's local composer.json
			 */
			if ( $composer->getPackage() ) {
				$composerPackage = $composer->getPackage();
				$extra = $composerPackage->getExtra();

				/**
				 * Allow WordPress' core path to changed
				 * Defaults to `wp/`
				 */
				$corePath = ! empty( $extra[ 'wordpress-core-path' ] )
					? rtrim( $extra[ 'wordpress-core-path' ], '/' ) . '/'
					: $corePath;

				/**
				 * Allow WordPress' content path to changed
				 * Defaults to `content/`
				 */
				$contentPath = !empty($extra['wordpress-content-path'])
					? rtrim($extra['wordpress-content-path'],'/').'/'
					: $contentPath;

				/**
				 * Allow WordPress' content path to changed
				 * Defaults to `content/`
				 */
				$webrootPath = !empty($extra['wordpress-webroot-path'])
					? rtrim($extra['wordpress-webroot-path'],'/').'/'
					: $webrootPath;

				if (!empty($extra['installer-paths']) && is_array($extra['installer-paths'])) {
					/*
					 * See if composer.extra.'installer-paths' in project.json
					 * has hardcoded this type or this specific package.
					 */
					foreach( $extra['installer-paths'] as $maybeDir => $testItems ) {
						foreach( $testItems as $testItem ) {
							if ( "type:{$packageType}" === $testItem || $packageName === $testItem ) {
								$installDir = $maybeDir;
								break;
							}
						}
					}
					if (isset($installDir)) {
						break;
					}
				}

				if (!empty($extra[$dirPropertyName])) {
					$installDir = $extra[$dirPropertyName];
					break;
				}
			}

			/**
			 * Get the `composer.extra` from package's composer.json
			 */
			$extra = $package->getExtra();

			/**
			 * Capture the install directory from the package itself
			 */
			if (!empty($extra[$dirPropertyName])) {
				$installDir = $extra[$dirPropertyName];
				break;
			}

			/**
			 * Default the install dir to value specified above in $this->locations
			 */
			$installDir = $this->locations[ $packageType ];

			$message = null;

			$vendorDir = $composer->getConfig()->get( 'vendor-dir', Config::RELATIVE_PATHS ) ?: 'vendor';

			if ( '.' === $installDir || $vendorDir === $installDir ) {
				$message = sprintf('Cannot install %s in [%s] directory.',$prettyName,$installDir);
			}

			if ( $message ) {
				throw new \InvalidArgumentException( $message );
			}

		} while ( false );

		/**
		 * Allow replacement of package name as well as CORE, CONTENT, WEBROOT paths
		 */
		$installDir = str_replace( '{$name}', $packagePath, $installDir );
		$installDir = str_replace( '{$core_path}', $corePath, $installDir );
		$installDir = str_replace( '{$content_path}', $contentPath, $installDir );
		$installDir = str_replace( '{$webroot_path}', $webrootPath, $installDir );

		return rtrim($installDir,'/').'/';
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

