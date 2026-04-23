<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Development\Business\Dependency\DependencyContainer;

use Generated\Shared\Transfer\DependencyCollectionTransfer;
use Generated\Shared\Transfer\ModuleTransfer;

interface DependencyContainerInterface
{
    /**
     * @param \Generated\Shared\Transfer\ModuleTransfer $moduleTransfer
     *
     * @return $this
     */
    public function initialize(ModuleTransfer $moduleTransfer);

    /**
     * @param string $moduleOrComposerName
     * @param string $type
     * @param bool $isOptional
     * @param bool $isTest
     * @param string|null $usedByFqcn Fully qualified class name of the file that references the dependency. Aggregated per package when provided.
     *
     * @return $this
     */
    public function addDependency(string $moduleOrComposerName, string $type, bool $isOptional = false, bool $isTest = false, ?string $usedByFqcn = null);

    public function getDependencyCollection(): DependencyCollectionTransfer;
}
