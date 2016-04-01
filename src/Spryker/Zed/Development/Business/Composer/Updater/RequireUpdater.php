<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Development\Business\Composer\Updater;

use Spryker\Zed\Development\Business\DependencyTree\DependencyFilter\TreeFilterInterface;
use Spryker\Zed\Development\Business\DependencyTree\DependencyTree;
use Spryker\Zed\Development\Business\DependencyTree\DependencyTreeReader\DependencyTreeReaderInterface;
use Zend\Filter\Word\CamelCaseToDash;
use Zend\Filter\Word\DashToCamelCase;

class RequireUpdater implements UpdaterInterface
{

    const KEY_REQUIRE = 'require';
    const RELEASE_OPERATOR = '^';
    const EXTERNAL_DEPENDENCIES_BUNDLE_NAME = 'External';

    /**
     * @var \Spryker\Zed\Development\Business\DependencyTree\DependencyTreeReader\DependencyTreeReaderInterface
     */
    private $dependencyTreeReader;

    /**
     * @var \Spryker\Zed\Development\Business\DependencyTree\DependencyFilter\TreeFilterInterface
     */
    private $treeFilter;

    /**
     * @param \Spryker\Zed\Development\Business\DependencyTree\DependencyTreeReader\DependencyTreeReaderInterface $dependencyTreeReader
     * @param \Spryker\Zed\Development\Business\DependencyTree\DependencyFilter\TreeFilterInterface $treeFilter
     */
    public function __construct(DependencyTreeReaderInterface $dependencyTreeReader, TreeFilterInterface $treeFilter)
    {
        $this->dependencyTreeReader = $dependencyTreeReader;
        $this->treeFilter = $treeFilter;
    }

    /**
     * @param array $composerJson
     *
     * @return array
     */
    public function update(array $composerJson)
    {
        $bundleName = $this->getBundleName($composerJson);
        $dependentBundles = $this->getDependentBundles($bundleName);

        foreach ($dependentBundles as $dependentBundle) {
            $filter = new CamelCaseToDash();
            $dependentBundle = strtolower($filter->filter($dependentBundle));
            $composerJson[self::KEY_REQUIRE]['spryker/' . $dependentBundle] = self::RELEASE_OPERATOR . '2.0.0-RC1';
        }

        return $composerJson;
    }

    /**
     * @param array $composerJsonData
     *
     * @return string
     */
    private function getBundleName(array $composerJsonData)
    {
        $nameParts = explode('/', $composerJsonData['name']);
        $bundleName = array_pop($nameParts);
        $filter = new DashToCamelCase();

        return $filter->filter($bundleName);
    }

    /**
     * @param string $bundleName
     *
     * @return array
     */
    private function getDependentBundles($bundleName)
    {
        $dependencyTree = $this->treeFilter->filter($this->dependencyTreeReader->read());
        $dependentBundles = [];
        foreach ($dependencyTree as $dependency) {
            if ($dependency[DependencyTree::META_FOREIGN_BUNDLE] === 'external') {
                continue;
            }
            if ($dependency[DependencyTree::META_BUNDLE] === $bundleName) {
                $dependentBundles[] = $dependency[DependencyTree::META_FOREIGN_BUNDLE];
            }
        }
        $dependentBundles = array_unique($dependentBundles);
        sort($dependentBundles);

        return $dependentBundles;
    }

}
