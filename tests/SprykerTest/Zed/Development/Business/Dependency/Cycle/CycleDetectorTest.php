<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Development\Business\Dependency\Cycle;

use ArrayObject;
use Codeception\Stub;
use Codeception\Test\Unit;
use Generated\Shared\Transfer\CycleDetectionRequestTransfer;
use Generated\Shared\Transfer\CycleTransfer;
use Spryker\Zed\Development\Business\Dependency\Cycle\Algorithm\CycleFinderInterface;
use Spryker\Zed\Development\Business\Dependency\Cycle\Algorithm\DirectCycleFinder;
use Spryker\Zed\Development\Business\Dependency\Cycle\Algorithm\TarjanCycleFinder;
use Spryker\Zed\Development\Business\Dependency\Cycle\CycleDetector;
use Spryker\Zed\Development\Business\Dependency\Cycle\Graph\CycleGraphBuilderInterface;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Development
 * @group Business
 * @group Dependency
 * @group Cycle
 * @group CycleDetectorTest
 * Add your own group annotations below this line
 */
class CycleDetectorTest extends Unit
{
    public function testDeclaredCyclesAreReturnedWithDeclaredSource(): void
    {
        $declaredGraph = [
            'spryker/a' => ['spryker/b' => true],
            'spryker/b' => ['spryker/a' => true],
        ];
        $cycleDetector = $this->createCycleDetector($declaredGraph, []);

        $cycleDetectionResponseTransfer = $cycleDetector->detectCycles(new CycleDetectionRequestTransfer());
        $declaredCycleTransfers = $this->toList($cycleDetectionResponseTransfer->getDeclaredCycles());

        $this->assertCount(1, $declaredCycleTransfers);
        $this->assertCount(0, $cycleDetectionResponseTransfer->getUsageOnlyCycles());
        $this->assertSame(CycleDetector::SOURCE_DECLARED, $declaredCycleTransfers[0]->getSource());
        $this->assertSame(2, $declaredCycleTransfers[0]->getLength());
        $this->assertSame(['spryker/a', 'spryker/b'], $declaredCycleTransfers[0]->getModuleChain());
    }

    public function testUsageOnlyCycleExcludesDeclaredCycle(): void
    {
        $declaredGraph = [
            'spryker/a' => ['spryker/b' => true],
            'spryker/b' => ['spryker/a' => true],
        ];
        $usageGraph = [
            'spryker/a' => ['spryker/b' => true],
            'spryker/b' => ['spryker/a' => true],
            'spryker/c' => ['spryker/d' => true],
            'spryker/d' => ['spryker/c' => true],
        ];
        $cycleDetector = $this->createCycleDetector($declaredGraph, $usageGraph);

        $cycleDetectionResponseTransfer = $cycleDetector->detectCycles(new CycleDetectionRequestTransfer());
        $usageOnlyCycleTransfers = $this->toList($cycleDetectionResponseTransfer->getUsageOnlyCycles());

        $this->assertCount(1, $cycleDetectionResponseTransfer->getDeclaredCycles());
        $this->assertCount(1, $usageOnlyCycleTransfers);
        $this->assertSame(CycleDetector::SOURCE_USAGE_ONLY, $usageOnlyCycleTransfers[0]->getSource());
        $this->assertSame(['spryker/c', 'spryker/d'], $usageOnlyCycleTransfers[0]->getModuleChain());
    }

    public function testUsesTarjanFinderWhenIsDeepIsTrue(): void
    {
        $declaredGraph = [
            'spryker/a' => ['spryker/b' => true],
            'spryker/b' => ['spryker/c' => true],
            'spryker/c' => ['spryker/a' => true],
        ];
        $cycleDetector = $this->createCycleDetector($declaredGraph, []);
        $cycleDetectionRequestTransfer = (new CycleDetectionRequestTransfer())->setIsDeep(true);

        $cycleDetectionResponseTransfer = $cycleDetector->detectCycles($cycleDetectionRequestTransfer);
        $declaredCycleTransfers = $this->toList($cycleDetectionResponseTransfer->getDeclaredCycles());

        $this->assertCount(1, $declaredCycleTransfers);
        $this->assertSame(3, $declaredCycleTransfers[0]->getLength());
    }

    public function testDirectFinderIgnoresThreeCyclesByDefault(): void
    {
        $declaredGraph = [
            'spryker/a' => ['spryker/b' => true],
            'spryker/b' => ['spryker/c' => true],
            'spryker/c' => ['spryker/a' => true],
        ];
        $cycleDetector = $this->createCycleDetector($declaredGraph, []);

        $cycleDetectionResponseTransfer = $cycleDetector->detectCycles(new CycleDetectionRequestTransfer());

        $this->assertCount(0, $cycleDetectionResponseTransfer->getDeclaredCycles());
    }

    public function testReturnsDeterministicOrdering(): void
    {
        $declaredGraph = [
            'spryker/z' => ['spryker/y' => true],
            'spryker/y' => ['spryker/z' => true],
            'spryker/a' => ['spryker/b' => true],
            'spryker/b' => ['spryker/a' => true],
        ];
        $cycleDetector = $this->createCycleDetector($declaredGraph, []);

        $cycleDetectionResponseTransfer = $cycleDetector->detectCycles(new CycleDetectionRequestTransfer());
        $declaredCycleTransfers = $this->toList($cycleDetectionResponseTransfer->getDeclaredCycles());

        $this->assertCount(2, $declaredCycleTransfers);
        $this->assertSame(['spryker/a', 'spryker/b'], $declaredCycleTransfers[0]->getModuleChain());
        $this->assertSame(['spryker/y', 'spryker/z'], $declaredCycleTransfers[1]->getModuleChain());
    }

    /**
     * @param array<string, array<string, bool>> $declaredGraph
     * @param array<string, array<string, bool>> $usageGraph
     *
     * @return \Spryker\Zed\Development\Business\Dependency\Cycle\CycleDetector
     */
    protected function createCycleDetector(array $declaredGraph, array $usageGraph): CycleDetector
    {
        /** @var \Spryker\Zed\Development\Business\Dependency\Cycle\Graph\CycleGraphBuilderInterface $cycleGraphBuilderMock */
        $cycleGraphBuilderMock = Stub::makeEmpty(CycleGraphBuilderInterface::class, [
            'buildDeclaredGraph' => $declaredGraph,
            'buildUsageGraph' => $usageGraph,
        ]);

        return new CycleDetector(
            $cycleGraphBuilderMock,
            $this->createDirectCycleFinder(),
            $this->createTarjanCycleFinder(),
        );
    }

    protected function createDirectCycleFinder(): CycleFinderInterface
    {
        return new DirectCycleFinder();
    }

    protected function createTarjanCycleFinder(): CycleFinderInterface
    {
        return new TarjanCycleFinder();
    }

    /**
     * @param \ArrayObject<int, \Generated\Shared\Transfer\CycleTransfer> $cycleTransfers
     *
     * @return array<\Generated\Shared\Transfer\CycleTransfer>
     */
    protected function toList(ArrayObject $cycleTransfers): array
    {
        return array_values(array_map(static fn (CycleTransfer $cycleTransfer): CycleTransfer => $cycleTransfer, $cycleTransfers->getArrayCopy()));
    }
}
