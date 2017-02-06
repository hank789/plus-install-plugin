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

        $installerClass = $extra['installer-class'];
        try {
            $installer = new $installerClass;
            $manager = new InstallManager($this->composer, $package, $this, $installer);
            $manager->install();
        } catch (\Exception $e) {
            parent::uninstall($repo, $package);
            throw $e;
        }
    }
}
