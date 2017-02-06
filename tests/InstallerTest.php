<?php

namespace Zhiyi\Component\Installer\PlusInstallPlugin\Test;

// use Composer\Installers\Installer;
// use Composer\Util\Filesystem;
// use Composer\Package\Package;
// use Composer\Package\RootPackage;
// use Composer\Composer;
// use Composer\Config;

use Composer\Util\Filesystem;
use Composer\Composer;
use Composer\Config;

use Zhiyi\Component\Installer\PlusInstallPlugin\PlusComponentInstaller;

class InstallerTest extends TestCase
{
    protected $composer;
    protected $config;
    protected $rootDir;
    protected $vendorDir;
    protected $binDir;
    protected $dm;
    protected $repository;
    protected $io;
    protected $fs;

    protected function setUp()
    {
        $this->fs = new Filesystem;

        $this->composer = new Composer();
        $this->config = new Config();
        $this->composer->setConfig($this->config);

        $this->rootDir = $this->getUniqueTmpDirectory();
        $this->vendorDir = $this->rootDir.DIRECTORY_SEPARATOR.'vendor';
        $this->ensureDirectoryExistsAndClear($this->vendorDir);

        $this->binDir = $this->rootDir.DIRECTORY_SEPARATOR.'bin';
        $this->ensureDirectoryExistsAndClear($this->binDir);

        $this->config->merge(array(
            'config' => array(
                'vendor-dir' => $this->vendorDir,
                'bin-dir' => $this->binDir,
            ),
        ));

        $this->dm = $this->getMockBuilder('Composer\Downloader\DownloadManager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->composer->setDownloadManager($this->dm);

        $this->repository = $this->createMock('Composer\Repository\InstalledRepositoryInterface');
        $this->io = $this->createMock('Composer\IO\IOInterface');
    }

    protected function tearDown()
    {
        $this->fs->removeDirectory($this->rootDir);
    }

    public function testIsInstalled()
    {
        $library = new PlusComponentInstaller($this->io, $this->composer);
        $package = $this->createPackageMock();

        $package
            ->expects($this->any())
            ->method('getPrettyName')
            ->will($this->returnValue('some/package'));

        $package
            ->expects($this->any())
            ->method('getExtra')
            ->will($this->returnValue([
                'installer-class' => "demo"
            ]));

        $this->dm
            ->expects($this->once())
            ->method('download')
            ->with($package, $this->vendorDir.'/some/package');

        $this->repository
            ->expects($this->once())
            ->method('addPackage')
            ->with($package);

        $library->install($this->repository, $package);
        $this->assertFileExists($this->vendorDir, 'Vendor dir should be created');
        $this->assertFileExists($this->binDir, 'Bin dir should be created');

        // $installer = new PlusComponentInstaller($this->io, $this->composer);

        // $package = $this->createPackageMock();
        // $package
        //     ->expects($this->any())
        //     ->method('getPrettyName')
        //     ->will($this->returnValue('some/package'));

        // $package
        //     ->expects($this->any())
        //     ->method('getType')
        //     ->will($this->returnValue('plus-component'));


        // $this->dm
        //     ->expects($this->once())
        //     ->method('download')
        //     ->with($package, $this->vendorDir.'/some/package');

        // $this->repository
        //     ->expects($this->once())
        //     ->method('addPackage')
        //     ->with($package);

        // $installer->install($this->repository, $package);
        // $this->assertTrue($installer->isInstalled($this->repository, $package));
        // $this->assertFalse($installer->isInstalled($this->repository, $package));
    }

    protected function createPackageMock()
    {
        return $this->getMockBuilder('Composer\Package\Package')
            ->setConstructorArgs(array(md5(mt_rand()), '1.0.0.0', '1.0.0'))
            ->getMock();
    }


    // private $composer;
    // private $config;
    // private $vendorDir;
    // private $binDir;
    // private $dm;
    // private $repository;
    // private $io;
    // private $fs;

    // public function setUp()
    // {
    //     $this->fs = new Filesystem;
    //     $this->composer = new Composer();
    //     $this->config = new Config();
    //     $this->composer->setConfig($this->config);
    //     $this->vendorDir = realpath(sys_get_temp_dir()) . DIRECTORY_SEPARATOR . 'plus-test-vendor';
    //     $this->ensureDirectoryExistsAndClear($this->vendorDir);
    //     $this->binDir = realpath(sys_get_temp_dir()) . DIRECTORY_SEPARATOR . 'plus-test-bin';
    //     $this->ensureDirectoryExistsAndClear($this->binDir);
    //     $this->config->merge(array(
    //         'config' => array(
    //             'vendor-dir' => $this->vendorDir,
    //             'bin-dir' => $this->binDir,
    //         ),
    //     ));
    //     $this->dm = $this->getMockBuilder('Composer\Downloader\DownloadManager')
    //         ->disableOriginalConstructor()
    //         ->getMock();
    //     $this->composer->setDownloadManager($this->dm);
    //     $this->repository = $this->createMock('Composer\Repository\InstalledRepositoryInterface');
    //     $this->io = $this->createMock('Composer\IO\IOInterface');
    // }

    // public function tearDown()
    // {
    //     $this->fs->removeDirectory($this->vendorDir);
    //     $this->fs->removeDirectory($this->binDir);
    // }

    // public function testSupports()
    // {
    //     $installer = new PlusComponentInstaller($this->io, $this->composer);
    //     $this->assertTrue($installer->supports('plus-component'));
    // }

    // public function testInstall()
    // {
    //     $installer = new PlusComponentInstaller($this->io, $this->composer);
    //     $package = new Package('zhiyicx/plus-component-test', '1.0.0', '1.0.0');
    //     $package->setType('plus-component');
    //     $package->setExtra([
    //         'installer-class' => FixTures\TestInstaller::class,
    //     ]);
    //     $package->setAutoload([
    //         'psr-4' => [
    //             'Zhiyi\\Test\\Installer\\PlusComponentInstaller\\': ""
    //         ],
    //     ]);

    //     var_dump($package);exit;
    // }
}

