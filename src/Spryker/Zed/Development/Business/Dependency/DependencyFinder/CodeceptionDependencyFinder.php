<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Development\Business\Dependency\DependencyFinder;

use Spryker\Zed\Development\Business\Dependency\DependencyContainer\DependencyContainerInterface;
use Spryker\Zed\Development\Business\Dependency\DependencyFinder\Context\DependencyFinderContextInterface;
use Spryker\Zed\Development\DevelopmentConfig;

class CodeceptionDependencyFinder extends AbstractFileDependencyFinder
{
    /**
     * @var string
     */
    public const TYPE_CODECEPTION = 'codeception';

    /**
     * @return string
     */
    public function getType(): string
    {
        return static::TYPE_CODECEPTION;
    }

    /**
     * @param \Spryker\Zed\Development\Business\Dependency\DependencyFinder\Context\DependencyFinderContextInterface $context
     *
     * @return bool
     */
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

    /**
     * @param \Spryker\Zed\Development\Business\Dependency\DependencyFinder\Context\DependencyFinderContextInterface $context
     * @param \Spryker\Zed\Development\Business\Dependency\DependencyContainer\DependencyContainerInterface $dependencyContainer
     *
     * @return \Spryker\Zed\Development\Business\Dependency\DependencyContainer\DependencyContainerInterface
     */
    public function findDependencies(DependencyFinderContextInterface $context, DependencyContainerInterface $dependencyContainer): DependencyContainerInterface
    {
        if (preg_match_all('/SprykerTest\\\\(.*?)\\\\(.*?)\\\\/', $context->getFileInfo()->getContents(), $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $applicationName = $match[1];
                $moduleName = $match[2];

                // When a class name does not follow "normal" Spryker naming convention where the Application name is the second and the Module name the third element.
                // In this case, we will most likely have a module name following a more modern structure where the second element is the Module name.
                // We also must take into account different test namespaces such as SprykerTest\\AsyncApi\\ModuleName
                if (!in_array($applicationName, DevelopmentConfig::APPLICATIONS) && !in_array($applicationName, DevelopmentConfig::TEST_APPLICATION_NAMESPACES)) {
                    $moduleName = $applicationName;
                }

                $dependencyContainer->addDependency(sprintf('spryker/%s', $this->getFilter()->filter($moduleName)), $this->getType(), false, true);
            }
        }

        return $dependencyContainer;
    }
}
