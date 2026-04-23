<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Development\Business\Dependency\Cycle\Algorithm;

class DirectCycleFinder implements CycleFinderInterface
{
    /**
     * @param array<string, array<string, bool>> $adjacency
     *
     * @return array<array<string>>
     */
    public function findCycles(array $adjacency): array
    {
        $cycles = [];
        $seen = [];

        foreach ($adjacency as $from => $neighbours) {
            foreach ($neighbours as $to => $_) {
                if ($from === $to) {
                    continue;
                }

                if (!isset($adjacency[$to][$from])) {
                    continue;
                }

                $pairKey = $this->buildPairKey($from, (string)$to);
                if (isset($seen[$pairKey])) {
                    continue;
                }

                $seen[$pairKey] = true;
                $cycles[] = $this->canonicalize([$from, (string)$to]);
            }
        }

        return $cycles;
    }

    protected function buildPairKey(string $nodeA, string $nodeB): string
    {
        $pair = [$nodeA, $nodeB];
        sort($pair);

        return implode('|', $pair);
    }

    /**
     * @param array<string> $cycle
     *
     * @return array<string>
     */
    protected function canonicalize(array $cycle): array
    {
        $minIndex = 0;
        foreach ($cycle as $index => $node) {
            if ($node < $cycle[$minIndex]) {
                $minIndex = $index;
            }
        }

        return array_merge(array_slice($cycle, $minIndex), array_slice($cycle, 0, $minIndex));
    }
}
