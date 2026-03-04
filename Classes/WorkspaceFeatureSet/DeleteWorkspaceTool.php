<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\FeatureSet\Neos\WorkspaceFeatureSet;

use Neos\ContentRepository\Core\SharedModel\Workspace\WorkspaceName;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\ActionRequest;
use Neos\Neos\Domain\Service\WorkspaceService;
use Neos\Neos\FrontendRouting\SiteDetection\SiteDetectionResult;
use Psr\Log\LoggerInterface;
use SJS\Flow\MCP\Domain\MCP\Tool;
use SJS\Flow\MCP\Domain\MCP\Tool\Annotations;
use SJS\Flow\MCP\JsonSchema\ObjectSchema;
use SJS\Flow\MCP\JsonSchema\StringSchema;


class DeleteWorkspaceTool extends Tool
{
    #[Flow\Inject]
    protected WorkspaceService $workspaceService;

    #[Flow\Inject]
    protected LoggerInterface $logger;

    public function __construct()
    {
        parent::__construct(
            name: 'delete_workspace',
            description: 'Delete a workspace',
            inputSchema: new ObjectSchema(properties: [
                'name' => (new StringSchema(description: "technical name of the workspace to be deleted"))->required(),
            ]),
            annotations: new Annotations(
                title: 'Delete Workspace',
                destructiveHint: true
            )
        );
    }

    /**
     * @param array<string,mixed> $input
     * @return array{message: string, status: string}
     */
    public function run(ActionRequest $actionRequest, array $input)
    {
        $workspaceName = $this->retrieveName($input);

        $siteDetection = SiteDetectionResult::fromRequest($actionRequest->getHttpRequest());

        $this->workspaceService->deleteWorkspace(
            $siteDetection->contentRepositoryId,
            WorkspaceName::fromString($workspaceName)
        );

        return [
            'status' => 'success',
            'message' => "Workspace '{$workspaceName}' deleted successfully",
        ];
    }

    /**
     * @param array<string,mixed> $input
     */
    public function retrieveName(array $input): string
    {
        $name = $input["name"];
        return $name;
    }
}
