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

trait FieldMapResolverTrait
{
    /**
     * Gets all unique Opensearch field names from the mapping
     *
     * @return array<int, string> List of unique Opensearch field names
     */
    public function getOSFields(): array
    {
        return array_unique(
            array_column(
                array_filter($this->map, static fn($def) => is_array($def)),
                'field'
            )
        );
    }

    /**
     * Gets all Magento field names from the mapping
     *
     * @return array<int, string> List of Magento field names (keys from the map)
     */
    public function getMagentoFields(): array
    {
        return array_keys($this->map);
    }

    /**
     * Gets the corresponding Opensearch field for a Magento field
     *
     * @param string $magentoField The Magento field name
     *
     * @return string|null The corresponding Opensearch field name or null if not found
     */
    public function getOSFieldByMagentoField(string $magentoField): ?string
    {
        if (!isset($this->map[$magentoField])) {
            return null;
        }

        return $this->map[$magentoField]['field'];
    }
}
