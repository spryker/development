<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Development\Business\Dependency\DependencyFinder;

use Spryker\Zed\Development\Business\Dependency\DependencyContainer\DependencyContainerInterface;
use Spryker\Zed\Development\Business\Dependency\DependencyFinder\Context\DependencyFinderContextInterface;

class ModuleAnnotationDependencyFinder implements DependencyFinderInterface
{
    /**
     * @var string
     */
    public const TYPE = 'module-annotation';

    /**
     * @var array<string>
     */
    protected $acceptedFileNames = [
        'Repository.php',
        'RepositoryInterface.php',
        'QueryContainer.php',
        'QueryContainerInterface.php',
        'EntityManager.php',
        'EntityManagerInterface.php',
    ];

    public function getType(): string
    {
        return static::TYPE;
    }

    public function accept(DependencyFinderContextInterface $context): bool
    {
        if ($context->getDependencyType() !== null && $context->getDependencyType() !== $this->getType()) {
            return false;
        }

        if ($context->getFileInfo()->getExtension() !== 'php') {
            return false;
        }

        if (!$this->isAcceptedFile($context)) {
            return false;
        }

        return true;
    }

    protected function isAcceptedFile(DependencyFinderContextInterface $context): bool
    {
        foreach ($this->acceptedFileNames as $fileName) {
            if (substr($context->getFileInfo()->getFilename(), - strlen($fileName)) === $fileName) {
                return true;
            }
        }

        return false;
    }

    public function findDependencies(DependencyFinderContextInterface $context, DependencyContainerInterface $dependencyContainer): DependencyContainerInterface
    {
        $ownerFqcn = $context->getOwnerFqcn();
        if (preg_match_all('/@module\s([a-zA-Z]+)/', $context->getFileInfo()->getContents(), $matches, PREG_SET_ORDER) !== false) {
            foreach ($matches as $match) {
                $dependencyContainer->addDependency($match[1], $this->getType(), false, false, $ownerFqcn);
            }
        }

        return $dependencyContainer;
    }
}
