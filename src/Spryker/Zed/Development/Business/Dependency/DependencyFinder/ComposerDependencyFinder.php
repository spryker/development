<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Development\Business\Dependency\DependencyFinder;

use Spryker\Zed\Development\Business\Dependency\DependencyContainer\DependencyContainerInterface;
use Spryker\Zed\Development\Business\Dependency\DependencyFinder\Context\DependencyFinderContextInterface;

class ComposerDependencyFinder extends AbstractFileDependencyFinder
{
    /**
     * @var string
     */
    public const TYPE_COMPOSER = 'composer';

    public function getType(): string
    {
        return static::TYPE_COMPOSER;
    }

    public function accept(DependencyFinderContextInterface $context): bool
    {
        if ($context->getDependencyType() !== null && $context->getDependencyType() !== $this->getType()) {
            return false;
        }

        if ($context->getFileInfo()->getFilename() !== 'composer.json') {
            return false;
        }

        return true;
    }

    public function findDependencies(DependencyFinderContextInterface $context, DependencyContainerInterface $dependencyContainer): DependencyContainerInterface
    {
        $fileContent = $context->getFileInfo()->getContents();
        $ownerFqcn = $context->getOwnerFqcn();

        if (strpos($fileContent, 'cs-check') !== false) {
            $dependencyContainer->addDependency('spryker/code-sniffer', $this->getType(), false, true, $ownerFqcn);
        }

        if (preg_match('/code-sniffer\/(Spryker|SprykerStrict)/', $fileContent)) {
            $dependencyContainer->addDependency('spryker/code-sniffer', $this->getType(), false, true, $ownerFqcn);
        }

        if (strpos($fileContent, 'codecept run') !== false) {
            $dependencyContainer->addDependency('spryker/testify', $this->getType(), false, true, $ownerFqcn);
        }

        if (strpos($fileContent, 'phpstan analyse') !== false) {
            $dependencyContainer->addDependency('phpstan/phpstan', $this->getType(), false, true, $ownerFqcn);
        }

        return $dependencyContainer;
    }
}
