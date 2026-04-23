<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Development\Business\Dependency\DependencyContainer;

use ArrayObject;
use Generated\Shared\Transfer\DependencyCollectionTransfer;
use Generated\Shared\Transfer\DependencyModuleTransfer;
use Generated\Shared\Transfer\DependencyTransfer;
use Generated\Shared\Transfer\ModuleTransfer;
use Laminas\Filter\Word\DashToCamelCase;

class DependencyContainer implements DependencyContainerInterface
{
    protected const string REDIRECT_KEY_TARGET = 'target';

    protected const string REDIRECT_KEY_WHEN_PRESENT = 'whenPresent';

    /**
     * @var \Generated\Shared\Transfer\DependencyCollectionTransfer
     */
    protected $dependencyCollectionTransfer;

    /**
     * @param array<string> $alwaysOptionalDependencies
     * @param array<string, array<string, mixed>> $dependencyRedirectMap
     */
    public function __construct(
        protected array $alwaysOptionalDependencies = [],
        protected array $dependencyRedirectMap = [],
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\ModuleTransfer $moduleTransfer
     *
     * @return $this
     */
    public function initialize(ModuleTransfer $moduleTransfer)
    {
        $this->dependencyCollectionTransfer = new DependencyCollectionTransfer();
        $this->dependencyCollectionTransfer->setModule($moduleTransfer);

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @param string $moduleOrComposerName
     * @param string $type
     * @param bool $isOptional
     * @param bool $isTest
     * @param string|null $usedByFqcn
     *
     * @return $this
     */
    public function addDependency(string $moduleOrComposerName, string $type, bool $isOptional = false, bool $isTest = false, ?string $usedByFqcn = null)
    {
        $moduleName = $moduleOrComposerName;
        $composerName = null;

        if (strpos($moduleOrComposerName, '/') !== false) {
            $composerName = $moduleOrComposerName;
            $moduleName = $this->getModuleNameFromComposerName($composerName);
        }

        $isOptional = $this->resolveOptional($composerName ?? $moduleName, $isOptional);

        $dependencyTransfer = new DependencyTransfer();
        $dependencyTransfer
            ->setModule($moduleName)
            ->setComposerName($composerName)
            ->setType($type)
            ->setIsOptional($isOptional)
            ->setIsInTest($isTest);

        if ($usedByFqcn !== null) {
            $dependencyTransfer->addUsedByFqcn($usedByFqcn);
        }

        $dependencyModuleTransfer = $this->getDependencyModuleTransfer($dependencyTransfer);
        $dependencyModuleTransfer->addDependency($dependencyTransfer);

        if ($usedByFqcn !== null) {
            $this->appendUsedByFqcn($dependencyModuleTransfer, $usedByFqcn);
        }

        return $this;
    }

    protected function appendUsedByFqcn(DependencyModuleTransfer $dependencyModuleTransfer, string $usedByFqcn): void
    {
        $existingUsedByFqcns = $dependencyModuleTransfer->getUsedByFqcns();

        foreach ($existingUsedByFqcns as $existingUsedByFqcn) {
            if ($existingUsedByFqcn === $usedByFqcn) {
                return;
            }
        }

        $dependencyModuleTransfer->addUsedByFqcn($usedByFqcn);
    }

    protected function getDependencyModuleTransfer(DependencyTransfer $dependencyTransfer): DependencyModuleTransfer
    {
        foreach ($this->dependencyCollectionTransfer->getDependencyModules() as $dependencyModuleTransfer) {
            if ($dependencyTransfer->getComposerName() === null && $dependencyModuleTransfer->getModule() === $dependencyTransfer->getModule()) {
                return $dependencyModuleTransfer;
            }

            if ($dependencyTransfer->getComposerName() !== null && $dependencyModuleTransfer->getComposerName() === $dependencyTransfer->getComposerName()) {
                return $dependencyModuleTransfer;
            }
        }

        $dependencyModuleTransfer = new DependencyModuleTransfer();
        $dependencyModuleTransfer->setModule($dependencyTransfer->getModule());
        $dependencyModuleTransfer->setComposerName($dependencyTransfer->getComposerName());

        $this->dependencyCollectionTransfer->addDependencyModule($dependencyModuleTransfer);

        return $dependencyModuleTransfer;
    }

    public function getDependencyCollection(): DependencyCollectionTransfer
    {
        $this->applyDependencyRedirects();

        return $this->sortDependencies($this->dependencyCollectionTransfer);
    }

    protected function applyDependencyRedirects(): void
    {
        // Snapshot prevents chained redirects: a redirect target cannot become a new redirect source
        $detectedComposerNames = $this->getDetectedComposerNames();

        foreach ($this->dependencyRedirectMap as $source => $config) {
            if (!in_array($source, $detectedComposerNames, true)) {
                continue;
            }

            if (!$this->isRedirectConditionMet($config, $detectedComposerNames)) {
                continue;
            }

            $this->redirectDependency($source, $config[static::REDIRECT_KEY_TARGET]);
        }
    }

    /**
     * @return array<string>
     */
    protected function getDetectedComposerNames(): array
    {
        $composerNames = [];

        foreach ($this->dependencyCollectionTransfer->getDependencyModules() as $dependencyModuleTransfer) {
            if ($dependencyModuleTransfer->getComposerName() !== null) {
                $composerNames[] = $dependencyModuleTransfer->getComposerName();
            }
        }

        return $composerNames;
    }

    /**
     * @param array<string, mixed> $config
     * @param array<string> $detectedComposerNames
     */
    protected function isRedirectConditionMet(array $config, array $detectedComposerNames): bool
    {
        if (!isset($config[static::REDIRECT_KEY_WHEN_PRESENT]) || $config[static::REDIRECT_KEY_WHEN_PRESENT] === []) {
            return true;
        }

        foreach ($config[static::REDIRECT_KEY_WHEN_PRESENT] as $requiredDependency) {
            if (in_array($requiredDependency, $detectedComposerNames, true)) {
                return true;
            }
        }

        return false;
    }

    protected function redirectDependency(string $source, string $target): void
    {
        $sourceDependencyModuleTransfer = null;
        $sourceIndex = null;

        foreach ($this->dependencyCollectionTransfer->getDependencyModules() as $index => $dependencyModuleTransfer) {
            if ($dependencyModuleTransfer->getComposerName() === $source) {
                $sourceDependencyModuleTransfer = $dependencyModuleTransfer;
                $sourceIndex = $index;

                break;
            }
        }

        if ($sourceDependencyModuleTransfer === null) {
            return;
        }

        $targetModuleName = $this->getModuleNameFromComposerName($target);
        $isTargetAlwaysOptional = in_array($target, $this->alwaysOptionalDependencies, true);

        // Find or create the target DependencyModuleTransfer
        $targetDependencyModuleTransfer = $this->findOrCreateTargetDependencyModule($target, $targetModuleName);

        // Move all dependencies from source to target
        foreach ($sourceDependencyModuleTransfer->getDependencies() as $dependencyTransfer) {
            $redirectedDependency = new DependencyTransfer();
            $redirectedDependency
                ->setModule($targetModuleName)
                ->setComposerName($target)
                ->setType($dependencyTransfer->getType())
                ->setIsOptional($isTargetAlwaysOptional || $dependencyTransfer->getIsOptional())
                ->setIsInTest($dependencyTransfer->getIsInTest());

            foreach ($dependencyTransfer->getUsedByFqcns() as $usedByFqcn) {
                $redirectedDependency->addUsedByFqcn($usedByFqcn);
            }

            $targetDependencyModuleTransfer->addDependency($redirectedDependency);
        }

        foreach ($sourceDependencyModuleTransfer->getUsedByFqcns() as $usedByFqcn) {
            $this->appendUsedByFqcn($targetDependencyModuleTransfer, $usedByFqcn);
        }

        // Remove the source from the collection
        $dependencyModules = $this->dependencyCollectionTransfer->getDependencyModules()->getArrayCopy();
        unset($dependencyModules[$sourceIndex]);

        $this->dependencyCollectionTransfer->setDependencyModules(new ArrayObject(array_values($dependencyModules)));
    }

    protected function findOrCreateTargetDependencyModule(string $composerName, string $moduleName): DependencyModuleTransfer
    {
        foreach ($this->dependencyCollectionTransfer->getDependencyModules() as $dependencyModuleTransfer) {
            if ($dependencyModuleTransfer->getComposerName() === $composerName) {
                return $dependencyModuleTransfer;
            }
        }

        $dependencyModuleTransfer = new DependencyModuleTransfer();
        $dependencyModuleTransfer->setModule($moduleName);
        $dependencyModuleTransfer->setComposerName($composerName);

        $this->dependencyCollectionTransfer->addDependencyModule($dependencyModuleTransfer);

        return $dependencyModuleTransfer;
    }

    protected function resolveOptional(string $moduleOrComposerName, bool $isOptional): bool
    {
        if (in_array($moduleOrComposerName, $this->alwaysOptionalDependencies, true)) {
            return true;
        }

        return $isOptional;
    }

    protected function sortDependencies(DependencyCollectionTransfer $dependencyCollectionTransfer): DependencyCollectionTransfer
    {
        $callback = function (DependencyModuleTransfer $dependencyBundleTransferA, DependencyModuleTransfer $dependencyBundleTransferB) {
            return strcmp($dependencyBundleTransferA->getModule(), $dependencyBundleTransferB->getModule());
        };

        $dependencyModules = $dependencyCollectionTransfer->getDependencyModules()->getArrayCopy();

        usort($dependencyModules, $callback);

        $dependencyCollectionTransfer->setDependencyModules(new ArrayObject());

        foreach ($dependencyModules as $dependencyModule) {
            $dependencyCollectionTransfer->addDependencyModule($dependencyModule);
        }

        return $dependencyCollectionTransfer;
    }

    protected function getModuleNameFromComposerName(string $composerName): string
    {
        [$organizationName, $moduleName] = explode('/', $composerName);

        $filter = new DashToCamelCase();
        /** @var string $camelCasedModuleName */
        $camelCasedModuleName = $filter->filter($moduleName);

        return ucfirst($camelCasedModuleName);
    }
}
