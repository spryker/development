<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Development\Business\Dependency\Validator;

use ArrayObject;
use Codeception\Test\Unit;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Development
 * @group Business
 * @group Dependency
 * @group Validator
 * @group DependencyValidatorTest
 * Add your own group annotations below this line
 */
class DependencyValidatorTest extends Unit
{
    /**
     * @var \SprykerTest\Zed\Development\DevelopmentBusinessTester
     */
    protected $tester;

    public function testDependencyIsValidWhenDependencyTypeIsDevOnly(): void
    {
        $developmentFacade = $this->tester->getFacadeForDependencyTests($this->tester->getDevOnlyComposerDependency());
        $dependencyValidationResponseTransfer = $developmentFacade->validateModuleDependencies($this->tester->getDependencyValidationRequestTransfer());

        $this->tester->assertValidDependencies($dependencyValidationResponseTransfer);
    }

    public function testDependencyIsInvalidWhenDependencyInSourceButMissingInComposerRequire(): void
    {
        $developmentFacade = $this->tester->getFacadeForDependencyTests($this->tester->getInvalidSourceDependency());
        $dependencyValidationResponseTransfer = $developmentFacade->validateModuleDependencies($this->tester->getDependencyValidationRequestTransfer());

        $this->tester->assertInvalidDependencies($dependencyValidationResponseTransfer);
    }

    public function testDependencyIsInvalidWhenDependencyNotInSourceButInComposerRequire(): void
    {
        $developmentFacade = $this->tester->getFacadeForDependencyTests($this->tester->getInvalidRequireDependency());
        $dependencyValidationResponseTransfer = $developmentFacade->validateModuleDependencies($this->tester->getDependencyValidationRequestTransfer());

        $this->tester->assertInvalidDependencies($dependencyValidationResponseTransfer);
    }

    public function testDependencyIsValidWhenDependencyInSourceAndInComposerRequire(): void
    {
        $developmentFacade = $this->tester->getFacadeForDependencyTests($this->tester->getValidSourceDependency());
        $dependencyValidationResponseTransfer = $developmentFacade->validateModuleDependencies($this->tester->getDependencyValidationRequestTransfer());

        $this->tester->assertValidDependencies($dependencyValidationResponseTransfer);
    }

    public function testDependencyIsInvalidWhenDependencyInTestButMissingInComposerRequireDev(): void
    {
        $developmentFacade = $this->tester->getFacadeForDependencyTests($this->tester->getInvalidTestDependency());
        $dependencyValidationResponseTransfer = $developmentFacade->validateModuleDependencies($this->tester->getDependencyValidationRequestTransfer());

        $this->tester->assertInvalidDependencies($dependencyValidationResponseTransfer);
    }

    public function testDependencyIsInvalidWhenDependencyNotInTestButInComposerRequireDev(): void
    {
        $developmentFacade = $this->tester->getFacadeForDependencyTests($this->tester->getInvalidRequireDevDependency());
        $dependencyValidationResponseTransfer = $developmentFacade->validateModuleDependencies($this->tester->getDependencyValidationRequestTransfer());

        $this->tester->assertInvalidDependencies($dependencyValidationResponseTransfer);
    }

    public function testDependencyIsValidWhenDependencyInTestAndInComposerRequireDev(): void
    {
        $developmentFacade = $this->tester->getFacadeForDependencyTests($this->tester->getInvalidTestDependency());
        $dependencyValidationResponseTransfer = $developmentFacade->validateModuleDependencies($this->tester->getDependencyValidationRequestTransfer());

        $this->tester->assertInvalidDependencies($dependencyValidationResponseTransfer);
    }

    public function testDependencyIsValidWhenDependencyIsOptionalAndNotInRequire(): void
    {
        $developmentFacade = $this->tester->getFacadeForDependencyTests($this->tester->getValidOptionalRequiredDevDependency());
        $dependencyValidationResponseTransfer = $developmentFacade->validateModuleDependencies($this->tester->getDependencyValidationRequestTransfer());

        $this->tester->assertValidDependencies($dependencyValidationResponseTransfer);
    }

    public function testDependencyIsInvalidWhenDependencyIsOptionalButInRequire(): void
    {
        $developmentFacade = $this->tester->getFacadeForDependencyTests($this->tester->getInvalidOptionalRequiredDependency());
        $dependencyValidationResponseTransfer = $developmentFacade->validateModuleDependencies($this->tester->getDependencyValidationRequestTransfer());

        $this->tester->assertInvalidDependencies($dependencyValidationResponseTransfer);
    }

    public function testDependencyIsInvalidWhenDependencyIsOptionalButNotInRequireDev(): void
    {
        $developmentFacade = $this->tester->getFacadeForDependencyTests($this->tester->getInvalidOptionalNotRequiredDevDependency());
        $dependencyValidationResponseTransfer = $developmentFacade->validateModuleDependencies($this->tester->getDependencyValidationRequestTransfer());

        $this->tester->assertInvalidDependencies($dependencyValidationResponseTransfer);
    }

    public function testDependencyIsInvalidWhenDependencyIsOptionalButNotSuggested(): void
    {
        $developmentFacade = $this->tester->getFacadeForDependencyTests($this->tester->getInvalidOptionalNotSuggestedDependency());
        $dependencyValidationResponseTransfer = $developmentFacade->validateModuleDependencies($this->tester->getDependencyValidationRequestTransfer());

        $this->tester->assertInvalidDependencies($dependencyValidationResponseTransfer);
    }

    public function testDependencyIsInvalidWhenDependencyInRequireAndInRequireDev(): void
    {
        $developmentFacade = $this->tester->getFacadeForDependencyTests($this->tester->getInvalidRequireAndRequireDevDependency());
        $dependencyValidationResponseTransfer = $developmentFacade->validateModuleDependencies($this->tester->getDependencyValidationRequestTransfer());

        $this->tester->assertInvalidDependencies($dependencyValidationResponseTransfer);
    }

    public function testUsedByFqcnsArePopulatedWhenRequested(): void
    {
        $developmentFacade = $this->tester->getFacadeForDependencyTestsWithUsage($this->tester->getValidSourceDependencyWithUsage());
        $dependencyValidationRequestTransfer = $this->tester->getDependencyValidationRequestTransfer();
        $dependencyValidationRequestTransfer->setIsWithUsage(true);

        $dependencyValidationResponseTransfer = $developmentFacade->validateModuleDependencies($dependencyValidationRequestTransfer);

        $moduleDependencies = $dependencyValidationResponseTransfer->getModuleDependencies()->getArrayCopy();
        $this->assertCount(1, $moduleDependencies);
        $moduleDependencyTransfer = $moduleDependencies[0];
        $this->assertSame(
            [
                'Spryker\\Zed\\Foo\\Business\\FooFacade',
                'Spryker\\Zed\\Foo\\Business\\Model\\FooModel',
            ],
            $this->toArray($moduleDependencyTransfer->getUsedByFqcns()),
        );
    }

    /**
     * @param \ArrayObject<int, string>|array<string> $collection
     *
     * @return array<string>
     */
    protected function toArray($collection): array
    {
        if ($collection instanceof ArrayObject) {
            return $collection->getArrayCopy();
        }

        return (array)$collection;
    }
}
