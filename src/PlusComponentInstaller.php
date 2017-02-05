<?php

namespace Zhiyi\Component\Installer\PlusInstallPlugin;

use Composer\Package\PackageInterface;
use Composer\Installer\LibraryInstaller;
use Composer\Repository\InstalledRepositoryInterface;
use Composer\Package\PackageInterface;
use InvalidArgumentException;

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
    public function supports(string $packageType): bool
    {
        $isPlusComponent = $packageType === $this->type;

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

        $generator = $this->composer->getAutoloadGenerator();
        $installerClass = $extra['installer-class'];

        parent::uninstall($repo, $package);
    }
}
