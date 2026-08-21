<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Development\Business\Dependency\DependencyFinder;

use Codeception\Test\Unit;
use Spryker\Zed\Development\Business\Dependency\DependencyFinder\InternalDependencyFinder;
use Spryker\Zed\Development\Business\Dependency\DependencyFinder\InternalTestDependencyFinder;
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
 * @group InternalTestDependencyFinderTest
 * Add your own group annotations below this line
 */
class InternalTestDependencyFinderTest extends Unit
{
    protected const string ORGANIZATION_NAME = 'Spryker';

    protected const string MODULE_NAME = 'Queue';

    protected const string HELPER_PATHNAME_IN_TESTS = 'tests/QueueHelper.php';

    protected DevelopmentBusinessTester $tester;

    public function testGivenAnApplicationLayeredTestNamespaceWhenDependenciesAreFoundThenTheOwningModulePackageIsReported(): void
    {
        // Arrange
        $virtualModuleFiles = [
            static::HELPER_PATHNAME_IN_TESTS => $this->tester->buildPhpFileWithUseStatements([
                'SprykerTest\Client\Search\Helper\SearchHelperTrait',
            ]),
        ];

        // Act
        $dependencyTransfers = $this->tester->findOutgoingDependencies(
            static::ORGANIZATION_NAME,
            static::MODULE_NAME,
            InternalTestDependencyFinder::TYPE_INTERNAL_TEST,
            $virtualModuleFiles,
        );

        // Assert
        $this->assertSame(['spryker/search'], array_keys($dependencyTransfers));
        $this->assertSame(InternalTestDependencyFinder::TYPE_INTERNAL_TEST, $dependencyTransfers['spryker/search']->getType());
    }

    public function testGivenAModuleLayeredTestNamespaceWhenDependenciesAreFoundThenTheModuleIsTakenFromTheSecondFragment(): void
    {
        // Arrange
        $virtualModuleFiles = [
            static::HELPER_PATHNAME_IN_TESTS => $this->tester->buildPhpFileWithUseStatements([
                'SprykerTest\ApiPlatform\Helper\ApiPlatformHelperTrait',
            ]),
        ];

        // Act
        $dependencyTransfers = $this->tester->findOutgoingDependencies(
            static::ORGANIZATION_NAME,
            static::MODULE_NAME,
            InternalTestDependencyFinder::TYPE_INTERNAL_TEST,
            $virtualModuleFiles,
        );

        // Assert
        $this->assertSame(['spryker/api-platform'], array_keys($dependencyTransfers));
    }

    public function testGivenATestApplicationNamespaceWhenDependenciesAreFoundThenTheModuleIsTakenFromTheThirdFragment(): void
    {
        // Arrange
        $virtualModuleFiles = [
            static::HELPER_PATHNAME_IN_TESTS => $this->tester->buildPhpFileWithUseStatements([
                'SprykerTest\AsyncApi\MessageBroker\Helper\AsyncApiHelper',
            ]),
        ];

        // Act
        $dependencyTransfers = $this->tester->findOutgoingDependencies(
            static::ORGANIZATION_NAME,
            static::MODULE_NAME,
            InternalTestDependencyFinder::TYPE_INTERNAL_TEST,
            $virtualModuleFiles,
        );

        // Assert
        $this->assertSame(['spryker/message-broker'], array_keys($dependencyTransfers));
    }

    public function testGivenTestNamespacesOfOtherOrganizationsWhenDependenciesAreFoundThenTheirVendorNamesAreResolved(): void
    {
        // Arrange
        $virtualModuleFiles = [
            static::HELPER_PATHNAME_IN_TESTS => $this->tester->buildPhpFileWithUseStatements([
                'SprykerShopTest\Yves\ShopUi\Helper\ShopUiHelper',
                'SprykerFeatureTest\Zed\SelfServicePortal\Helper\SelfServicePortalHelper',
            ]),
        ];

        // Act
        $dependencyTransfers = $this->tester->findOutgoingDependencies(
            static::ORGANIZATION_NAME,
            static::MODULE_NAME,
            InternalTestDependencyFinder::TYPE_INTERNAL_TEST,
            $virtualModuleFiles,
        );

        // Assert
        $this->assertSame(['spryker-feature/self-service-portal', 'spryker-shop/shop-ui'], array_keys($dependencyTransfers));
    }

