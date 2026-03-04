<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\FeatureSet\Neos\WorkspaceFeatureSet;

use Neos\ContentRepositoryRegistry\ContentRepositoryRegistry;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\ActionRequest;
use Neos\Neos\Domain\Repository\WorkspaceMetadataAndRoleRepository;
use Neos\Neos\FrontendRouting\SiteDetection\SiteDetectionResult;
use SJS\Flow\MCP\Domain\MCP\Tool;
use SJS\Flow\MCP\Domain\MCP\Tool\Annotations;
use SJS\Flow\MCP\Domain\MCP\Tool\Content;
use SJS\Flow\MCP\JsonSchema\ObjectSchema;


class ListWorkspacesTool extends Tool
{
    #[Flow\Inject]
    protected ContentRepositoryRegistry $contentRepositoryRegistry;

    #[Flow\Inject]
    protected WorkspaceMetadataAndRoleRepository $workspaceMetadataAndRoleRepository;

    public function __construct()
    {
        parent::__construct(
            name: 'list_workspaces',
            description: 'Lists all workspaces with metadata',
            inputSchema: new ObjectSchema(),
            annotations: new Annotations(
                title: 'List Workspaces',
                readOnlyHint: true
            )
        );
    }

    /**
     * @param array<string,mixed> $input
     */
    public function run(ActionRequest $actionRequest, array $input): Content
    {
        $siteDetection = SiteDetectionResult::fromRequest($actionRequest->getHttpRequest());
        $cr = $this->contentRepositoryRegistry->get($siteDetection->contentRepositoryId);

        $workspaces = [];
        foreach ($cr->findWorkspaces() as $workspace) {
            $workspaceMetadata = $this->workspaceMetadataAndRoleRepository->loadWorkspaceMetadata(
                $siteDetection->contentRepositoryId,
                $workspace->workspaceName
            );

            if ($workspaceMetadata === null) {
                continue;
            }

            $workspaces[(string) $workspace->workspaceName] = [
                'title' => $workspaceMetadata->title->value,
                'description' => $workspaceMetadata->description->value,
                'classification' => $workspaceMetadata->classification->value,
                'ownerUserId' => $workspaceMetadata->ownerUserId,
            ];
        }

        return Content::structuredWithFallback($workspaces);
    }
}
