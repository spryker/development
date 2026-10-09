<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Development;

use Nette\DI\Config\Adapters\NeonAdapter;
use Nette\DI\Config\Adapters\PhpAdapter;
use Nette\DI\Config\Loader;
use Spryker\Zed\Development\Dependency\Facade\DevelopmentToModuleFinderFacadeBridge;
use Spryker\Zed\Graph\Communication\Plugin\GraphPlugin;
use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * @method \Spryker\Zed\Development\DevelopmentConfig getConfig()
 */
class DevelopmentDependencyProvider extends AbstractBundleDependencyProvider
{
    public const string FACADE_MODULE_FINDER = 'module finder facade';

    public const string PLUGIN_GRAPH = 'graph plugin';

    public const string FINDER = 'finder';

    public const string FILESYSTEM = 'filesystem';

    public const string CONFIG_LOADER = 'config loader';

    public const string TWIG_ENVIRONMENT = 'twig environment';

    public const string TWIG_LOADER_FILESYSTEM = 'twig loader filesystem';

    public const string PHPSTAN_ADAPTERS = 'PHPSTAN_ADAPTERS';

    public const string SERVICE_UTIL_ENCODING = 'SERVICE_UTIL_ENCODING';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container)
    {
        $container->set(static::PLUGIN_GRAPH, function () {
            return $this->getGraphPlugin();
        });

        $container->set(static::FINDER, function () {
            return $this->createFinder();
        });

        $container->set(static::FILESYSTEM, function () {
            return $this->createFilesystem();
        });

        $container->set(static::CONFIG_LOADER, function () {
            return $this->createConfigLoader();
        });

        $container->set(static::TWIG_ENVIRONMENT, function () {
            return $this->createTwigEnvironment();
        });

        $container->set(static::TWIG_LOADER_FILESYSTEM, function () {
            return $this->createTwigLoaderFilesystem();
        });

        $container = $this->addModuleFinderFacade($container);
        $container = $this->addUtilEncodingService($container);
        $container = $this->addPhpstanAdapters($container);

        return $container;
    }

    /**
     * @return \Spryker\Shared\Graph\GraphInterface
     */
    protected function getGraphPlugin()
    {
        return new GraphPlugin();
    }

    /**
     * @return \Symfony\Component\Finder\Finder
     */
    protected function createFinder()
    {
        return Finder::create();
    }

    /**
     * @return \Symfony\Component\Filesystem\Filesystem
     */
    protected function createFilesystem()
    {
        return new Filesystem();
    }

    /**
     * @return \Nette\DI\Config\Loader
     */
    protected function createConfigLoader()
    {
        return new Loader();
    }

    /**
     * @return \Twig\Environment
     */
    protected function createTwigEnvironment()
    {
        return new Environment($this->createTwigLoaderFilesystem());
    }

    /**
     * @return \Twig\Loader\FilesystemLoader
     */
    protected function createTwigLoaderFilesystem()
    {
        return new FilesystemLoader();
    }

    protected function addUtilEncodingService(Container $container): Container
    {
        $container->set(static::SERVICE_UTIL_ENCODING, function (Container $container) {
            return $container->getLocator()->utilEncoding()->service();
        });

        return $container;
    }

    protected function addModuleFinderFacade(Container $container): Container
    {
        $container->set(static::FACADE_MODULE_FINDER, function (Container $container) {
            $developmentToModuleFinderFacadeBridge = new DevelopmentToModuleFinderFacadeBridge(
                $container->getLocator()->moduleFinder()->facade(),
            );

            return $developmentToModuleFinderFacadeBridge;
        });

        return $container;
    }

    protected function addPhpstanAdapters(Container $container): Container
    {
        $container->set(static::PHPSTAN_ADAPTERS, function (Container $container) {
            return [
                'php' => PhpAdapter::class,
                'neon' => NeonAdapter::class,
            ];
        });

        return $container;
    }
}
