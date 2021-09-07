<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Development\Business\Composer\Updater;

use Symfony\Component\Finder\SplFileInfo;

class TypeUpdater implements UpdaterInterface
{
    /**
     * @var string
     */
    public const KEY_TYPE = 'type';

    /**
     * @param array $composerJson
     * @param \Symfony\Component\Finder\SplFileInfo $composerJsonFile
     *
     * @return array
     */
    public function update(array $composerJson, SplFileInfo $composerJsonFile): array
    {
        $composerJson[static::KEY_TYPE] = 'library';

        if (preg_match('/\/([a-z]+)-behavior/', $composerJson['name'])) {
            $composerJson[static::KEY_TYPE] = 'propel-behavior';
        }

        return $composerJson;
    }
}
