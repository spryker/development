<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Development\Communication\Console;

use Generated\Shared\Transfer\CycleDetectionRequestTransfer;
use Generated\Shared\Transfer\CycleDetectionResponseTransfer;
use Generated\Shared\Transfer\CycleTransfer;
use Spryker\Zed\Development\Business\Dependency\Cycle\CycleDetector;
use Spryker\Zed\Kernel\Communication\Console\Console;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 *
 * @method \Spryker\Zed\Development\Business\DevelopmentFacadeInterface getFacade()
 * @method \Spryker\Zed\Development\Communication\DevelopmentCommunicationFactory getFactory()
 */
class DependencyCycleFinderConsole extends Console
{
    /**
     * @var string
     */
    public const COMMAND_NAME = 'dev:dependency:find-cycles';

    /**
     * @var string
     */
    public const OPTION_DEEP_CYCLES = 'deep-cycles';

    /**
     * @var string
     */
    public const OPTION_INCLUDE_EXTENSIONS = 'include-extensions';

    /**
     * @var string
     */
    public const OPTION_INCLUDE_DEV = 'include-dev';

    /**
     * @var string
     */
    public const OPTION_FAIL_ON = 'fail-on';

    /**
     * @var string
     */
    protected const ARROW = ' → ';

    /**
     * @var array<string>
     */
    protected const VALID_FAIL_ON_VALUES = [
        CycleDetector::FAIL_ON_DECLARED,
        CycleDetector::FAIL_ON_ANY,
        CycleDetector::FAIL_ON_NONE,
    ];

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName(static::COMMAND_NAME)
            ->addOption(
                static::OPTION_DEEP_CYCLES,
                null,
                InputOption::VALUE_NONE,
                'Detect cycles of any length (Tarjan SCC). Default is direct A↔B cycles only.',
            )
            ->addOption(
                static::OPTION_INCLUDE_EXTENSIONS,
                null,
                InputOption::VALUE_NONE,
                'Include cycles that only exist between a module and its own *-extension module.',
            )
            ->addOption(
                static::OPTION_INCLUDE_DEV,
                null,
                InputOption::VALUE_NONE,
                'Include require-dev edges in the declared graph.',
            )
            ->addOption(
                static::OPTION_FAIL_ON,
                null,
                InputOption::VALUE_REQUIRED,
                'Exit-code policy: "declared" (fail on declared cycles), "any" (default, fail on any cycle), "none" (never fail).',
                CycleDetector::FAIL_ON_ANY,
            )
            ->setDescription('Detect module-level circular dependencies in declared and usage graphs.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $failOn = (string)$input->getOption(static::OPTION_FAIL_ON);
        if (!in_array($failOn, static::VALID_FAIL_ON_VALUES, true)) {
            $this->error(sprintf(
                'Invalid --%s value "%s". Allowed: %s',
                static::OPTION_FAIL_ON,
                $failOn,
                implode(', ', static::VALID_FAIL_ON_VALUES),
            ));

            return static::CODE_ERROR;
        }

        $cycleDetectionRequestTransfer = new CycleDetectionRequestTransfer();
        $cycleDetectionRequestTransfer->setIsDeep((bool)$input->getOption(static::OPTION_DEEP_CYCLES));
        $cycleDetectionRequestTransfer->setIncludeExtensions((bool)$input->getOption(static::OPTION_INCLUDE_EXTENSIONS));
        $cycleDetectionRequestTransfer->setIncludeRequireDev((bool)$input->getOption(static::OPTION_INCLUDE_DEV));
        $cycleDetectionRequestTransfer->setFailOn($failOn);

        if ($output->isVerbose()) {
            $this->info('Building declared and usage dependency graphs — this can take a moment.');
        }

        $cycleDetectionResponseTransfer = $this->getFacade()->findModuleDependencyCycles($cycleDetectionRequestTransfer);

        $this->renderResponse($output, $cycleDetectionResponseTransfer);

        return $this->resolveExitCode($cycleDetectionResponseTransfer, $failOn);
    }

    protected function renderResponse(OutputInterface $output, CycleDetectionResponseTransfer $cycleDetectionResponseTransfer): void
    {
        $this->renderSection(
            $output,
            'Declared Cycles',
            $cycleDetectionResponseTransfer->getDeclaredCycles(),
            'red',
        );

        $this->renderSection(
            $output,
            'Usage-only Cycles',
            $cycleDetectionResponseTransfer->getUsageOnlyCycles(),
            'yellow',
        );
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param string $title
     * @param iterable<\Generated\Shared\Transfer\CycleTransfer> $cycleTransfers
     * @param string $color
     *
     * @return void
     */
    protected function renderSection(OutputInterface $output, string $title, iterable $cycleTransfers, string $color): void
    {
        $rows = [];
        foreach ($cycleTransfers as $cycleTransfer) {
            $rows[] = $this->buildCycleRow($cycleTransfer, $color);
        }

        $table = new Table($output);
        $table->setHeaders([
            new TableCell(sprintf('<fg=%s>%s</> (%d)', $color, $title, count($rows)), ['colspan' => 3]),
        ]);

        if ($rows === []) {
            $table->addRow([new TableCell('<fg=green>No cycles detected.</>', ['colspan' => 3])]);
            $table->render();
            $output->writeln('');

            return;
        }

        $table->addRow(['Cycle', 'Length', 'Source']);
        foreach ($rows as $row) {
            $table->addRow($row);
        }

        $table->render();
        $output->writeln('');
    }

    /**
     * @param \Generated\Shared\Transfer\CycleTransfer $cycleTransfer
     * @param string $color
     *
     * @return array<string>
     */
    protected function buildCycleRow(CycleTransfer $cycleTransfer, string $color): array
    {
        $chain = (array)$cycleTransfer->getModuleChain();

        return [
            sprintf('<fg=%s>%s</>', $color, implode(static::ARROW, $chain)),
            (string)$cycleTransfer->getLength(),
            (string)$cycleTransfer->getSource(),
        ];
    }

    protected function resolveExitCode(CycleDetectionResponseTransfer $cycleDetectionResponseTransfer, string $failOn): int
    {
        if ($failOn === CycleDetector::FAIL_ON_NONE) {
            return static::CODE_SUCCESS;
        }

        $declaredCount = count($cycleDetectionResponseTransfer->getDeclaredCycles());
        if ($failOn === CycleDetector::FAIL_ON_DECLARED) {
            return $declaredCount > 0 ? static::CODE_ERROR : static::CODE_SUCCESS;
        }

        $usageOnlyCount = count($cycleDetectionResponseTransfer->getUsageOnlyCycles());

        return ($declaredCount + $usageOnlyCount) > 0 ? static::CODE_ERROR : static::CODE_SUCCESS;
    }
}
