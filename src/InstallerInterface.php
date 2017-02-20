<?php

namespace Zhiyi\Component\Installer\PlusInstallPlugin;

use Closure;
use Illuminate\Console\Command;
use Illuminate\Console\OutputStyle;

interface InstallerInterface
{
    /**
     * 构造方法.
     *
     * @param Illuminate\Console\Command     $command
     * @param Illuminate\Console\OutputStyle $output
     *
     * @author Seven Du <shiweidu@outlook.com>
     * @homepage http://medz.cn
     */
    public function __construct(Command $command, OutputStyle $output);

    /**
     * Get the component info.
     *
     * @return ?ComponentInfoInterface
     *
     * @author Seven Du <shiweidu@outlook.com>
     * @homepage http://medz.cn
     */
    public function getComponentInfo();

    /**
     * 应用安装.
     *
     * @param Closure $next
     *
     * @author Seven Du <shiweidu@outlook.com>
     * @homepage http://medz.cn
     */
    public function install(Closure $next);

    /**
     * 应用升级.
     *
     * @param Closure $next
     *
     * @author Seven Du <shiweidu@outlook.com>
     * @homepage http://medz.cn
     */
    public function update(Closure $next);

    /**
     * 应用卸载.
     *
     * @param Closure $next
     *
     * @author Seven Du <shiweidu@outlook.com>
     * @homepage http://medz.cn
     */
    public function uninstall(Closure $next);

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
