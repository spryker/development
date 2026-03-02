<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Development\Business\Module\PathBuilder;

use Generated\Shared\Transfer\ModuleTransfer;

class SprykerStandaloneModulePathBuilder extends AbstractPathBuilder
{
    /**
     * @var string
     */
    protected const ORGANIZATION = 'Spryker';

    public function buildPaths(ModuleTransfer $moduleTransfer): array
    {
        $paths = [
            sprintf(
                '%s%s/',
                APPLICATION_VENDOR_DIR . '/spryker/',
                $this->getModuleName($moduleTransfer),
            ),
        ];

        return $paths;
    }

    protected function getModuleName(ModuleTransfer $moduleTransfer): string
    {
        return $moduleTransfer->getNameDashed();
    }
}
