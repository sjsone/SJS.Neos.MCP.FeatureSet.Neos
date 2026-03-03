<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\FeatureSet\Neos;

use Neos\Flow\Annotations as Flow;
use SJS\Flow\MCP\FeatureSet\AbstractFeatureSet;
use SJS\Neos\MCP\FeatureSet\Neos\FlowFeatureSet\ListConfigurationTreeTool;
use SJS\Neos\MCP\FeatureSet\Neos\FlowFeatureSet\ListPackagesTool;

#[Flow\Scope("singleton")]
class FlowFeatureSet extends AbstractFeatureSet
{
    public function initialize(): void
    {
        $this->addTool(ListConfigurationTreeTool::class);
        $this->addTool(ListPackagesTool::class);
    }
}
