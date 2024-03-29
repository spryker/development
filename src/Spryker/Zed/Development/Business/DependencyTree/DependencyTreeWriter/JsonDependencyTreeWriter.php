<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Development\Business\DependencyTree\DependencyTreeWriter;

use Symfony\Component\Filesystem\Filesystem;

class JsonDependencyTreeWriter implements DependencyTreeWriterInterface
{
    /**
     * @var string
     */
    protected $pathToFile;

    /**
     * @param string $pathToFile
     */
    public function __construct($pathToFile)
    {
        $this->pathToFile = $pathToFile;
    }

    /**
     * @param array $dependencyTree
     *
     * @return void
     */
    public function write(array $dependencyTree)
    {
        $filesystem = new Filesystem();
        /** @var string $content */
        $content = json_encode($dependencyTree, JSON_PRETTY_PRINT);
        $filesystem->dumpFile($this->pathToFile, $content);
    }
}
