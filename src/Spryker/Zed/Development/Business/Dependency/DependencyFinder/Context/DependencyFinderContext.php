<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Development\Business\Dependency\DependencyFinder\Context;

use Generated\Shared\Transfer\ModuleTransfer;
use Symfony\Component\Finder\SplFileInfo;

class DependencyFinderContext implements DependencyFinderContextInterface
{
    /**
     * @var \Generated\Shared\Transfer\ModuleTransfer
     */
    protected $moduleTransfer;

    /**
     * @var \Symfony\Component\Finder\SplFileInfo
     */
    protected $fileInfo;

    /**
     * @var string|null
     */
    protected $dependencyType;

    protected ?string $ownerFqcn = null;

    public function __construct(ModuleTransfer $moduleTransfer, SplFileInfo $fileInfo, ?string $dependencyType = null)
    {
        $this->moduleTransfer = $moduleTransfer;
        $this->fileInfo = $fileInfo;
        $this->dependencyType = $dependencyType;
    }

    public function getModule(): ModuleTransfer
    {
        return $this->moduleTransfer;
    }

    public function getFileInfo(): SplFileInfo
    {
        return $this->fileInfo;
    }

    public function getDependencyType(): ?string
    {
        return $this->dependencyType;
    }

    public function getOwnerFqcn(): ?string
    {
        return $this->ownerFqcn;
    }

    /**
     * @param string|null $ownerFqcn
     *
     * @return $this
     */
    public function setOwnerFqcn(?string $ownerFqcn)
    {
        $this->ownerFqcn = $ownerFqcn;

        return $this;
    }
}
