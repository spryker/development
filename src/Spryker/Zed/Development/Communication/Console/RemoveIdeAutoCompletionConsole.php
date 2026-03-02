<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Development\Communication\Console;

use Spryker\Zed\Kernel\Communication\Console\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method \Spryker\Zed\Development\Business\DevelopmentFacadeInterface getFacade()
 * @method \Spryker\Zed\Development\Communication\DevelopmentCommunicationFactory getFactory()
 */
class RemoveIdeAutoCompletionConsole extends Console
{
    /**
     * @var string
     */
    public const COMMAND_NAME = 'dev:ide-auto-completion:remove';

    protected function configure(): void
    {
        parent::configure();

        $this->setName(static::COMMAND_NAME);
        $this->setDescription('Removes IDE auto completion files.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $dependingCommands = [
            RemoveYvesIdeAutoCompletionConsole::COMMAND_NAME,
            RemoveZedIdeAutoCompletionConsole::COMMAND_NAME,
            RemoveClientIdeAutoCompletionConsole::COMMAND_NAME,
            RemoveServiceIdeAutoCompletionConsole::COMMAND_NAME,
            RemoveGlueIdeAutoCompletionConsole::COMMAND_NAME,
            RemoveGlueBackendIdeAutoCompletionConsole::COMMAND_NAME,
        ];

        foreach ($dependingCommands as $commandName) {
            if (!$this->getApplication()->has($commandName)) {
                $this->showCommandNotFoundMessage($commandName);

                continue;
            }
            $this->runDependingCommand($commandName);

            if ($this->hasError()) {
                return $this->getLastExitCode();
            }
        }

        return $this->getLastExitCode();
    }

    protected function showCommandNotFoundMessage(string $commandName): void
    {
        $this->output->writeln(sprintf('<comment>Can not find %s in your project.</comment>', $commandName));
        $this->output->writeln('You can fix this by adding the missing command to your project ConsoleDependencyProvider.');
    }
}
