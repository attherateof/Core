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
 * @link      https://github.com/attherateof/Core
 */

declare(strict_types=1);

namespace MageStack\Core\Api\OpenSearch;

/**
 * Opensearch config interface
 *
 * interface IndexResolverInterface
 *
 * namespace MageStack\Core\Api\OpenSearch
 *
 * @api
 */
interface IndexResolverInterface
{
    /**
     * Get OpenSearch index prefix.
     *
     * @return string
     */
    public function getPrefix(): string;

    /**
     * Get opensearch index.
     *
     * @return string
     */
    public function getIndex(): string;

    /**
     * Get opensearch index pattern.
     *
     * @return string
     */
    public function getIndexPattern(): string;
}
