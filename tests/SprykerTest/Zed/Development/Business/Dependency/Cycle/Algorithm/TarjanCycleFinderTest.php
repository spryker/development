<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Development\Business\Dependency\Cycle\Algorithm;

use Codeception\Test\Unit;
use Spryker\Zed\Development\Business\Dependency\Cycle\Algorithm\TarjanCycleFinder;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Development
 * @group Business
 * @group Dependency
 * @group Cycle
 * @group Algorithm
 * @group TarjanCycleFinderTest
 * Add your own group annotations below this line
 */
class TarjanCycleFinderTest extends Unit
{
    public function testReturnsNoCyclesForAcyclicGraph(): void
    {
        $adjacency = [
            'a' => ['b' => true],
            'b' => ['c' => true],
            'c' => [],
        ];

        $this->assertSame([], (new TarjanCycleFinder())->findCycles($adjacency));
    }

    public function testDetectsTwoNodeCycle(): void
    {
        $adjacency = [
            'a' => ['b' => true],
            'b' => ['a' => true],
        ];

        $cycles = (new TarjanCycleFinder())->findCycles($adjacency);

        $this->assertCount(1, $cycles);
        $this->assertSame(['a', 'b'], $cycles[0]);
    }

    public function testDetectsThreeNodeCycle(): void
    {
        $adjacency = [
            'a' => ['b' => true],
            'b' => ['c' => true],
            'c' => ['a' => true],
        ];

        $cycles = (new TarjanCycleFinder())->findCycles($adjacency);

        $this->assertCount(1, $cycles);
        $this->assertSame(['a', 'b', 'c'], $cycles[0]);
    }

    public function testDetectsFiveNodeCycle(): void
    {
        $adjacency = [
            'a' => ['b' => true],
            'b' => ['c' => true],
            'c' => ['d' => true],
            'd' => ['e' => true],
            'e' => ['a' => true],
        ];

        $cycles = (new TarjanCycleFinder())->findCycles($adjacency);

        $this->assertCount(1, $cycles);
        $this->assertSame(['a', 'b', 'c', 'd', 'e'], $cycles[0]);
    }

    public function testHandlesDisconnectedSubgraphs(): void
    {
        $adjacency = [
            'a' => ['b' => true],
            'b' => ['a' => true],
            'c' => ['d' => true],
            'd' => ['c' => true],
            'e' => ['f' => true],
            'f' => [],
        ];

        $cycles = (new TarjanCycleFinder())->findCycles($adjacency);

        $this->assertCount(2, $cycles);
    }

    public function testDetectsSelfLoopAsCycle(): void
    {
        $adjacency = [
            'a' => ['a' => true],
        ];

        $cycles = (new TarjanCycleFinder())->findCycles($adjacency);

        $this->assertCount(1, $cycles);
        $this->assertSame(['a'], $cycles[0]);
    }

    public function testIgnoresEdgeToUnknownNode(): void
    {
        $adjacency = [
            'a' => ['b' => true, 'ghost' => true],
            'b' => ['a' => true],
        ];

        $cycles = (new TarjanCycleFinder())->findCycles($adjacency);

        $this->assertCount(1, $cycles);
        $this->assertSame(['a', 'b'], $cycles[0]);
    }
}
