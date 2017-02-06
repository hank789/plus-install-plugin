<?php

namespace Zhiyi\Component\Installer\PlusInstallPlugin;

use Composer\Composer;
use Composer\Package\PackageInterface;
use Composer\Package\Link;
use Composer\DependencyResolver\Pool;
use Composer\Installer\InstallerInterface;
use Composer\Package\Package;
use Zhiyi\Component\Installer\PlusInstallPlugin\InstallerInterface as PlusInstallerInterface;

class InstallManager
{
    protected $composer;
    protected $package;
    protected $installer;

    public function __construct(Composer $composer, PackageInterface $package, InstallerInterface $installer)
    {
        $this->composer = $composer;
        $this->package = $package;
        $this->installer = $installer;

        $localRepo = $this->composer->getRepositoryManager()->getLocalRepository();
        $pool = new Pool('dev');
        $pool->addRepository($localRepo);

        $autoloadPackages = [$package->getName() => $package];
        $autoloadPackages = $this->collectDependencies($pool, $autoloadPackages, $package);

        $generator = $this->composer->getAutoloadGenerator();
        $autoloads = [];
        foreach ($autoloadPackages as $autoloadPackage) {
            $downloadPath = $this->installer->getInstallPath($autoloadPackage, false);
            $autoloads[] = array($autoloadPackage, $downloadPath);
        }

        $map = $generator->parseAutoloads($autoloads, new Package('dummy', '1.0.0.0', '1.0.0'));
        $classLoader = $generator->createLoader($map);
        $classLoader->register();
    }

    public function install($installerClass)
    {
        $installer = new $installerClass;

        if (!($installer instanceof PlusInstallerInterface)) {
            throw new \Exception(sprintf(
                'The class "%s" not implement "%s"',
                $installerClass,
                PlusInstallerInterface::class
            ), 1);
        }

        $installer->install();
    }

    /**
     * Resolves a package link to a package in the installed pool
     *
     * Since dependencies are already installed this should always find one.
     *
     * @param Pool $pool Pool of installed packages only
     * @param Link $link Package link to look up
     *
     * @return PackageInterface|null The found package
     */
    private function lookupInstalledPackage(Pool $pool, Link $link)
    {
        $packages = $pool->whatProvides($link->getTarget(), $link->getConstraint());

        return (!empty($packages)) ? $packages[0] : null;
    }

    /**
     * Recursively generates a map of package names to packages for all deps
     *
     * @param Pool             $pool      Package pool of installed packages
     * @param array            $collected Current state of the map for recursion
     * @param PackageInterface $package   The package to analyze
     *
     * @return array Map of package names to packages
     */
    private function collectDependencies(Pool $pool, array $collected, PackageInterface $package)
    {
        $requires = array_merge(
            $package->getRequires(),
            $package->getDevRequires()
        );

        foreach ($requires as $requireLink) {
            $requiredPackage = $this->lookupInstalledPackage($pool, $requireLink);
            if ($requiredPackage && !isset($collected[$requiredPackage->getName()])) {
                $collected[$requiredPackage->getName()] = $requiredPackage;
                $collected = $this->collectDependencies($pool, $collected, $requiredPackage);
            }
        }

        return $collected;
    }
}
