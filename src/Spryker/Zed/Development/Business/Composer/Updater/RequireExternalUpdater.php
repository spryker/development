<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Development\Business\Composer\Updater;

use Laminas\Filter\Word\CamelCaseToDash;
use Laminas\Filter\Word\DashToCamelCase;
use Spryker\Shared\Config\Config;
use Spryker\Shared\Development\DevelopmentConstants;
use Spryker\Zed\Development\Business\DependencyTree\DependencyTree;
use Symfony\Component\Finder\SplFileInfo;

class RequireExternalUpdater implements UpdaterInterface
{
    /**
     * @var string
     */
    public const KEY_REQUIRE = 'require';

    /**
     * @var string
     */
    public const RELEASE_OPERATOR = '^';

    /**
     * @var string
     */
    public const KEY_NAME = 'name';

    /**
     * @var array
     */
    protected $externalDependencyTree;

    /**
     * @var array<string, string>
     */
    protected $externalToInternalMap;

    /**
     * @var array<string>
     */
    protected $ignorableDependencies;

    /**
     * @param array $externalDependencyTree
     * @param array<string, string> $externalToInternalMap
     * @param array<string> $ignorableDependencies
     */
    public function __construct(array $externalDependencyTree, array $externalToInternalMap, array $ignorableDependencies)
    {
        $this->externalDependencyTree = $externalDependencyTree;
        $this->externalToInternalMap = $externalToInternalMap;
        $this->ignorableDependencies = $ignorableDependencies;
    }

    /**
     * @param array $composerJson
     * @param \Symfony\Component\Finder\SplFileInfo $composerJsonFile
     *
     * @return array
     */
    public function update(array $composerJson, SplFileInfo $composerJsonFile): array
    {
        $moduleName = $this->getModuleName($composerJson);

        $dependentModules = $this->getExternalModules($moduleName);

        if (!Config::hasValue(DevelopmentConstants::COMPOSER_REQUIRE_VERSION_EXTERNAL)) {
            return $composerJson;
        }
        $composerRequireVersion = Config::get(DevelopmentConstants::COMPOSER_REQUIRE_VERSION_EXTERNAL);

        if (preg_match('/^[0-9]/', $composerRequireVersion)) {
            $composerRequireVersion = static::RELEASE_OPERATOR . $composerRequireVersion;
        }

        foreach ($dependentModules as $dependentModule) {
            if (!$dependentModule || $dependentModule === $composerJson[static::KEY_NAME]) {
                continue;
            }
            $filter = new CamelCaseToDash();
            /** @var string $camelCasedDependentModule */
            $camelCasedDependentModule = $filter->filter($dependentModule);
            $dependentModule = strtolower($camelCasedDependentModule);

            $composerJson[static::KEY_REQUIRE][$dependentModule] = static::RELEASE_OPERATOR . $composerRequireVersion;
        }

        return $composerJson;
    }

    /**
     * @param array $composerJsonData
     *
     * @return string
     */
    protected function getModuleName(array $composerJsonData)
    {
        $nameParts = explode('/', $composerJsonData[static::KEY_NAME]);
        $moduleName = array_pop($nameParts);
        $filter = new DashToCamelCase();

        /** @var string $camelCasedModuleName */
        $camelCasedModuleName = $filter->filter($moduleName);

        return (string)$camelCasedModuleName;
    }

    /**
     * @param string $bundleName
     *
     * @return array
     */
    protected function getExternalModules($bundleName)
    {
        $dependentModules = [];
        foreach ($this->externalDependencyTree as $dependency) {
            if (
                $dependency[DependencyTree::META_MODULE] === $bundleName
                && !in_array($dependency[DependencyTree::META_COMPOSER_NAME], $this->ignorableDependencies, true)
            ) {
                $dependentModule = $this->mapExternalToInternal($dependency[DependencyTree::META_COMPOSER_NAME]);

                if ($dependentModule === null) {
                    continue;
                }

                $dependentModules[] = $dependentModule;
            }
        }
        $dependentModules = array_unique($dependentModules);
        sort($dependentModules);

        return $dependentModules;
    }

    /**
     * @param string $composerName
     *
     * @return string|null
     */
    protected function mapExternalToInternal($composerName)
    {
        foreach ($this->externalToInternalMap as $external => $internal) {
            if (substr($external, 0, 1) === '/') {
                if (preg_match($external, $composerName)) {
                    return $internal;
                }
            } elseif ($external === $composerName) {
                return $internal;
            }
        }

        return null;
    }
}
