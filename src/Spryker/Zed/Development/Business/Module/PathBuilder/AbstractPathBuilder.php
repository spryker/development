<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Development\Business\Module\PathBuilder;

use Generated\Shared\Transfer\ModuleTransfer;
use Spryker\Zed\Development\DevelopmentConfig;

abstract class AbstractPathBuilder implements PathBuilderInterface
{
    /**
     * @var string
     */
    protected const ORGANIZATION = '';

    /**
     * @var \Spryker\Zed\Development\DevelopmentConfig
     */
    protected $config;

    public function __construct(DevelopmentConfig $config)
    {
        $this->config = $config;
    }

    public function buildPaths(ModuleTransfer $moduleTransfer): array
    {
        return [
            sprintf(
                '%s%s/',
                $this->config->getPathToInternalNamespace(static::ORGANIZATION),
                $this->getModuleName($moduleTransfer),
            ),
        ];
    }

    public function accept(ModuleTransfer $moduleTransfer): bool
    {
        return ($moduleTransfer->getOrganization()->getName() === static::ORGANIZATION);
    }

    protected function getModuleName(ModuleTransfer $moduleTransfer): string
    {
        return $moduleTransfer->getName();
    }
}
