<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Development\Business\Dependency\Cycle\Algorithm;

class TarjanCycleFinder implements CycleFinderInterface
{
    /**
     * @param array<string, array<string, bool>> $adjacency
     *
     * @return array<array<string>>
     */
    public function findCycles(array $adjacency): array
    {
        $state = [
            'index' => 0,
            'indices' => [],
            'lowlinks' => [],
            'onStack' => [],
            'stack' => [],
            'sccs' => [],
        ];

        foreach (array_keys($adjacency) as $node) {
            if (!isset($state['indices'][$node])) {
                $this->strongConnect((string)$node, $adjacency, $state);
            }
        }

        $cycles = [];
        foreach ($state['sccs'] as $scc) {
            if ($this->isCycle($scc, $adjacency)) {
                $cycles[] = $this->canonicalize($scc);
            }
        }

        return $cycles;
    }

    /**
     * @param string $node
     * @param array<string, array<string, bool>> $adjacency
     * @param array<string, mixed> $state
     *
     * @return void
     */
    protected function strongConnect(string $node, array $adjacency, array &$state): void
    {
        $state['indices'][$node] = $state['index'];
        $state['lowlinks'][$node] = $state['index'];
        $state['index']++;
        $state['stack'][] = $node;
        $state['onStack'][$node] = true;

        foreach ($adjacency[$node] ?? [] as $neighbour => $_) {
            $neighbour = (string)$neighbour;
            if (!isset($adjacency[$neighbour])) {
                continue;
            }

            if (!isset($state['indices'][$neighbour])) {
                $this->strongConnect($neighbour, $adjacency, $state);
                $state['lowlinks'][$node] = min($state['lowlinks'][$node], $state['lowlinks'][$neighbour]);

                continue;
            }

            if (!empty($state['onStack'][$neighbour])) {
                $state['lowlinks'][$node] = min($state['lowlinks'][$node], $state['indices'][$neighbour]);
            }
        }

        if ($state['lowlinks'][$node] !== $state['indices'][$node]) {
            return;
        }

        $scc = [];
        do {
            $stackNode = array_pop($state['stack']);
            unset($state['onStack'][$stackNode]);
            $scc[] = $stackNode;
        } while ($stackNode !== $node);

        $state['sccs'][] = $scc;
    }

    /**
     * @param array<string> $scc
     * @param array<string, array<string, bool>> $adjacency
     *
     * @return bool
     */
    protected function isCycle(array $scc, array $adjacency): bool
    {
        if (count($scc) > 1) {
            return true;
        }

        $single = $scc[0] ?? null;
        if ($single === null) {
            return false;
        }

        return isset($adjacency[$single][$single]);
    }

    /**
     * @param array<string> $scc
     *
     * @return array<string>
     */
    protected function canonicalize(array $scc): array
    {
        sort($scc);

        return $scc;
    }
}