    public function testGivenATestNamespaceOfTheOwnModuleWhenDependenciesAreFoundThenNothingIsReported(): void
    {
        // Arrange
        $virtualModuleFiles = [
            static::HELPER_PATHNAME_IN_TESTS => $this->tester->buildPhpFileWithUseStatements([
                'SprykerTest\Client\Queue\Helper\QueueHelperTrait',
            ]),
        ];

        // Act
        $dependencyTransfers = $this->tester->findOutgoingDependencies(
            static::ORGANIZATION_NAME,
            static::MODULE_NAME,
            InternalTestDependencyFinder::TYPE_INTERNAL_TEST,
            $virtualModuleFiles,
        );

        // Assert
        $this->assertSame([], array_keys($dependencyTransfers));
    }

    public function testGivenAModuleNamespacingItsOwnTestsUnderAForeignOrganizationWhenDependenciesAreFoundThenNothingIsReported(): void
    {
        // Arrange
        $virtualModuleFiles = [
            'tests/DateTimeConfiguratorPageExampleHelper.php' => $this->tester->buildPhpFileWithUseStatements([
                'SprykerTest\Yves\DateTimeConfiguratorPageExample\Helper\DateTimeConfiguratorPageExampleHelper',
            ]),
        ];

        // Act
        $dependencyTransfers = $this->tester->findOutgoingDependencies(
            'SprykerShop',
            'DateTimeConfiguratorPageExample',
            InternalTestDependencyFinder::TYPE_INTERNAL_TEST,
            $virtualModuleFiles,
        );

        // Assert
        $this->assertSame([], array_keys($dependencyTransfers));
    }

    public function testGivenProjectAndProductionNamespacesWhenDependenciesAreFoundThenNothingIsReported(): void
    {
        // Arrange
        $virtualModuleFiles = [
            static::HELPER_PATHNAME_IN_TESTS => $this->tester->buildPhpFileWithUseStatements([
                'PyzTest\Zed\Search\Helper\SearchHelperTrait',
                'Spryker\Client\Search\SearchClientInterface',
            ]),
        ];

        // Act
        $dependencyTransfers = $this->tester->findOutgoingDependencies(
            static::ORGANIZATION_NAME,
            static::MODULE_NAME,
            InternalTestDependencyFinder::TYPE_INTERNAL_TEST,
            $virtualModuleFiles,
        );

        // Assert
        $this->assertSame([], array_keys($dependencyTransfers));
    }

    public function testGivenATestNamespaceImportedFromSourceWhenDependenciesAreFoundThenTheDependencyIsStillMarkedAsInTest(): void
    {
        // Arrange
        $virtualModuleFiles = [
            'src/CodeceptionArgumentsBuilder.php' => $this->tester->buildPhpFileWithUseStatements([
                'SprykerTest\Client\Search\Helper\SearchHelperTrait',
            ]),
        ];

        // Act
        $dependencyTransfers = $this->tester->findOutgoingDependencies(
            static::ORGANIZATION_NAME,
            static::MODULE_NAME,
            InternalTestDependencyFinder::TYPE_INTERNAL_TEST,
            $virtualModuleFiles,
        );

        // Assert
        $this->assertTrue($dependencyTransfers['spryker/search']->getIsInTest());
    }

    public function testGivenANonPhpFileWhenDependenciesAreFoundThenNothingIsReported(): void
    {
        // Arrange
        $virtualModuleFiles = [
            'tests/_data/use_statement_fixture.txt' => $this->tester->buildPhpFileWithUseStatements([
                'SprykerTest\Client\Search\Helper\SearchHelperTrait',
            ]),
        ];

        // Act
        $dependencyTransfers = $this->tester->findOutgoingDependencies(
            static::ORGANIZATION_NAME,
            static::MODULE_NAME,
            InternalTestDependencyFinder::TYPE_INTERNAL_TEST,
            $virtualModuleFiles,
        );

        // Assert
        $this->assertSame([], array_keys($dependencyTransfers));
    }

    public function testGivenAForeignDependencyTypeIsRequestedWhenDependenciesAreFoundThenNothingIsReported(): void
    {
        // Arrange
        $virtualModuleFiles = [
            static::HELPER_PATHNAME_IN_TESTS => $this->tester->buildPhpFileWithUseStatements([
                'SprykerTest\Client\Search\Helper\SearchHelperTrait',
            ]),
        ];

        // Act
        $dependencyTransfers = $this->tester->findOutgoingDependencies(
            static::ORGANIZATION_NAME,
            static::MODULE_NAME,
            InternalDependencyFinder::TYPE_INTERNAL,
            $virtualModuleFiles,
        );

        // Assert
        $this->assertSame([], array_keys($dependencyTransfers));
    }
}
