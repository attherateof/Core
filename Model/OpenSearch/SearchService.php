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

use MageStack\Core\Model\OpenSearch\Trait\FieldMapResolverTrait;
use MageStack\Core\Model\OpenSearch\Trait\QueryBuilderTrait;
use MageStack\Core\Model\OpenSearch\Trait\ResponseMapperTrait;
use MageStack\Core\Model\OpenSearch\Trait\SortBuilderTrait;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Psr\Log\LoggerInterface;

/**
 * Class SearchService
 *
 * This class is responsible for executing search queries against an OpenSearch index.
 * It handles the construction of the query, execution, and mapping of the response.
 */
class SearchService
{
    use FieldMapResolverTrait;
    use ResponseMapperTrait;
    use SortBuilderTrait;
    use QueryBuilderTrait {
        QueryBuilderTrait::__construct as private __QueryBuilderConstruct;
    }

    /**
     * Default values for pagination and sorting
     */
    private const DEFAULT_PAGE_SIZE = 20;
    private const DEFAULT_SORT_FIELD = 'timestamp';
    private const DEFAULT_SORT_DIRECTION = 'desc';

    /**
     * Constructor
     *
     * @param Client $searchClient Opensearch client
     * @param TimezoneInterface $timezoneInterface Timezone interface for date handling
     * @param LoggerInterface|null $logger Optional logger for error handling
     * @param string $index Opensearch index name
     * @param array<string, array{
     *     field: string,
     *     data_type: 'keyword'|'date'|'text',
     *     query_type: 'term'|'range'|'match'|'wildcard',
     *     bool_clause?: 'filter'|'must'|'must_not'|'should'
     * }> $map Field mapping configuration
     */
    public function __construct(
        private readonly Client $searchClient,
        public readonly TimezoneInterface $timezoneInterface,
        private readonly ?LoggerInterface $logger = null,
        private readonly string $index = '', // Index should be passed or configured
        private readonly array $map = []    // Map should be passed or configured
    ) {
        $this->__QueryBuilderConstruct($timezoneInterface);
    }

    /**
     * Executes a search query based on provided parameters
     *
     * @param array{
     *     search?: string,
     *     filters?: array<string, mixed>,
     *     paging?: array{
     *         pageSize?: int,
     *         current?: int
     *     },
     *     sorting?: array{
     *         field?: string,
     *         direction?: 'asc'|'desc'
     *     }
     * } $parameters Search parameters
     *
     * @return array{
     *     totalRecords: int,
     *     items: array<int, array<string, mixed>>
     * } Normalized search results
     */
    public function search(array $parameters = []): array
    {
        try {
            $request = $this->normalizeRequest($parameters);
            $params = $this->buildSearchParams($request);
            $response = $this->searchClient->getInstance()->search($params);
        } catch (\Exception $e) {
            $this->logger?->error('Error executing OpenSearch query: ' . $e->getMessage(), ['params' => $params]);
            $response = [];
        }

        return $this->map($response);
    }

    /**
     * Normalizes the request parameters with defaults
     *
     * @param array<string, mixed> $parameters Raw parameters
     *
     * @return array{
     *  search: string,
     *  filters: array<string, string|array<string, string>>,
     *  paging: array{pageSize?: int, current?: int},
     *  sorting: array<int|string, array<int|string, string>|string>
     * } Normalized parameters with defaults applied
     */
    private function normalizeRequest(array $parameters): array
    {
        $defaults = [
            'search'  => '',
            'filters' => [],
            'paging'  => [
                'pageSize' => self::DEFAULT_PAGE_SIZE,
                'current' => 1
            ],
            'sorting' => [
                'field' => self::DEFAULT_SORT_FIELD,
                'direction' => self::DEFAULT_SORT_DIRECTION
            ],
        ];

        $result = array_merge($defaults, array_intersect_key($parameters, $defaults));

        // Make sure paging has the correct structure
        if (isset($parameters['paging']) && is_array($parameters['paging'])) {
            $result['paging'] = array_merge(
                $defaults['paging'],
                array_intersect_key(
                    $parameters['paging'],
                    $defaults['paging']
                )
            );
        }

        // Make sure sorting has the correct structure
        if (isset($parameters['sorting']) && is_array($parameters['sorting'])) {
            $result['sorting'] = array_merge(
                $defaults['sorting'],
                array_intersect_key(
                    $parameters['sorting'],
                    $defaults['sorting']
                )
            );
        }

        // Ensure search is a string
        $result['search'] = is_string($result['search']) ? $result['search'] : '';

        // Ensure filters is an array
        $result['filters'] = is_array($result['filters']) ? $result['filters'] : [];

        /**
         * @var array{
         *  search: string,
         *  filters: array<string, string|array<string, string>>,
         *  paging: array{pageSize?: int, current?: int},
         *  sorting: array<int|string, array<int|string, string>|string>
         * }
         */
        return $result;
    }

    /**
     * Builds Opensearch query parameters from normalized request
     *
     * @param array{
     *  search: string,
     *  filters: array<string, string|array<string, string>>,
     *  paging: array{pageSize?: int, current?: int},
     *  sorting: array<int|string, array<int|string, string>|string>
     * } $request Normalized request
     *
     * @return array{
     *      index: string,
     *      body: array{
     *          _source: array<int, string>,
     *          from: int,
     *          size: int,
     *          sort: non-empty-array<int|string, mixed>,
     *          query?: array{
     *              bool: array{
     *                  must?: array<int, array<string, mixed>>,
     *                  should?: array<int, array<string, mixed>>,
     *                  must_not?: array<int, array<string, mixed>>,
     *                  filter?: array<int, array<string, mixed>>
     *              }   &non-empty-array
     *          }
     *      }
     * } Opensearch query parameters
     */
    private function buildSearchParams(array $request): array
    {
        $filters = $request['filters'];
        $paging = $request['paging'];
        $sorting = $request['sorting'];
        $search = $request['search'];

        $from = $this->calculateFrom($paging);
        $size = $paging['pageSize'] ?? self::DEFAULT_PAGE_SIZE;

        $sort = $this->sort($sorting);
        if (empty($sort)) {
            $sort[] = [
                self::DEFAULT_SORT_FIELD => [
                    'order' => self::DEFAULT_SORT_DIRECTION
                ]
            ];
        }

        /**
         * @var string $search
         */
        $query = $this->query($filters, $search);
        $sourceFields = $this->getOSFields();

        $body = [
            '_source' => $sourceFields,
            'from' => $from,
            'size' => $size,
            'sort' => $sort,
        ];

        if (!empty($query)) {
            $body['query'] = ['bool' => $query];
        }

        return [
            'index' => $this->index,
            'body' => $body,
        ];
    }

    /**
     * Calculates the "from" parameter for pagination
     *
     * @param array{pageSize?: int, current?: int} $paging Pagination parameters
     *
     * @return int The offset value for the search query
     */
    private function calculateFrom(array $paging): int
    {
        $page = max(1, (int)($paging['current'] ?? 1));
        $pageSize = (int)($paging['pageSize'] ?? self::DEFAULT_PAGE_SIZE);
        return ($page - 1) * $pageSize;
    }
}
