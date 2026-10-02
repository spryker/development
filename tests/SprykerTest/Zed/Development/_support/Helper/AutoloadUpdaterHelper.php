<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Development\Helper;

use Codeception\Module;
use SprykerTest\Shared\Testify\Helper\VirtualFilesystemHelperTrait;
use Symfony\Component\Finder\SplFileInfo;

class AutoloadUpdaterHelper extends Module
{
    use VirtualFilesystemHelperTrait;

    protected const string MODULE_DIRECTORY = 'Foo';

    protected const string COMPOSER_JSON_FILE_NAME = 'composer.json';

    protected const string PHP_FILE_CONTENTS = "<?php\n";

    /**
     * Builds the `Foo` module on a virtual filesystem and returns its composer.json file.
     *
     * @param array<string> $moduleFiles Module relative pathnames of the PHP files to create.
     */
    public function haveModuleComposerJsonFile(array $moduleFiles): SplFileInfo
    {
        $moduleStructure = [static::COMPOSER_JSON_FILE_NAME => '{}'];

        foreach ($moduleFiles as $relativePathname) {
            $moduleStructure = array_merge_recursive(
                $moduleStructure,
                $this->buildNestedFileStructure(explode('/', $relativePathname)),
            );
        }

        $moduleDirectory = $this->getVirtualFilesystemHelper()->getVirtualDirectory([static::MODULE_DIRECTORY => $moduleStructure]) . static::MODULE_DIRECTORY;

        return new SplFileInfo($moduleDirectory . DIRECTORY_SEPARATOR . static::COMPOSER_JSON_FILE_NAME, '', static::COMPOSER_JSON_FILE_NAME);
    }

    /**
     * @param array<int, string> $pathFragments
     *
     * @return array<string, mixed>
     */
    protected function buildNestedFileStructure(array $pathFragments): array
    {
        $pathFragment = array_shift($pathFragments);

        if ($pathFragments === []) {
            return [$pathFragment => static::PHP_FILE_CONTENTS];
        }

        return [$pathFragment => $this->buildNestedFileStructure($pathFragments)];
    }
}
