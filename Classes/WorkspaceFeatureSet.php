<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\FeatureSet\Neos;

use SJS\Neos\MCP\FeatureSet\AbstractFeatureSet;
use SJS\Neos\MCP\FeatureSet\Neos\WorkspaceFeatureSet\CreateWorkspaceTool;
use SJS\Neos\MCP\FeatureSet\Neos\WorkspaceFeatureSet\DeleteWorkspaceTool;
use SJS\Neos\MCP\FeatureSet\Neos\WorkspaceFeatureSet\ListWorkspacesTool;
use SJS\Neos\MCP\FeatureSet\Neos\WorkspaceFeatureSet\ListWorkspaceChangesTool;
use SJS\Neos\MCP\FeatureSet\Neos\WorkspaceFeatureSet\PublishWorkspaceTool;

class WorkspaceFeatureSet extends AbstractFeatureSet
{
    public function initialize(): void
    {
        // TODO: refactor tools (like in SJS.Neos.MCP.FeatureSet.CR with traits and maybe an abstract workspace tool class)
        $this->addTool(ListWorkspacesTool::class);
        $this->addTool(ListWorkspaceChangesTool::class);
        $this->addTool(CreateWorkspaceTool::class);
        $this->addTool(DeleteWorkspaceTool::class);
        $this->addTool(PublishWorkspaceTool::class);
    }
}
