<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Development\Business\Dependency\DependencyFinder;

use Laminas\Filter\FilterChain;
use Laminas\Filter\Word\DashToCamelCase;
use Spryker\Zed\Development\Business\Dependency\DependencyContainer\DependencyContainerInterface;
use Spryker\Zed\Development\Business\Dependency\DependencyFinder\Context\DependencyFinderContextInterface;
use Spryker\Zed\Development\Dependency\Facade\DevelopmentToModuleFinderFacadeInterface;

class BehaviorDependencyFinder implements DependencyFinderInterface
{
    /**
     * @var string
     */
    public const TYPE_PERSISTENCE = 'persistence';

    /**
     * @var \Laminas\Filter\FilterChain|null
     */
    protected $filter;

    /**
     * @var \Spryker\Zed\Development\Dependency\Facade\DevelopmentToModuleFinderFacadeInterface
     */
    protected $moduleFinderFacade;

    public function __construct(DevelopmentToModuleFinderFacadeInterface $moduleFinderFacade)
    {
        $this->moduleFinderFacade = $moduleFinderFacade;
    }

    public function getType(): string
    {
        return static::TYPE_PERSISTENCE;
    }

    public function accept(DependencyFinderContextInterface $context): bool
    {
        if ($context->getDependencyType() !== null && $context->getDependencyType() !== $this->getType()) {
            return false;
        }

        if (substr($context->getFileInfo()->getFilename(), -10) !== 'schema.xml' || strpos($context->getFileInfo()->getFilename(), 'spy_') !== 0) {
            return false;
        }

        return true;
    }

    public function findDependencies(DependencyFinderContextInterface $context, DependencyContainerInterface $dependencyContainer): DependencyContainerInterface
    {
        if (preg_match_all('/<behavior name="(.*?)">/', $context->getFileInfo()->getContents(), $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $moduleName = $this->getModuleNameFromMatch($match);
                if (!$this->isModule($moduleName)) {
                    continue;
                }

                $dependencyContainer = $this->addModuleDependency($dependencyContainer, $moduleName);
            }
        }

        return $dependencyContainer;
    }

    protected function getModuleNameFromMatch(array $match): string
    {
        return ucfirst($this->getFilter()->filter($match[1])) . 'Behavior';
    }

    protected function getFilter(): FilterChain
    {
        if ($this->filter === null) {
            $this->filter = new FilterChain();
            $this->filter->attach(new DashToCamelCase());
        }

        return $this->filter;
    }

    protected function isModule(string $moduleName): bool
    {
        $moduleTransferCollection = $this->moduleFinderFacade->getModules();

        return isset($moduleTransferCollection['Spryker.' . $moduleName]);
    }

    protected function addModuleDependency(DependencyContainerInterface $dependencyContainer, string $moduleName): DependencyContainerInterface
    {
        $dependencyContainer->addDependency(
            $moduleName,
            $this->getType(),
        );

        return $dependencyContainer;
    }
}
