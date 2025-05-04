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

use LogicException;

trait SortBuilderTrait
{
    /**
     * Sort the given array of sorting fields.
     *
     * @param array<int|string, string|array<int|string, string>> $sorting
     *
     * @return array<int|string, mixed>
     */
    public function sort(array $sorting): array
    {
        if (!method_exists($this, 'getOSFieldByMagentoField')) {
            throw new LogicException('getOSFieldByMagentoField is required by trait FieldMapResolverTrait');
        }

        $formatted = [];

        if (empty($sorting)) {
            return $formatted;
        }

        $sorting = isset($sorting['field']) ? [$sorting] : $sorting;

        foreach ($sorting as $sortItem) {
            /**
             * @var array<int|string, string> $sortItem
             */
            if (isset($sortItem['field'], $sortItem['direction'])) {
                $field = $this->getOSFieldByMagentoField($sortItem['field']);
                $formatted[] = [
                    $field => [
                        'order' => strtolower($sortItem['direction'])
                    ]
                ];
            }
        }

        return $formatted;
    }
}
