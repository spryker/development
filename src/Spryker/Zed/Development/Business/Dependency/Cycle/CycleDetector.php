<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Development\Business\Dependency\Cycle;

use Generated\Shared\Transfer\CycleDetectionRequestTransfer;
use Generated\Shared\Transfer\CycleDetectionResponseTransfer;
use Generated\Shared\Transfer\CycleTransfer;
use Spryker\Zed\Development\Business\Dependency\Cycle\Algorithm\CycleFinderInterface;
use Spryker\Zed\Development\Business\Dependency\Cycle\Graph\CycleGraphBuilderInterface;

class CycleDetector implements CycleDetectorInterface
{
    /**
     * @var string
     */
    public const FAIL_ON_DECLARED = 'declared';

    /**
     * @var string
     */
    public const FAIL_ON_ANY = 'any';

    /**
     * @var string
     */
    public const FAIL_ON_NONE = 'none';

    /**
     * @var string
     */
    public const SOURCE_DECLARED = 'declared';

    /**
     * @var string
     */
    public const SOURCE_USAGE_ONLY = 'usage-only';

    protected CycleGraphBuilderInterface $cycleGraphBuilder;

    protected CycleFinderInterface $directCycleFinder;

    protected CycleFinderInterface $tarjanCycleFinder;

    public function __construct(
        CycleGraphBuilderInterface $cycleGraphBuilder,
        CycleFinderInterface $directCycleFinder,
        CycleFinderInterface $tarjanCycleFinder
    ) {
        $this->cycleGraphBuilder = $cycleGraphBuilder;
        $this->directCycleFinder = $directCycleFinder;
        $this->tarjanCycleFinder = $tarjanCycleFinder;
    }

    public function detectCycles(CycleDetectionRequestTransfer $cycleDetectionRequestTransfer): CycleDetectionResponseTransfer
    {
        $cycleFinder = $cycleDetectionRequestTransfer->getIsDeep()
            ? $this->tarjanCycleFinder
            : $this->directCycleFinder;

        $declaredGraph = $this->cycleGraphBuilder->buildDeclaredGraph($cycleDetectionRequestTransfer);
        $usageGraph = $this->cycleGraphBuilder->buildUsageGraph($cycleDetectionRequestTransfer);

        $declaredCycles = $this->normalizeCycles($cycleFinder->findCycles($declaredGraph));
        $usageCycles = $this->normalizeCycles($cycleFinder->findCycles($usageGraph));

        $usageOnlyCycles = $this->diff($usageCycles, $declaredCycles);

        $response = new CycleDetectionResponseTransfer();
        foreach ($declaredCycles as $cycle) {
            $response->addDeclaredCycle($this->buildCycleTransfer($cycle, static::SOURCE_DECLARED));
        }
        foreach ($usageOnlyCycles as $cycle) {
            $response->addUsageOnlyCycle($this->buildCycleTransfer($cycle, static::SOURCE_USAGE_ONLY));
        }

        return $response;
    }

    /**
     * @param array<array<string>> $cycles
     *
     * @return array<array<string>>
     */
    protected function normalizeCycles(array $cycles): array
    {
        $deduped = [];
        foreach ($cycles as $cycle) {
            $canonical = $this->canonicalize($cycle);
            $deduped[implode('|', $canonical)] = $canonical;
        }

        ksort($deduped);

        return array_values($deduped);
    }

    /**
     * @param array<array<string>> $usageCycles
     * @param array<array<string>> $declaredCycles
     *
     * @return array<array<string>>
     */
    protected function diff(array $usageCycles, array $declaredCycles): array
    {
        $declaredKeys = [];
        foreach ($declaredCycles as $cycle) {
            $declaredKeys[implode('|', $cycle)] = true;
        }

        $result = [];
        foreach ($usageCycles as $cycle) {
            if (isset($declaredKeys[implode('|', $cycle)])) {
                continue;
            }
            $result[] = $cycle;
        }

        return $result;
    }

    /**
     * @param array<string> $cycle
     *
     * @return array<string>
     */
    protected function canonicalize(array $cycle): array
    {
        if ($cycle === []) {
            return $cycle;
        }

        $sorted = $cycle;
        sort($sorted);

        return $sorted;
    }

    /**
     * @param array<string> $cycle
     * @param string $source
     *
     * @return \Generated\Shared\Transfer\CycleTransfer
     */
    protected function buildCycleTransfer(array $cycle, string $source): CycleTransfer
    {
        $cycleTransfer = new CycleTransfer();
        $cycleTransfer->setSource($source);
        $cycleTransfer->setLength(count($cycle));
        foreach ($cycle as $node) {
            $cycleTransfer->addModuleChainEntry($node);
        }

        return $cycleTransfer;
    }
}
