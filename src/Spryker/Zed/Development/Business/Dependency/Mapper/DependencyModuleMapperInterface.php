<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Development\Business\Dependency\Mapper;

use Generated\Shared\Transfer\DependencyModuleTransfer;
use Generated\Shared\Transfer\DependencyModuleViewTransfer;

interface DependencyModuleMapperInterface
{
    public function mapDependencyModuleTransferToDependencyModuleViewTransfer(
        DependencyModuleTransfer $dependencyModuleTransfer,
        DependencyModuleViewTransfer $dependencyModuleViewTransfer
    ): DependencyModuleViewTransfer;
}
