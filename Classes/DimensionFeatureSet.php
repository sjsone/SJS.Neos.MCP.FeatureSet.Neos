<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\FeatureSet\Neos;

use SJS\Flow\MCP\FeatureSet\AbstractFeatureSet;
use SJS\Neos\MCP\FeatureSet\Neos\DimensionFeatureSet\ListDimensionCombinationsTool;
use SJS\Neos\MCP\FeatureSet\Neos\DimensionFeatureSet\ListDimensionsTool;

class DimensionFeatureSet extends AbstractFeatureSet
{
    public function initialize(): void
    {
        $this->addTool(ListDimensionsTool::class);
        $this->addTool(ListDimensionCombinationsTool::class);
    }
}
