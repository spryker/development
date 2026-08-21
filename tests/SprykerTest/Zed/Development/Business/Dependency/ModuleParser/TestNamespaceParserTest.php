<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Development\Business\Dependency\ModuleParser;

use Codeception\Test\Unit;
use Spryker\Zed\Development\Business\Dependency\ModuleParser\TestNamespaceParser;
use Spryker\Zed\Development\DevelopmentConfig;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Development
 * @group Business
 * @group Dependency
 * @group ModuleParser
 * @group TestNamespaceParserTest
 * Add your own group annotations below this line
 */
class TestNamespaceParserTest extends Unit
{
    /**
     * @dataProvider organizationNameDataProvider
     */
    public function testGivenANamespaceWhenTheOrganizationIsResolvedThenTheOwningOrganizationIsReturned(
        string $namespace,
        ?string $expectedOrganizationName
    ): void {
        // Arrange
        $namespaceFragments = explode('\\', $namespace);

        // Act
        $organizationName = $this->createTestNamespaceParser()->resolveOrganizationName($namespaceFragments);

        // Assert
        $this->assertSame($expectedOrganizationName, $organizationName);
    }

    /**
     * @dataProvider moduleNameDataProvider
     */
    public function testGivenANamespaceWhenTheModuleIsResolvedThenTheModuleUnderTestIsReturned(
        string $namespace,
        ?string $expectedModuleName
    ): void {
        // Arrange
        $namespaceFragments = explode('\\', $namespace);

        // Act
        $moduleName = $this->createTestNamespaceParser()->resolveModuleName($namespaceFragments);

        // Assert
        $this->assertSame($expectedModuleName, $moduleName);
    }

    /**
     * @return array<string, array<string|null>>
     */
    public function organizationNameDataProvider(): array
    {
        return [
            'core test namespace' => ['SprykerTest\Client\Search\Helper\SearchHelperTrait', 'Spryker'],
            'shop test namespace' => ['SprykerShopTest\Yves\ShopUi\Helper\ShopUiHelper', 'SprykerShop'],
            'feature test namespace' => ['SprykerFeatureTest\Zed\SelfServicePortal\Helper\SelfServicePortalHelper', 'SprykerFeature'],
            'eco test namespace' => ['SprykerEcoTest\Zed\Algolia\Helper\AlgoliaHelper', 'SprykerEco'],
            'sdk test namespace' => ['SprykerSdkTest\Zed\AsyncApi\Helper\AsyncApiHelper', 'SprykerSdk'],
            'merchant portal test namespace' => ['SprykerMerchantPortalTest\Zed\DashboardMerchantPortalGui\Helper\DashboardHelper', 'SprykerMerchantPortal'],
            'project test namespace' => ['PyzTest\Zed\Search\Helper\SearchHelperTrait', null],
            'production namespace' => ['Spryker\Client\Search\SearchClientInterface', null],
            'namespace without fragments' => ['', null],
        ];
    }

    /**
     * @return array<string, array<string|null>>
     */
    public function moduleNameDataProvider(): array
    {
        return [
            'application layered namespace' => ['SprykerTest\Client\Search\Helper\SearchHelperTrait', 'Search'],
            'module layered namespace' => ['SprykerTest\ApiPlatform\Helper\ApiPlatformHelper', 'ApiPlatform'],
            'test application namespace' => ['SprykerTest\AsyncApi\MessageBroker\Helper\AsyncApiHelper', 'MessageBroker'],
            'test root namespace only' => ['SprykerTest', null],
            'application without module fragment' => ['SprykerTest\Client', null],
            'test application without module fragment' => ['SprykerTest\AsyncApi', null],
        ];
    }

    protected function createTestNamespaceParser(): TestNamespaceParser
    {
        return new TestNamespaceParser(new DevelopmentConfig());
    }
}
