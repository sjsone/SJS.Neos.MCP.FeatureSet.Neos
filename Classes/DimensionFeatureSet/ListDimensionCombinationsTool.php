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

class ListDimensionCombinationsTool extends Tool
{
    #[Flow\Inject]
    protected ContentRepositoryRegistry $contentRepositoryRegistry;

    public function __construct()
    {
        parent::__construct(
            name: 'list_dimension_combinations',
            description: 'Lists all valid dimension space points (coordinate sets) that can be used to address content nodes in the content repository',
            inputSchema: new ObjectSchema(),
            annotations: new Annotations(
                title: 'List Dimension Combinations',
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

        $variationGraph = $contentRepository->getVariationGraph();

        $combinations = [];
        foreach ($variationGraph->getDimensionSpacePoints() as $point) {
            $combinations[$point->hash] = $point->coordinates;
        }

        return Content::structuredWithFallback($combinations);
    }
}
