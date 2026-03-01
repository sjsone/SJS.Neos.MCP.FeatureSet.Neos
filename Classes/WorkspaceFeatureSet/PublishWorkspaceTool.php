<?php
declare(strict_types=1);

namespace SJS\Neos\MCP\FeatureSet\Neos\WorkspaceFeatureSet;

use Neos\ContentRepository\Core\Feature\WorkspacePublication\Command\PublishWorkspace;
use Neos\ContentRepository\Core\SharedModel\Workspace\WorkspaceName;
use Neos\ContentRepositoryRegistry\ContentRepositoryRegistry;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\ActionRequest;
use Neos\Neos\FrontendRouting\SiteDetection\SiteDetectionResult;
use SJS\Neos\MCP\Domain\MCP\Tool;
use SJS\Neos\MCP\Domain\MCP\Tool\Annotations;
use SJS\Neos\MCP\Domain\MCP\Tool\Content;
use SJS\Neos\MCP\JsonSchema\ObjectSchema;
use SJS\Neos\MCP\JsonSchema\StringSchema;

class PublishWorkspaceTool extends Tool
{
    #[Flow\Inject]
    protected ContentRepositoryRegistry $contentRepositoryRegistry;

    public function __construct()
    {
        parent::__construct(
            name: 'publish_workspace',
            description: 'Publishes all changes in a workspace to its base workspace (e.g. live). Be aware of eventual consistency so let some seconds pass before reading again.',
            inputSchema: new ObjectSchema(properties: [
                'name' => (new StringSchema(description: "technical name of the workspace to publish"))->required(),
            ]),
            annotations: new Annotations(
                title: 'Publish Workspace'
            )
        );
    }

    public function run(ActionRequest $actionRequest, array $input): Content
    {
        $workspaceName = $input['name'];

        $siteDetection = SiteDetectionResult::fromRequest($actionRequest->getHttpRequest());
        $contentRepository = $this->contentRepositoryRegistry->get($siteDetection->contentRepositoryId);

        $command = PublishWorkspace::create(WorkspaceName::fromString($workspaceName));
        $contentRepository->handle(command: $command);

        return Content::text("Workspace '{$workspaceName}' published successfully.");
    }
}
