<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Development\Business\Module\PathBuilder;

use Generated\Shared\Transfer\ModuleTransfer;

interface PathBuilderInterface
{
    public function accept(ModuleTransfer $moduleTransfer): bool;

    public function buildPaths(ModuleTransfer $moduleTransfer): array;
}
