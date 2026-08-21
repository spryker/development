<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Development\Business\Dependency\DependencyFinder;

use Spryker\Zed\Development\Business\Dependency\DependencyContainer\DependencyContainerInterface;
use Spryker\Zed\Development\Business\Dependency\DependencyFinder\Context\DependencyFinderContextInterface;

class CodeceptionDependencyFinder extends AbstractTestNamespaceDependencyFinder
{
    /**
     * @var string
     */
    public const TYPE_CODECEPTION = 'codeception';

    protected const string TEST_NAMESPACE_PATTERN = '/(?<!\\w)(Spryker[A-Za-z]*Test)\\\\([A-Za-z0-9_]+)\\\\([A-Za-z0-9_]+)\\\\/';

    public function getType(): string
    {
        return static::TYPE_CODECEPTION;
    }

    public function accept(DependencyFinderContextInterface $context): bool
    {
        if ($context->getDependencyType() !== null && $context->getDependencyType() !== $this->getType()) {
            return false;
        }

        if ($context->getFileInfo()->getFilename() !== 'codeception.yml') {
            return false;
        }

        return true;
    }

    public function findDependencies(DependencyFinderContextInterface $context, DependencyContainerInterface $dependencyContainer): DependencyContainerInterface
    {
        if (!preg_match_all(static::TEST_NAMESPACE_PATTERN, $context->getFileInfo()->getContents(), $matches, PREG_SET_ORDER)) {
            return $dependencyContainer;
        }

        $moduleName = $context->getModule()->getNameOrFail();

        foreach ($matches as $match) {
            $composerName = $this->resolveComposerName([$match[1], $match[2], $match[3]], $moduleName);

            if ($composerName === null) {
                continue;
            }

            $dependencyContainer->addDependency($composerName, $this->getType(), false, true, $context->getOwnerFqcn());
        }

        return $dependencyContainer;
    }
}
