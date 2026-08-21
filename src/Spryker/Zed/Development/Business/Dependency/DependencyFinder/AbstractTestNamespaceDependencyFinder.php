<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Development\Business\Dependency\DependencyFinder;

use Spryker\Zed\Development\Business\Dependency\ModuleParser\TestNamespaceParserInterface;

abstract class AbstractTestNamespaceDependencyFinder extends AbstractFileDependencyFinder
{
    public function __construct(protected TestNamespaceParserInterface $testNamespaceParser)
    {
    }

    /**
     * @param array<int, string> $namespaceFragments
     */
    protected function resolveComposerName(array $namespaceFragments, string $moduleName): ?string
    {
        $organizationName = $this->testNamespaceParser->resolveOrganizationName($namespaceFragments);

        if ($organizationName === null) {
            return null;
        }

        $foreignModuleName = $this->testNamespaceParser->resolveModuleName($namespaceFragments);

        if ($foreignModuleName === null || $foreignModuleName === $moduleName) {
            return null;
        }

        return $this->buildComposerName($organizationName, $foreignModuleName);
    }
}
