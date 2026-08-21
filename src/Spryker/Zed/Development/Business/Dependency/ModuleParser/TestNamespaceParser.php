<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Development\Business\Dependency\ModuleParser;

use Spryker\Zed\Development\DevelopmentConfig;

class TestNamespaceParser implements TestNamespaceParserInterface
{
    public function __construct(protected DevelopmentConfig $config)
    {
    }

    /**
     * @param array<int, string> $namespaceFragments
     */
    public function resolveOrganizationName(array $namespaceFragments): ?string
    {
        if (!isset($namespaceFragments[0])) {
            return null;
        }

        return $this->config->getTestNamespaceToOrganizationMap()[$namespaceFragments[0]] ?? null;
    }

    /**
     * Covers both the `<TestNamespace>\<Application>\<Module>` layout and the newer
     * `<TestNamespace>\<Module>` one, for example `SprykerTest\ApiPlatform\Helper`.
     *
     * @param array<int, string> $namespaceFragments
     */
    public function resolveModuleName(array $namespaceFragments): ?string
    {
        if (!isset($namespaceFragments[1])) {
            return null;
        }

        $secondFragment = $namespaceFragments[1];

        if (
            in_array($secondFragment, $this->config->getApplications(), true)
            || in_array($secondFragment, $this->config->getTestApplicationNamespaces(), true)
        ) {
            return $namespaceFragments[2] ?? null;
        }

        return $secondFragment;
    }
}
