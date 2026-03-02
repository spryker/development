<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Development\Business\Dependency\DependencyFinder;

use Spryker\Zed\Development\Business\Dependency\DependencyContainer\DependencyContainerInterface;
use Spryker\Zed\Development\Business\Dependency\DependencyFinder\Context\DependencyFinderContextInterface;
use Spryker\Zed\Development\Business\Dependency\SchemaParser\PropelSchemaParserInterface;

class PersistenceDependencyFinder implements DependencyFinderInterface
{
    /**
     * @var string
     */
    public const TYPE_PERSISTENCE = 'persistence';

    /**
     * @var \Spryker\Zed\Development\Business\Dependency\SchemaParser\PropelSchemaParserInterface
     */
    protected $propelSchemaParser;

    public function __construct(PropelSchemaParserInterface $propelSchemaParser)
    {
        $this->propelSchemaParser = $propelSchemaParser;
    }

    public function getType(): string
    {
        return static::TYPE_PERSISTENCE;
    }

    public function accept(DependencyFinderContextInterface $context): bool
    {
        if ($context->getDependencyType() !== null && $context->getDependencyType() !== $this->getType()) {
            return false;
        }

        if (substr($context->getFileInfo()->getFilename(), -10) !== 'schema.xml' || strpos($context->getFileInfo()->getFilename(), 'spy_') !== 0) {
            return false;
        }

        return true;
    }

    public function findDependencies(DependencyFinderContextInterface $context, DependencyContainerInterface $dependencyContainer): DependencyContainerInterface
    {
        $foreignIdColumnNames = $this->propelSchemaParser->getForeignColumnNames($context->getFileInfo());

        foreach ($foreignIdColumnNames as $foreignIdColumnName) {
            $dependentModule = $this->propelSchemaParser->getModuleNameByForeignReference($foreignIdColumnName, $context->getModule()->getName());
            $dependencyContainer->addDependency(
                $dependentModule,
                $this->getType(),
            );
        }

        return $dependencyContainer;
    }
}
