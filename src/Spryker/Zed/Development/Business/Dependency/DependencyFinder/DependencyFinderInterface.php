<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Development\Business\Dependency\DependencyFinder;

use Spryker\Zed\Development\Business\Dependency\DependencyContainer\DependencyContainerInterface;
use Spryker\Zed\Development\Business\Dependency\DependencyFinder\Context\DependencyFinderContextInterface;

interface DependencyFinderInterface
{
    public function getType(): string;

    public function accept(DependencyFinderContextInterface $context): bool;

    public function findDependencies(
        DependencyFinderContextInterface $context,
        DependencyContainerInterface $dependencyContainer
    ): DependencyContainerInterface;
}
