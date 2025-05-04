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

namespace MageStack\Core\Model\OpenSearch\Trait;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use LogicException;

/**
 * Trait for building OpenSearch queries.
 *
 * Class QueryBuilderTrait
 *
 * namespace MageStack\Core\Model\OpenSearch\Trait
 */
trait QueryBuilderTrait
{
    /**
     * @var TimezoneInterface $timezone
     */
    private TimezoneInterface $timezone;

    /**
     * @param TimezoneInterface $timezone
     */
    public function __construct(TimezoneInterface $timezone)
    {
        $this->timezone = $timezone;
    }

    /**
     * Builds an OpenSearch-compatible query array based on provided filters and optional search string.
     *
     * @param array<string, string|array<string, string>> $filters Filter criteria to build the query from.
     * @param string $search  Optional full-text search string.
     *
     * @return array{
     *     must?: list<array<string, mixed>>,
     *     should?: list<array<string, mixed>>,
     *     must_not?: list<array<string, mixed>>,
     *     filter?: list<array<string, mixed>>
     * }
     */
    public function query(array $filters, string $search = ''): array
    {
        $query = ['must' => [], 'should' => [], 'must_not' => [], 'filter' => []];

        foreach ($filters as $field => $value) {
            $clause = $this->buildClause($field, $value);
            if (!$clause) {
                continue;
            }

            [$boolClause, $condition] = $clause;
            if (array_key_exists($boolClause, $query)) {
                $query[$boolClause][] = $condition;
            }
        }

        $searchClause = $this->buildSearchClause($search);
        if ($searchClause) {
            $query['must'][] = $searchClause;
        }

        return array_filter($query);
    }

    /**
     * Builds a query clause for a specific field and value
     *
     * @param string $magentoField The field name to build clause for
     * @param string|array<int|string, string> $value The filter value
     *
     * @return array{0: string, 1: array<string, mixed>}|null Returns [bool_clause, condition] or null if invalid
     */
    private function buildClause(string $magentoField, string|array $value): ?array
    {
        $map = $this->getMap()[$magentoField] ?? null;

        if (!$map || !$value) {
            return null;
        }

        $osField = $map['field'];
        $type = $map['query_type'];
        $boolClause = $map['bool_clause'] ?? 'must';

        $condition = match ($type) {
            'match' => is_string($value) ? ['match' => [$osField => $value]] : null,
            'wildcard' => is_string($value) ? ['wildcard' => ["{$osField}.keyword" => "*{$value}*"]] : null,
            'range' => is_array($value) ? ['range' => [$osField => $this->builDateTimeFilter($value)]] : null,
            'term' => is_string($value) ? ['term' => ["{$osField}.keyword" => $value]] : null,
        };

        return $condition ? [$boolClause, $condition] : null;
    }

    /**
     * Builds a multi-match search clause across all full_text fields
     *
     * @param string $search The search term
     *
     * @return array{multi_match: array{query: string, fields: array<int, string>}}|null
     */
    private function buildSearchClause(string $search): ?array
    {
        if (empty($search)) {
            return null;
        }

        $fields = array_column(
            array_filter(
                $this->getMap(),
                static fn($def) => ($def['data_type'] === 'text' && $def['query_type'] === 'match')
            ),
            'field'
        );

        return $fields ? [
            'multi_match' => [
                'query'  => $search,
                'fields' => $fields,
            ],
        ] : null;
    }

    /**
     * Returns the field mapping configuration
     *
     * @return array<string, array{
     *     field: string,
     *     data_type: 'keyword'|'date'|'text',
     *     query_type: 'term'|'range'|'match'|'wildcard',
     *     bool_clause?: 'filter'|'must'|'must_not'|'should'
     * }> $map
     *
     * @throws \LogicException If the map property is not defined or not an array
     */
    private function getMap(): array
    {
        /**
         * @phpstan-ignore-next-line
         */
        if (!isset($this->map) || !is_array($this->map)) {
            throw new LogicException('The map property must be an array.');
        }

        return $this->map;
    }

    /**
     * Builds a date-time filter for OpenSearch
     *
     * @param array<int|string, string> $value
     *
     * @return array<string, string>
     */
    private function builDateTimeFilter(array $value): array
    {
        $defaults = [
            'from' => [0, 0, 0],
            'to'   => [23, 59, 59],
        ];

        $timeRange = [];

        foreach ($defaults as $key => [$hour, $minute, $second]) {
            if (!isset($value[$key])) {
                continue;
            }

            $date = $this->timezone->date($value[$key]);
            $date->setTime($hour, $minute, $second);
            $formatted = $date->format('Y-m-d\TH:i:s');

            $timeRange[$key === 'from' ? 'gte' : 'lte'] = $formatted;
        }

        return $timeRange;
    }
}
