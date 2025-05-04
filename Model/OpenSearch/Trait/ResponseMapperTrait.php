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

trait ResponseMapperTrait
{
    /**
     * Maps Opensearch response to a standardized format
     *
     * @param array{
     *     took?: int,
     *     timed_out?: bool,
     *     _shards?: array{total: int, successful: int, skipped: int, failed: int},
     *     hits: array{
     *         total: array{value: int, relation: string},
     *         max_score: ?float,
     *         hits: array<int, array{
     *             _index: string,
     *             _id: string,
     *             _score: ?float,
     *             _source?: array<string, mixed>,
     *             sort?: array<int|string, mixed>
     *         }>
     *     }
     * }|array{} $response An Opensearch response array
     *
     * @return array{totalRecords: int, items: array<int, array<string, mixed>>}
     */
    public function map(array $response): array
    {
        $totalRecords = 0;
        $items = [];
        if (!empty($response)) {
            $hits = $response['hits']['hits'];
            $totalRecords = $response['hits']['total']['value'];

            /**
             * @var array<int|string, mixed> $hit
             */
            foreach ($hits as $hit) {
                $item = ['entity_id' => $hit['_id']];
                $source = (isset($hit['_source']) && is_array($hit['_source'])) ? $hit['_source'] : [];

                foreach ($this->map as $magentoField => $def) {
                    $item[$magentoField] = $this->getValue($source, $def['field']);
                }

                $items[] = $item;
            }
        }


        return ['totalRecords' => $totalRecords, 'items' => $items];
    }

    /**
     * Gets a value from a nested array by dot notation
     *
     * @param array<string, mixed> $array   The source array to extract value from
     * @param string               $key     The dot notation key path (e.g. 'user.address.city')
     * @param mixed                $default The default value to return if key doesn't exist
     *
     * @return mixed The value found at the specified path or default if not found
     */
    private function getValue(array $array, string $key, mixed $default = ''): mixed
    {
        foreach (explode('.', $key) as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return $default;
            }
            $array = $array[$segment];
        }

        return $array;
    }
}
