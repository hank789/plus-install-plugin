<?php

namespace Zhiyi\Component\Installer\PlusInstallPlugin;

use Composer\Package\PackageInterface;
use Composer\Installer\LibraryInstaller;
use Composer\Repository\InstalledRepositoryInterface;
use InvalidArgumentException;
use Composer\DependencyResolver\Pool;
use Composer\Package\Package;
use Composer\Package\Link;

class PlusComponentInstaller extends LibraryInstaller
{
    /**
     * supports package type.
     *
     * @param string $packageType
     * @return bool
     * @author Seven Du <shiweidu@outlook.com>
     * @homepage http://medz.cn
     */
    public function supports($packageType): bool
    {
        $isPlusComponent = $packageType === 'plus-component';

        return $isPlusComponent;
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

    public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        $extra = $package->getExtra();
        if (!$extra['installer-class']) {
            throw new InvalidArgumentException(
                sprintf(
                    'The %s component is not set "installer-class" field.'.PHP_EOL
                    .'Using the following config within your package composer.json will allow this:'.PHP_EOL
                    .'{'.PHP_EOL
                    .'    "type": "plus-component",'.PHP_EOL
                    .'    "extra": {'.PHP_EOL
                    .'        "installer-class": "Vendor\\\\Name\\\\Component\\\\Installer"'.PHP_EOL
                    .'    }'.PHP_EOL
                    .'}'.PHP_EOL,
                    $package->getName()
                )
            );
        }

        // run installer.
        parent::install($repo, $package);

        $localRepo = $this->composer->getRepositoryManager()->getLocalRepository();
        $pool = new Pool('dev');
        $pool->addRepository($localRepo);

        $autoloadPackages = array($package->getName() => $package);
        $autoloadPackages = $this->collectDependencies($pool, $autoloadPackages, $package);

        $generator = $this->composer->getAutoloadGenerator();
        $autoloads = array();
        foreach ($autoloadPackages as $autoloadPackage) {
            $downloadPath = $this->getInstallPath($autoloadPackage, (null && $globalRepo->hasPackage($autoloadPackage)));
            $autoloads[] = array($autoloadPackage, $downloadPath);
        }

        $map = $generator->parseAutoloads($autoloads, new Package('dummy', '1.0.0.0', '1.0.0'));
        $classLoader = $generator->createLoader($map);
        $classLoader->register();

        $installerClass = $extra['installer-class'];

        var_dump($classLoader->findFile($installerClass));

        $is = class_exists($installerClass);

        // if ($is === false) {
            // parent::uninstall($repo, $package);
        // }

        var_dump($is, $installerClass);exit;
    }
}
