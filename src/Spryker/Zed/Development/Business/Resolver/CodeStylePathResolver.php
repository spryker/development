<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Development\Business\Resolver;

use Laminas\Filter\FilterChain;
use Laminas\Filter\StringToLower;
use Laminas\Filter\Word\CamelCaseToDash;
use Spryker\Zed\Development\Business\CodeStyleSniffer\Config\CodeStyleSnifferConfigurationLoaderInterface;
use Spryker\Zed\Development\Business\Exception\CodeStyleSniffer\PathDoesNotExistException;
use Spryker\Zed\Development\DevelopmentConfig;
use Symfony\Component\Finder\Finder;

class CodeStylePathResolver implements PathResolverInterface
{
    /**
     * @var array<string>
     */
    protected const APPLICATION_NAMESPACES = ['Orm'];

    /**
     * @var array<string>
     */
    protected const APPLICATION_LAYERS = ['Zed', 'Client', 'Yves', 'Service', 'Shared'];

    /**
     * @var string
     */
    protected const NAMESPACE_SPRYKER_SHOP = 'SprykerShop';

    /**
     * @var string
     */
    protected const NAMESPACE_SPRYKER = 'Spryker';

    /**
     * @var \Spryker\Zed\Development\DevelopmentConfig
     */
    protected DevelopmentConfig $config;

    /**
     * @var \Spryker\Zed\Development\Business\CodeStyleSniffer\Config\CodeStyleSnifferConfigurationLoaderInterface
     */
    protected $codeStyleSnifferConfigurationLoader;

    /**
     * @param \Spryker\Zed\Development\DevelopmentConfig $config
     * @param \Spryker\Zed\Development\Business\CodeStyleSniffer\Config\CodeStyleSnifferConfigurationLoaderInterface $codeStyleSnifferConfigurationLoader
     */
    public function __construct(
        DevelopmentConfig $config,
        CodeStyleSnifferConfigurationLoaderInterface $codeStyleSnifferConfigurationLoader
    ) {
        $this->config = $config;
        $this->codeStyleSnifferConfigurationLoader = $codeStyleSnifferConfigurationLoader;
    }

    /**
     * @param string|null $module
     * @param string|null $namespace
     * @param string|null $path
     * @param array<string, mixed> $options
     *
     * @return array<string, \Spryker\Zed\Development\Business\CodeStyleSniffer\Config\CodeStyleSnifferConfigurationInterface>
     */
    public function resolvePaths(?string $module, ?string $namespace, ?string $path, array $options): array
    {
        $path = $path !== null ? trim($path, DIRECTORY_SEPARATOR) : null;

        if ($namespace !== null && $this->config->getPathToInternalNamespace($namespace) === null) {
            return $this->resolveCommonModulePath($module, $namespace, $path, $options);
        }

        if ($namespace) {
            return $this->resolveCorePath($module, $namespace, $path, $options);
        }

        if (!$module) {
            return $this->addPath([], $this->config->getPathToRoot() . $path, $options);
        }

        return $this->resolveProjectPath($module, $path, $options);
    }

    /**
     * @param string|null $module
     * @param string|null $namespace
     * @param string|null $path
     * @param array $options
     *
     * @return array<string, \Spryker\Zed\Development\Business\CodeStyleSniffer\Config\CodeStyleSnifferConfigurationInterface>
     */
    protected function resolveCommonModulePath(?string $module, ?string $namespace, ?string $path, array $options): array
    {
        $vendor = $this->normalizeName($namespace);
        $module = $this->normalizeName($module);
        $path = APPLICATION_VENDOR_DIR . DIRECTORY_SEPARATOR. $vendor . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR;

        return $this->addPath([], $path, $options);
    }

    /**
     * @param string $module
     * @param string $namespace
     * @param string|null $path
     * @param array<string, mixed> $options
     *
     * @return array<string, \Spryker\Zed\Development\Business\CodeStyleSniffer\Config\CodeStyleSnifferConfigurationInterface>
     */
    protected function resolveCorePath(string $module, string $namespace, ?string $path, array $options)
    {
        if ($module === 'all') {
            return $this->getPathsToAllCoreModules($namespace, $path, $options);
        }

        return $this->getPathToCoreModule($module, $namespace, $path, $options);
    }

    /**
     * @param string $module
     * @param string|null $pathSuffix
     * @param array<string, mixed> $options
     *
     * @return array<string, \Spryker\Zed\Development\Business\CodeStyleSniffer\Config\CodeStyleSnifferConfigurationInterface>
     */
    protected function resolveProjectPath(string $module, ?string $pathSuffix, array $options): array
    {
        $projectNamespaces = $this->config->getProjectNamespaces();
        $namespaces = array_merge(static::APPLICATION_NAMESPACES, $projectNamespaces);
        $pathToRoot = $this->config->getPathToRoot();

        $paths = [];
        foreach ($namespaces as $namespace) {
            $path = $pathToRoot . 'src' . DIRECTORY_SEPARATOR . $namespace . DIRECTORY_SEPARATOR;

            foreach (static::APPLICATION_LAYERS as $layer) {
                $layerPath = $path . $layer . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR;
                if ($pathSuffix) {
                    $layerPath .= $pathSuffix;
                }

                if (!is_dir($layerPath)) {
                    continue;
                }

                $paths[] = $layerPath;
            }
        }

        return $this->addPath([], implode(' ', $paths), $options);
    }

