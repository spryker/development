<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Development\Business\Phpstan\Config;

interface PhpstanConfigFileManagerInterface
{
    /**
     * @param array<\SplFileInfo> $configFiles
     * @param string $newConfigFileName
     *
     * @return string
     */
    public function merge(array $configFiles, string $newConfigFileName): string;

    public function isMergedConfigFile(string $configFilePath): bool;

    public function deleteConfigFile(string $configFilePath): void;
}
