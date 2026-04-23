<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Development\Business\Dependency\Cycle\Graph;

use Generated\Shared\Transfer\CycleDetectionRequestTransfer;

interface CycleGraphBuilderInterface
{
    /**
     * @param \Generated\Shared\Transfer\CycleDetectionRequestTransfer $cycleDetectionRequestTransfer
     *
     * @return array<string, array<string, bool>>
     */
    public function buildDeclaredGraph(CycleDetectionRequestTransfer $cycleDetectionRequestTransfer): array;

    /**
     * @param \Generated\Shared\Transfer\CycleDetectionRequestTransfer $cycleDetectionRequestTransfer
     *
     * @return array<string, array<string, bool>>
     */
    public function buildUsageGraph(CycleDetectionRequestTransfer $cycleDetectionRequestTransfer): array;
}
