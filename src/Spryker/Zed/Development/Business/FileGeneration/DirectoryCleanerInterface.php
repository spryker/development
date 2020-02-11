<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Development\Business\FileGeneration;

interface DirectoryCleanerInterface
{
    /**
     * @return void
     */
    public function clear(): void;
}