<?php

namespace Intervapp\Component\Installer\PlusInstallPlugin;

interface ComponentInfoInterface
{
    /**
     * Get the coppnent display name.
     *
     * ```php
     * public function getName(): string {
     *     return '测试应用';
     * }
     * ```
     *
     * @see 测试应用
     *
     * @return string
     *
     * @author Seven Du <shiweidu@outlook.com>
     * @homepage http://medz.cn
     */
    public function getName(): string;

    /**
     * Get the component show logo.
     *
     * ```php
     * public function getLogo(): string
     * {
     *     // return 'https://avatars0.githubusercontent.com/u/5564821?v=3&s=460';
     *     // The func created the component resource to public.
     *     return assset('medz/plus-component-example/logo.png');
     * }
     * ```
     *
     * @see asset('medz/plus-component-example/logo.png')
     * @see https://example/logo.png
     *
     * @return string
     *
     * @author Seven Du <shiweidu@outlook.com>
     * @homepage http://medz.cn
     */
    public function getLogo(): string;

    /**
     * Get the component admin list show icon.
     *
     * reference ::getLogo()
     *
     * @see asset('medz/plus-component-example/icon.png')
     * @see https://example/icon.png
     *
     * @return string
     *
     * @author Seven Du <shiweidu@outlook.com>
     * @homepage http://medz.cn
     */
    public function getIcon(): string;

    /**
     * Get the component admin list link.
     *
     * @see route('/example/admin');
     * @see url('/example/admin');
     * @see https://example/example/admin
     *
     * @return mixed
     *
     * @author Seven Du <shiweidu@outlook.com>
     * @homepage http://medz.cn
     */
    public function getAdminEntry();
}
