<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Development\Business;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\ModuleFilterTransfer;
use Generated\Shared\Transfer\ModuleTransfer;
use Generated\Shared\Transfer\OrganizationTransfer;
use Spryker\Zed\Development\Business\DevelopmentFacadeInterface;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Development
 * @group Business
 * @group Facade
 * @group DevelopmentFacadeTest
 * Add your own group annotations below this line
 */
class DevelopmentFacadeTest extends Unit
{
    /**
     * @var \SprykerTest\Zed\Development\DevelopmentBusinessTester
     */
    protected $tester;

    public function testGetsModules(): void
    {
        $moduleTransferCollection = $this->getFacade()->getModules();

        $this->assertIsArray($moduleTransferCollection);
    }

    /**
     * @dataProvider moduleFilterDataProvider
     *
     * @param string $organizationName
     * @param string $moduleName
     * @param int $expectedModuleCount
     *
     * @return void
     */
    public function testGetsModulesWithModuleFilter(string $organizationName, string $moduleName, int $expectedModuleCount): void
    {
        $organizationTransfer = new OrganizationTransfer();
        $organizationTransfer->setName($organizationName);

        $moduleTransfer = new ModuleTransfer();
        $moduleTransfer->setName($moduleName);

        $moduleFilterTransfer = new ModuleFilterTransfer();
        $moduleFilterTransfer
            ->setOrganization($organizationTransfer)
            ->setModule($moduleTransfer);

        $moduleTransferCollection = $this->getFacade()->getModules($moduleFilterTransfer);

        $this->assertIsArray($moduleTransferCollection);
        $this->assertCount($expectedModuleCount, $moduleTransferCollection);

        if ($expectedModuleCount === 1) {
            $this->assertArrayHasKey('Spryker.Development', $moduleTransferCollection);
        }
    }

    public function moduleFilterDataProvider(): array
    {
        return [
            ['Spryker', 'Development', 1],
        ];
    }

    public function testGetsProjectModules(): void
    {
        $moduleTransferCollection = $this->getFacade()->getProjectModules();

        $this->assertIsArray($moduleTransferCollection);
    }

    public function testGetsPackages(): void
    {
        $packageTransferCollection = $this->getFacade()->getPackages();

        $this->assertIsArray($packageTransferCollection);
    }

    public function testGetModuleOverviewReturnsCollection(): void
    {
        $this->assertIsArray($this->getFacade()->getModuleOverview());
    }

    protected function getFacade(): DevelopmentFacadeInterface
    {
        return $this->tester->getFacade();
    }
}
