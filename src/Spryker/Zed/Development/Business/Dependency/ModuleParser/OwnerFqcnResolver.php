<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Development\Business\Dependency\ModuleParser;

use Symfony\Component\Finder\SplFileInfo;

class OwnerFqcnResolver implements OwnerFqcnResolverInterface
{
    protected const string PHP_EXTENSION = 'php';

    protected const string NAMESPACE_PATTERN = '/^\s*namespace\s+([^\s;{]+)\s*[;{]/m';

    public function resolve(SplFileInfo $fileInfo): ?string
    {
        if ($fileInfo->getExtension() !== static::PHP_EXTENSION) {
            return null;
        }

        $namespace = $this->extractNamespace($fileInfo);

        if ($namespace === null) {
            return null;
        }

        return sprintf('%s\\%s', $namespace, $fileInfo->getBasename('.' . static::PHP_EXTENSION));
    }

    protected function extractNamespace(SplFileInfo $fileInfo): ?string
    {
        if (!preg_match(static::NAMESPACE_PATTERN, $fileInfo->getContents(), $matches)) {
            return null;
        }

        return $matches[1];
    }
}
