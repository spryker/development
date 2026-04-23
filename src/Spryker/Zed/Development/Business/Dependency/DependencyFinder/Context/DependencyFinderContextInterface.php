<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Development\Business\Dependency\DependencyFinder\Context;

use Generated\Shared\Transfer\ModuleTransfer;
use Symfony\Component\Finder\SplFileInfo;

interface DependencyFinderContextInterface
{
    public function getModule(): ModuleTransfer;

    public function getFileInfo(): SplFileInfo;

    public function getDependencyType(): ?string;

    public function getOwnerFqcn(): ?string;

    /**
     * @param string|null $ownerFqcn
     *
     * @return $this
     */
    public function setOwnerFqcn(?string $ownerFqcn);
}
