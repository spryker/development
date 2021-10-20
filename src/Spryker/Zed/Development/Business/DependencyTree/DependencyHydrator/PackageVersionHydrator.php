<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Development\Business\DependencyTree\DependencyHydrator;

use Spryker\Zed\Development\Business\DependencyTree\DependencyTree;

class PackageVersionHydrator implements DependencyHydratorInterface
{
    /**
     * @var string
     */
    public const NAME = 'name';

    /**
     * @var string
     */
    public const VERSION = 'version';

    /**
     * @var array
     */
    protected $installedPackages;

    /**
     * @param array $installedPackages
     */
    public function __construct(array $installedPackages)
    {
        $this->installedPackages = $installedPackages;
    }

    /**
     * @param array $dependency
     *
     * @return void
     */
    public function hydrate(array &$dependency)
    {
        $composerVersion = $this->getComposerVersion($dependency);

        if ($composerVersion === null) {
            return;
        }

        $dependency[DependencyTree::META_COMPOSER_VERSION] = $composerVersion;
    }

    /**
     * @param array $dependency
     *
     * @return string|bool|null
     */
    private function getComposerVersion(array $dependency)
    {
        if ($dependency[DependencyTree::META_COMPOSER_NAME] === null || $dependency[DependencyTree::META_COMPOSER_NAME] === false) {
            return false;
        }

        foreach ($this->installedPackages as $installedPackage) {
            if ($installedPackage[static::NAME] === $dependency[DependencyTree::META_COMPOSER_NAME]) {
                return $installedPackage[static::VERSION];
            }
        }

        return null;
    }
}
