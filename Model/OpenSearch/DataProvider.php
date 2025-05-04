<?php

/**
 * Copyright © 2025 MageStack. All rights reserved.
 * See COPYING.txt for license details.
 *
 * DISCLAIMER
 *
 * Do not make any kind of changes to this file if you
 * wish to upgrade this extension to newer version in the future.
 *
 * @category  MageStack
 * @package   MageStack_Core
 * @author    Amit Biswas <amit.biswas.webdeveloper@gmail.com>
 * @copyright 2025 MageStack
 * @license   https://opensource.org/licenses/MIT  MIT License
 */

declare(strict_types=1);

namespace MageStack\Core\Model\OpenSearch;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider as CoreDataProvider;

/**
 * DataProvider class for OpenSearch.
 *
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 */
class DataProvider extends CoreDataProvider
{
    /**
     * Constructor
     *
     * @param SearchService $searchService
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ReportingInterface $reporting
     * @param SearchCriteriaBuilder $criteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param string $index
     * @param array<int|string, mixed> $map
     * @param array<int|string, mixed> $meta
     * @param array<int|string, mixed> $data
     */
    public function __construct(
        private readonly SearchService $searchService,
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $criteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        public readonly string $index = '',
        public readonly array $map = [],
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $criteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
    }

    /**
     * Get data for the grid.
     *
     * @return array<int|string, mixed>
     */
    public function getData(): array
    {
        $params = $this->request->getParams();

        return $this->searchService->search($params);
    }
}
