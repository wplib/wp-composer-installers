<?php

namespace WPLib;

use WPLib\WordPressRelatedInstallers;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\Installer\InstallationManager;
use Composer\Installer\InstallerInterface;
use Composer\Repository\RepositoryInterface;
use Composer\Repository\InstalledRepositoryInterface;
use Composer\DependencyResolver\Operation\OperationInterface;
use Composer\DependencyResolver\Operation\InstallOperation;
use Composer\DependencyResolver\Operation\UpdateOperation;
use Composer\DependencyResolver\Operation\UninstallOperation;
use Composer\DependencyResolver\Operation\MarkAliasInstalledOperation;
use Composer\DependencyResolver\Operation\MarkAliasUninstalledOperation;

/**
 * Package operation manager for WordPress.
 *
 * ONLY here for addInstaller() method because Composer
 * does not allow setting priority for installers.
 *
 * @author Mike Schinkel <mike@newclarity.net>
 *
 */
class WordPressInstallationManager extends InstallationManager {

	/**
	 * @var InstallationManager
	 */
	private $_installationManager;

	/**
	 * WordPressInstallationManager constructor.
	 *
	 * @param InstallationManager $installationManager
	 */
	public function __construct( InstallationManager $installationManager ) {
		$this->_installationManager = $installationManager;
	}

	/**
	 *
	 */
	public function reset()
	{
		$this->_installationManager->reset();
	}

	/**
	 * Adds installer
	 *
	 * @param InstallerInterface $installer installer instance
	 */
	public function addInstaller(InstallerInterface $installer)
	{
		static $wordPressRelatedInstaller = null;

		if ( $installer instanceof WordPressRelatedInstallers ) {
			$wordPressRelatedInstaller = $installer;
			$this->_installationManager->addInstaller( $installer );
		} else if ( $wordPressRelatedInstaller ) {
			/**
			 * Always make sure that we are the last one added
			 */
			$this->_installationManager->removeInstaller( $wordPressRelatedInstaller );
			$this->_installationManager->addInstaller( $installer );
			$this->_installationManager->addInstaller( $wordPressRelatedInstaller );
		}

	}

	/**
	 * Removes installer
	 *
	 * @param InstallerInterface $installer installer instance
	 */
	public function removeInstaller(InstallerInterface $installer)
	{
		$this->_installationManager->removeInstaller($installer);
	}

	/**
	 * Disables plugins.
	 *
	 * We prevent any plugins from being instantiated by simply
	 * deactivating the installer for them. This ensure that no third-party
	 * code is ever executed.
	 */
	public function disablePlugins()
	{
		$this->_installationManager->disablePlugins();
	}

	/**
	 * Returns installer for a specific package type.
	 *
	 * @param string $type package type
	 *
	 * @throws \InvalidArgumentException if installer for provided type is not registered
	 * @return InstallerInterface
	 */
	public function getInstaller($type)
	{
		return $this->_installationManager->getInstaller($type);
	}

	/**
	 * Checks whether provided package is installed in one of the registered installers.
	 *
	 * @param InstalledRepositoryInterface $repo    repository in which to check
	 * @param PackageInterface             $package package instance
	 *
	 * @return bool
	 */
	public function isPackageInstalled(InstalledRepositoryInterface $repo, PackageInterface $package)
	{
		return $this->_installationManager->isPackageInstalled($repo,$package);
	}

	/**
	 * Install binary for the given package.
	 * If the installer associated to this package doesn't handle that function, it'll do nothing.
	 *
	 * @param PackageInterface $package Package instance
	 */
	public function ensureBinariesPresence(PackageInterface $package)
	{
		$this->_installationManager->ensureBinariesPresence($package);
	}

	/**
	 * Executes solver operation.
	 *
	 * @param RepositoryInterface $repo      repository in which to check
	 * @param OperationInterface  $operation operation instance
	 */
	public function execute(RepositoryInterface $repo, OperationInterface $operation)
	{
		$this->_installationManager->execute($repo,$operation);
	}

	/**
	 * Executes install operation.
	 *
	 * @param RepositoryInterface $repo      repository in which to check
	 * @param InstallOperation    $operation operation instance
	 */
	public function install(RepositoryInterface $repo, InstallOperation $operation)
	{
		$this->_installationManager->install($repo,$operation);
	}

	/**
	 * Executes update operation.
	 *
	 * @param RepositoryInterface $repo      repository in which to check
	 * @param UpdateOperation     $operation operation instance
	 */
	public function update(RepositoryInterface $repo, UpdateOperation $operation)
	{
		$this->_installationManager->update($repo,$operation);
	}

	/**
	 * Uninstalls package.
	 *
	 * @param RepositoryInterface $repo      repository in which to check
	 * @param UninstallOperation  $operation operation instance
	 */
	public function uninstall(RepositoryInterface $repo, UninstallOperation $operation)
	{
		$this->_installationManager->uninstall($repo,$operation);
	}

	/**
	 * Executes markAliasInstalled operation.
	 *
	 * @param RepositoryInterface         $repo      repository in which to check
	 * @param MarkAliasInstalledOperation $operation operation instance
	 */
	public function markAliasInstalled(RepositoryInterface $repo, MarkAliasInstalledOperation $operation)
	{
		$this->_installationManager->markAliasInstalled($repo,$operation);
	}

	/**
	 * Executes markAlias operation.
	 *
	 * @param RepositoryInterface           $repo      repository in which to check
	 * @param MarkAliasUninstalledOperation $operation operation instance
	 */
	public function markAliasUninstalled(RepositoryInterface $repo, MarkAliasUninstalledOperation $operation)
	{
		$this->_installationManager->markAliasUninstalled($repo,$operation);
	}

	/**
	 * Returns the installation path of a package
	 *
	 * @param  PackageInterface $package
	 * @return string           path
	 */
	public function getInstallPath(PackageInterface $package)
	{
		return $this->_installationManager->getInstallPath($package);
	}

	/**
	 * @param IOInterface $io
	 */
	public function notifyInstalls(IOInterface $io)
	{
		$this->_installationManager->notifyInstalls($io);
	}

}