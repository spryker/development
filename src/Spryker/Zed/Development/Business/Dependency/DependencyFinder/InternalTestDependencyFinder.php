<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Development\Business\Dependency\DependencyFinder;

use Spryker\Zed\Development\Business\Dependency\DependencyContainer\DependencyContainerInterface;
use Spryker\Zed\Development\Business\Dependency\DependencyFinder\Context\DependencyFinderContextInterface;
use Spryker\Zed\Development\Business\Dependency\ModuleParser\TestNamespaceParserInterface;
use Spryker\Zed\Development\Business\Dependency\ModuleParser\UseStatementParserInterface;

class InternalTestDependencyFinder extends AbstractTestNamespaceDependencyFinder
{
    public const string TYPE_INTERNAL_TEST = 'internal-test';

    public function __construct(
        protected UseStatementParserInterface $useStatementParser,
        TestNamespaceParserInterface $testNamespaceParser
    ) {
        parent::__construct($testNamespaceParser);
    }

    public function getType(): string
    {
        return static::TYPE_INTERNAL_TEST;
    }

    public function accept(DependencyFinderContextInterface $context): bool
    {
        if ($context->getDependencyType() !== null && $context->getDependencyType() !== $this->getType()) {
            return false;
        }

        return $context->getFileInfo()->getExtension() === 'php';
    }

    public function findDependencies(
        DependencyFinderContextInterface $context,
        DependencyContainerInterface $dependencyContainer
    ): DependencyContainerInterface {
        $moduleName = $context->getModule()->getNameOrFail();
        $useStatements = $this->useStatementParser->getUseStatements($context->getFileInfo());

        foreach ($useStatements as $useStatement) {
            $composerName = $this->resolveComposerName(explode('\\', $useStatement), $moduleName);

            if ($composerName === null) {
                continue;
            }

            // A test namespace is a dev dependency wherever it is imported from: modules that ship
            // Codeception helpers in src/ would otherwise be asked for a require entry.
            $dependencyContainer->addDependency($composerName, $this->getType(), false, true, $context->getOwnerFqcn());
        }

        return $dependencyContainer;
    }
}
