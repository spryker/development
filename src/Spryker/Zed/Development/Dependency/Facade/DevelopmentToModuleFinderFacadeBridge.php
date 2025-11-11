<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Development\Dependency\Facade;

use Generated\Shared\Transfer\ModuleFilterTransfer;
use Spryker\Shared\ModuleFinder\Transfer\ModuleFilter;

class DevelopmentToModuleFinderFacadeBridge implements DevelopmentToModuleFinderFacadeInterface
{
    /**
     * @var \Spryker\Zed\ModuleFinder\Business\ModuleFinderFacadeInterface
     */
    protected $moduleFinderFacade;

    /**
     * @param \Spryker\Zed\ModuleFinder\Business\ModuleFinderFacadeInterface $moduleFinderFacade
     */
    public function __construct($moduleFinderFacade)
    {
        $this->moduleFinderFacade = $moduleFinderFacade;
    }

    public function getProjectModules(ModuleFilterTransfer|ModuleFilter|null $moduleFilterTransfer = null): array
    {
        return $this->moduleFinderFacade->getProjectModules($moduleFilterTransfer);
    }

    public function getModules(?ModuleFilterTransfer $moduleFilterTransfer = null): array
    {
        return $this->moduleFinderFacade->getModules($moduleFilterTransfer);
    }

    public function getPackages(): array
    {
        return $this->moduleFinderFacade->getPackages();
    }
}
