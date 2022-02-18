<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Development\Business\DependencyTree\DependencyFilter;

use Spryker\Zed\Development\Business\DependencyTree\DependencyTree;

class ForeignEngineBundleFilter implements DependencyFilterInterface
{
    /**
     * @var array
     */
    protected $filterBundles = [];

    /**
     * @param string $pathToBundleConfig
     */
    public function __construct($pathToBundleConfig)
    {
        /** @var string $bundles */
        $bundles = file_get_contents($pathToBundleConfig);
        $bundleList = json_decode($bundles, true);
        $this->filterBundles = array_keys($bundleList);
    }

    /**
     * @param array<string, string> $dependency
     *
     * @return bool
     */
    public function filter(array $dependency)
    {
        return in_array($dependency[DependencyTree::META_FOREIGN_BUNDLE], $this->filterBundles, true);
    }
}
