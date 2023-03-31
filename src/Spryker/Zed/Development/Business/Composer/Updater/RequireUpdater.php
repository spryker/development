<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Development\Business\Composer\Updater;

use Symfony\Component\Finder\SplFileInfo;

class RequireUpdater implements UpdaterInterface
{
    /**
     * @var string
     */
    public const KEY_REQUIRE = 'require';

    /**
     * @var string
     */
    public const KEY_REQUIRE_PHP = 'php';

    /**
     * @var string
     */
    public const PHP_MINIMUM = '>=8.0';

    /**
     * @param array $composerJson
     * @param \Symfony\Component\Finder\SplFileInfo $composerJsonFile
     *
     * @return array
     */
    public function update(array $composerJson, SplFileInfo $composerJsonFile): array
    {
        $composerJson = $this->requirePhpVersion($composerJson);

        return $composerJson;
    }

    /**
     * @param array $composerJson
     *
     * @return array
     */
    protected function requirePhpVersion(array $composerJson)
    {
        if (isset($composerJson[static::KEY_REQUIRE][static::KEY_REQUIRE_PHP])) {
            return $composerJson;
        }

        $composerJson[static::KEY_REQUIRE][static::KEY_REQUIRE_PHP] = static::PHP_MINIMUM;

        return $composerJson;
    }
}
