<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Development\Business\DependencyTree;

use Generated\Shared\Transfer\ComposerDependencyCollectionTransfer;
use Generated\Shared\Transfer\DependencyCollectionTransfer;
use Generated\Shared\Transfer\ModuleTransfer;

interface ComposerDependencyParserInterface
{
    public function getComposerDependencyComparison(DependencyCollectionTransfer $dependencyCollectionTransfer): array;

    public function getDeclaredComposerDependencies(ModuleTransfer $moduleTransfer): ComposerDependencyCollectionTransfer;
}
