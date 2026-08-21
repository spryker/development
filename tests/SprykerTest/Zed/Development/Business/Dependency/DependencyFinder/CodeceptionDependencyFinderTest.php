<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Development\Business\Dependency\DependencyFinder;

use Codeception\Test\Unit;
use Spryker\Zed\Development\Business\Dependency\DependencyFinder\CodeceptionDependencyFinder;
use SprykerTest\Zed\Development\DevelopmentBusinessTester;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Development
 * @group Business
 * @group Dependency
 * @group DependencyFinder
 * @group CodeceptionDependencyFinderTest
 * Add your own group annotations below this line
 */
class CodeceptionDependencyFinderTest extends Unit
{
    protected const string ORGANIZATION_NAME = 'Spryker';

    protected const string MODULE_NAME = 'Acl';

    protected const string CODECEPTION_CONFIGURATION_PATHNAME = 'tests/codeception.yml';

    protected DevelopmentBusinessTester $tester;

    public function testGivenEnabledHelpersOfOtherModulesWhenDependenciesAreFoundThenTheirPackagesAreReported(): void
    {
        // Arrange
        $virtualModuleFiles = [
            static::CODECEPTION_CONFIGURATION_PATHNAME => $this->tester->buildCodeceptionConfigurationWithEnabledModules([
                '\SprykerTest\Zed\Application\Helper\ApplicationHelper',
                '\SprykerTest\ApiPlatform\Helper\ApiPlatformHelper',
            ]),
        ];

        // Act
        $dependencyTransfers = $this->tester->findOutgoingDependencies(
            static::ORGANIZATION_NAME,
            static::MODULE_NAME,
            CodeceptionDependencyFinder::TYPE_CODECEPTION,
            $virtualModuleFiles,
        );

        // Assert
        $this->assertSame(['spryker/api-platform', 'spryker/application'], array_keys($dependencyTransfers));
    }

    public function testGivenEnabledHelpersOfOtherOrganizationsWhenDependenciesAreFoundThenTheirVendorNamesAreResolved(): void
    {
        // Arrange
        $virtualModuleFiles = [
            static::CODECEPTION_CONFIGURATION_PATHNAME => $this->tester->buildCodeceptionConfigurationWithEnabledModules([
                '\SprykerShopTest\Yves\ShopUi\Helper\ShopUiHelper',
                '\SprykerFeatureTest\Zed\SelfServicePortal\Helper\SelfServicePortalHelper',
            ]),
        ];

        // Act
        $dependencyTransfers = $this->tester->findOutgoingDependencies(
            static::ORGANIZATION_NAME,
            static::MODULE_NAME,
            CodeceptionDependencyFinder::TYPE_CODECEPTION,
            $virtualModuleFiles,
        );

        // Assert
        $this->assertSame(['spryker-feature/self-service-portal', 'spryker-shop/shop-ui'], array_keys($dependencyTransfers));
    }

    public function testGivenAnEnabledHelperOfTheOwnModuleWhenDependenciesAreFoundThenNothingIsReported(): void
    {
        // Arrange
        $virtualModuleFiles = [
            static::CODECEPTION_CONFIGURATION_PATHNAME => $this->tester->buildCodeceptionConfigurationWithEnabledModules([
                '\SprykerTest\Zed\Acl\Helper\AclHelper',
            ]),
        ];

        // Act
        $dependencyTransfers = $this->tester->findOutgoingDependencies(
            static::ORGANIZATION_NAME,
            static::MODULE_NAME,
            CodeceptionDependencyFinder::TYPE_CODECEPTION,
            $virtualModuleFiles,
        );

        // Assert
        $this->assertSame([], array_keys($dependencyTransfers));
    }

    public function testGivenAModuleNamespacingItsOwnTestsUnderAForeignOrganizationWhenDependenciesAreFoundThenNothingIsReported(): void
    {
        // Arrange
        $virtualModuleFiles = [
            static::CODECEPTION_CONFIGURATION_PATHNAME => $this->tester->buildCodeceptionConfigurationWithEnabledModules([
                '\SprykerFeatureTest\Zed\SelfServicePortal\Helper\SelfServicePortalHelper',
            ]),
        ];

        // Act
        $dependencyTransfers = $this->tester->findOutgoingDependencies(
            'SprykerFeature',
            'SelfServicePortal',
            CodeceptionDependencyFinder::TYPE_CODECEPTION,
            $virtualModuleFiles,
        );

        // Assert
        $this->assertSame([], array_keys($dependencyTransfers));
    }

    public function testGivenAnEnabledHelperWhoseVendorNameEndsInATestRootNamespaceWhenDependenciesAreFoundThenNothingIsReported(): void
    {
        // Arrange
        $virtualModuleFiles = [
            static::CODECEPTION_CONFIGURATION_PATHNAME => $this->tester->buildCodeceptionConfigurationWithEnabledModules([
                '\\Foo\\MySprykerTest\\Zed\\Bar\\Helper\\BarHelper',
            ]),
        ];

        // Act
        $dependencyTransfers = $this->tester->findOutgoingDependencies(
            static::ORGANIZATION_NAME,
            static::MODULE_NAME,
            CodeceptionDependencyFinder::TYPE_CODECEPTION,
            $virtualModuleFiles,
        );

        // Assert
        $this->assertSame([], array_keys($dependencyTransfers));
    }
}
