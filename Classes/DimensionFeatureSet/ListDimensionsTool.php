<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\FeatureSet\Neos\DimensionFeatureSet;

use Neos\ContentRepositoryRegistry\ContentRepositoryRegistry;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\ActionRequest;
use Neos\Neos\FrontendRouting\SiteDetection\SiteDetectionResult;
use SJS\Flow\MCP\Domain\MCP\Tool;
use SJS\Flow\MCP\Domain\MCP\Tool\Annotations;
use SJS\Flow\MCP\Domain\MCP\Tool\Content;
use SJS\Flow\MCP\JsonSchema\ObjectSchema;

class ListDimensionsTool extends Tool
{
    #[Flow\Inject]
    protected ContentRepositoryRegistry $contentRepositoryRegistry;

    public function __construct()
    {
        parent::__construct(
            name: 'list_dimensions',
            description: 'Lists all content dimensions and their values configured in the content repository',
            inputSchema: new ObjectSchema(),
            annotations: new Annotations(
                title: 'List Dimensions',
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
        $contentRepository = $this->contentRepositoryRegistry->get($siteDetection->contentRepositoryId);

        $dimensionSource = $contentRepository->getContentDimensionSource();

        $dimensions = [];
        foreach ($dimensionSource->getContentDimensionsOrderedByPriority() as $dimension) {
            $values = [];

            foreach ($dimension->values as $value) {
                $values[] = [
                    'value' => $value->value,
                    'depth' => $value->specializationDepth->value,
                    'configuration' => $value->configuration,
                ];
            }
            $dimensions[(string) $dimension->id->value] = [
                'values' => $values,
            ];
        }

        return Content::structuredWithFallback($dimensions);
    }
}
