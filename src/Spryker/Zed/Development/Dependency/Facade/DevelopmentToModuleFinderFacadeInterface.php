<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Development\Dependency\Facade;

use Generated\Shared\Transfer\ModuleFilterTransfer;
use Spryker\Shared\ModuleFinder\Transfer\ModuleFilter;

interface DevelopmentToModuleFinderFacadeInterface
{
    public function getProjectModules(ModuleFilterTransfer|ModuleFilter|null $moduleFilterTransfer = null): array;

    public function getModules(?ModuleFilterTransfer $moduleFilterTransfer = null): array;

    public function getPackages(): array;
}
