<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Development\Business\Composer\Updater;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class LicenseUpdater implements UpdaterInterface
{
    /**
     * @var string
     */
    protected const KEY_LICENSE = 'license';

    /**
     * @var string
     */
    protected const LICENSE_TYPE_MIT = 'MIT';

    /**
     * @var string
     */
    protected const LICENSE_TYPE_PROPRIETARY = 'proprietary';

    /**
     * @var string
     */
    protected const MIT_LICENSE = 'The MIT License (MIT)';

    /**
     * @var int
     */
    protected const LICENSE_FILE_DEPTH = 0;

    /**
     * @var string
     */
    protected const LICENSE_FILE_NAME = 'LICENSE';

    /**
     * @param array $composerJson
     * @param \Symfony\Component\Finder\SplFileInfo $composerJsonFile
     *
     * @return array
     */
    public function update(array $composerJson, SplFileInfo $composerJsonFile): array
    {
        $modulePath = dirname($composerJsonFile->getPathname());
        $license = static::LICENSE_TYPE_PROPRIETARY;

        $isMitLicense = (new Finder())->files()
            ->in($modulePath)->depth(static::LICENSE_FILE_DEPTH)
            ->name(static::LICENSE_FILE_NAME)->contains(static::MIT_LICENSE)
            ->hasResults();

        if ($isMitLicense) {
            $license = static::LICENSE_TYPE_MIT;
        }

        $composerJson[static::KEY_LICENSE] = $license;

        return $composerJson;
    }
}