    /**
     * @param string $namespace
     * @param string|null $pathSuffix
     * @param array<string, mixed> $options
     *
     * @throws \RuntimeException
     *
     * @return array<string, \Spryker\Zed\Development\Business\CodeStyleSniffer\Config\CodeStyleSnifferConfigurationInterface>
     */
    protected function getPathsToAllCoreModules(string $namespace, ?string $pathSuffix, array $options): array
    {
        if ($pathSuffix) {
            throw new RuntimeException('Path suffix option is not possible for "all".');
        }

        $pathToInternalNamespace = $this->config->getPathToInternalNamespace($namespace);

        if (!$pathToInternalNamespace) {
            throw new RuntimeException('Namespace invalid: ' . $namespace);
        }

        $paths = [];
        $modules = $this->getCoreModules($pathToInternalNamespace);
        foreach ($modules as $module) {
            $path = $pathToInternalNamespace . $module . DIRECTORY_SEPARATOR;
            $paths = $this->addPath($paths, $path, $options, $namespace);
        }

        return $paths;
    }

    /**
     * @param string $module
     * @param string $namespace
     * @param string|null $pathSuffix
     * @param array<string, mixed> $options
     *
     * @throws \Spryker\Zed\Development\Business\Exception\CodeStyleSniffer\PathDoesNotExistException
     *
     * @return array<string, \Spryker\Zed\Development\Business\CodeStyleSniffer\Config\CodeStyleSnifferConfigurationInterface>
     */
    protected function getPathToCoreModule(string $module, string $namespace, ?string $pathSuffix, array $options)
    {
        $path = $this->getCorePath($module, $namespace, $pathSuffix);

        if ($this->isPathValid($path)) {
            return $this->addPath([], $path, $options, $namespace);
        }

        $message = sprintf(
            'Could not find a valid path to your module "%s". Expected path "%s". Maybe there is a typo in the module name?',
            $module,
            $path,
        );

        throw new PathDoesNotExistException($message);
    }

    /**
     * @param string $module
     * @param string $namespace
     * @param string|null $pathSuffix
     *
     * @return string
     */
    protected function getCorePath($module, $namespace, $pathSuffix = null)
    {
        $pathToInternalNamespace = $this->config->getPathToInternalNamespace($namespace);
        if ($pathToInternalNamespace && is_dir($pathToInternalNamespace . $module)) {
            return $this->buildPath($pathToInternalNamespace . $module . DIRECTORY_SEPARATOR, $pathSuffix);
        }

        $vendor = $this->normalizeName($namespace);
        $module = $this->normalizeName($module);
        $path = $this->config->getPathToRoot() . 'vendor' . DIRECTORY_SEPARATOR . $vendor . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR;

        return $this->buildPath($path, $pathSuffix);
    }

    /**
     * @param string $path
     * @param string $suffix
     *
     * @return string
     */
    protected function buildPath($path, $suffix)
    {
        if (!$suffix) {
            return $path;
        }

        return $path . $suffix;
    }

    /**
     * @param string $name
     *
     * @return string
     */
    protected function normalizeName($name)
    {
        $filterChain = new FilterChain();
        $filterChain
            ->attach(new CamelCaseToDash())
            ->attach(new StringToLower());

        return $filterChain->filter($name);
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    protected function isPathValid($path)
    {
        return (is_file($path) || is_dir($path));
    }

    /**
     * @param string $path
     *
     * @return array<string>
     */
    protected function getCoreModules(string $path): array
    {
        /** @var array<\Symfony\Component\Finder\SplFileInfo> $directories */
        $directories = (new Finder())
            ->directories()
            ->in($path)
            ->depth('== 0')
            ->sortByName();

        $modules = [];
        foreach ($directories as $dir) {
            $modules[] = $dir->getFilename();
        }

        return $modules;
    }

    /**
     * @param array<string, \Spryker\Zed\Development\Business\CodeStyleSniffer\Config\CodeStyleSnifferConfigurationInterface> $paths
     * @param string $moduleDirectoryPath
     * @param array<string, mixed> $options
     * @param string|null $namespace
     *
     * @return array<string, \Spryker\Zed\Development\Business\CodeStyleSniffer\Config\CodeStyleSnifferConfigurationInterface>
     */
    protected function addPath(array $paths, string $moduleDirectoryPath, array $options, ?string $namespace = null): array
    {
        $paths[$moduleDirectoryPath] = clone $this->codeStyleSnifferConfigurationLoader->load($options, $moduleDirectoryPath, $namespace);

        return $paths;
    }
}
