<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Development\Business\Dependency\ModuleParser;

interface TestNamespaceParserInterface
{
    /**
     * @param array<int, string> $namespaceFragments
     */
    public function resolveOrganizationName(array $namespaceFragments): ?string;

    /**
     * @param array<int, string> $namespaceFragments
     */
    public function resolveModuleName(array $namespaceFragments): ?string;
}
