<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Development\Business\Dependency\Cycle\Algorithm;

interface CycleFinderInterface
{
    /**
     * @param array<string, array<string, bool>> $adjacency
     *
     * @return array<array<string>>
     */
    public function findCycles(array $adjacency): array;
}
