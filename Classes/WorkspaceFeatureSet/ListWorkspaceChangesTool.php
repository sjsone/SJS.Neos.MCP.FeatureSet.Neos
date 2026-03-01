<?php
declare(strict_types=1);

namespace SJS\Neos\MCP\FeatureSet\Neos\WorkspaceFeatureSet;

use Neos\ContentRepository\Core\SharedModel\Workspace\WorkspaceName;
use Neos\ContentRepositoryRegistry\ContentRepositoryRegistry;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\ActionRequest;
use Neos\Neos\Domain\Service\WorkspacePublishingService;
use Neos\Neos\FrontendRouting\SiteDetection\SiteDetectionResult;
use SJS\Neos\MCP\Domain\MCP\Tool;
use SJS\Neos\MCP\Domain\MCP\Tool\Annotations;
use SJS\Neos\MCP\Domain\MCP\Tool\Content;
use SJS\Neos\MCP\JsonSchema\ObjectSchema;
use SJS\Neos\MCP\JsonSchema\StringSchema;

class ListWorkspaceChangesTool extends Tool
{
    #[Flow\Inject]
    protected ContentRepositoryRegistry $contentRepositoryRegistry;

    #[Flow\Inject]
    protected WorkspacePublishingService $workspacePublishingService;

    public function __construct()
    {
        parent::__construct(
            name: 'list_workspace_changes',
            description: 'Lists all pending changes in a workspace relative to its base workspace (e.g. live). Returns each changed node with its type and kind of change.',
            inputSchema: new ObjectSchema(properties: [
                'name' => (new StringSchema(description: "technical name of the workspace"))->required(),
            ]),
            annotations: new Annotations(
                title: 'List Workspace Changes',
                readOnlyHint: true
            )
        );
    }

    public function run(ActionRequest $actionRequest, array $input): Content
    {
        // TODO: The Neos\Workspace\Ui\Controller\WorkspaceController does what should be done here as well. Generalize the code ideally
        $workspaceName = WorkspaceName::fromString($input['name']);

        $siteDetection = SiteDetectionResult::fromRequest($actionRequest->getHttpRequest());
        $contentRepository = $this->contentRepositoryRegistry->get($siteDetection->contentRepositoryId);
        $contentGraph = $contentRepository->getContentGraph($workspaceName);

        $changes = $this->workspacePublishingService->pendingWorkspaceChanges(
            $siteDetection->contentRepositoryId,
            $workspaceName
        );

        $result = [];
        foreach ($changes as $change) {
            $changeType = match (true) {
                $change->created => 'created',
                $change->changed => 'changed',
                $change->moved => 'moved',
                $change->deleted => 'deleted',
            };

            $nodeAggregate = $contentGraph->findNodeAggregateById($change->nodeAggregateId);
            if ($nodeAggregate === null) {
                // TODO: contemplate what should be done if there is a change but the node cannot be found
                continue;
            }

            $nodeTypeName = $nodeAggregate->nodeTypeName;

            $nodeAggregateId = $change->nodeAggregateId;
            $result[(string) $nodeAggregateId] = [
                'nodeAggregateId' => $nodeAggregateId,
                'nodeType' => $nodeTypeName,
                'changeType' => $changeType,
                'dimension' => $change->originDimensionSpacePoint?->toJson(),
            ];
        }

        return Content::structured($result)
            ->addText(json_encode($result));
    }
}
