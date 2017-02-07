<?php

namespace Zhiyi\Component\Installer\PlusInstallPlugin;

interface InstallerInterface
{
    /**
     * 应用安装.
     *
     * @author Seven Du <shiweidu@outlook.com>
     * @homepage http://medz.cn
     */
    public function install();

    /**
     * 应用升级.
     *
     * @author Seven Du <shiweidu@outlook.com>
     * @homepage http://medz.cn
     */
    public function update();

    /**
     * 应用卸载.
     *
     * @author Seven Du <shiweidu@outlook.com>
     * @homepage http://medz.cn
     */
    public function uninstall();

    /**
     * 静态资源.
     *
     * @return array 静态资源文件列表
     *
     * @author Seven Du <shiweidu@outlook.com>
     * @homepage http://medz.cn
     */
    public function resource();

    /**
     * 路由配置.
     *
     * @return string 路由配置文件列表
     *
     * @author Seven Du <shiweidu@outlook.com>
     * @homepage http://medz.cn
     */
    public function router();
}
