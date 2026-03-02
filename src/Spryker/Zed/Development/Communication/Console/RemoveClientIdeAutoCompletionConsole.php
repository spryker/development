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
class RemoveClientIdeAutoCompletionConsole extends Console
{
    /**
     * @var string
     */
    public const COMMAND_NAME = 'dev:ide-auto-completion:client:remove';

    protected function configure(): void
    {
        parent::configure();

        $this->setName(static::COMMAND_NAME);
        $this->setDescription('Removes IDE auto completion files for Client.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->getFacade()->removeClientIdeAutoCompletion();

        $this->info('Removed Client IDE auto-completion files');

        return static::CODE_SUCCESS;
    }
}
