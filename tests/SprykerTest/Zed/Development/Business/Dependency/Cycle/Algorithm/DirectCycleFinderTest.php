<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Development\Business\Dependency\Cycle\Algorithm;

use Codeception\Test\Unit;
use Spryker\Zed\Development\Business\Dependency\Cycle\Algorithm\DirectCycleFinder;

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
 * @group DirectCycleFinderTest
 * Add your own group annotations below this line
 */
class DirectCycleFinderTest extends Unit
{
    public function testReturnsNoCyclesForAcyclicGraph(): void
    {
        $adjacency = [
            'a' => ['b' => true],
            'b' => ['c' => true],
            'c' => [],
        ];

        $this->assertSame([], (new DirectCycleFinder())->findCycles($adjacency));
    }

    public function testDetectsSingleTwoCycle(): void
    {
        $adjacency = [
            'a' => ['b' => true],
            'b' => ['a' => true],
        ];

        $cycles = (new DirectCycleFinder())->findCycles($adjacency);

        $this->assertCount(1, $cycles);
        $this->assertSame(['a', 'b'], $cycles[0]);
    }

    public function testIgnoresThreeCycleInDirectMode(): void
    {
        $adjacency = [
            'a' => ['b' => true],
            'b' => ['c' => true],
            'c' => ['a' => true],
        ];

        $this->assertSame([], (new DirectCycleFinder())->findCycles($adjacency));
    }

    public function testDedupsMirrorEdges(): void
    {
        $adjacency = [
            'a' => ['b' => true],
            'b' => ['a' => true],
            'c' => ['d' => true],
            'd' => ['c' => true],
        ];

        $cycles = (new DirectCycleFinder())->findCycles($adjacency);

        $this->assertCount(2, $cycles);
    }

    public function testIgnoresSelfLoops(): void
    {
        $adjacency = [
            'a' => ['a' => true, 'b' => true],
            'b' => ['a' => true],
        ];

        $cycles = (new DirectCycleFinder())->findCycles($adjacency);

        $this->assertCount(1, $cycles);
        $this->assertSame(['a', 'b'], $cycles[0]);
    }

    public function testCanonicalizesToLexicographicallySmallestFirst(): void
    {
        $adjacency = [
            'z' => ['a' => true],
            'a' => ['z' => true],
        ];

        $cycles = (new DirectCycleFinder())->findCycles($adjacency);

        $this->assertSame(['a', 'z'], $cycles[0]);
    }
}
