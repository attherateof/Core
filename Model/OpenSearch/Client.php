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

use Magento\Elasticsearch\SearchAdapter\ConnectionManager;
use Magento\OpenSearch\Model\SearchClient;
use OpenSearch\Client as OpenSearchClient;
use RuntimeException;

/**
 * OpenSearch Search Client
 *
 * Class Client
 *
 * namespace MageStack\Core\Model\OpenSearch
 */
class Client
{
    /**
     * Constructor
     *
     * @param ConnectionManager $client
     */
    public function __construct(
        private readonly ConnectionManager $client
    ) {}

    /**
     * Get OpenSearch Client
     *
     * @return OpenSearchClient
     *
     * @throws RuntimeException
     */
    public function getInstance(): OpenSearchClient
    {
        $searchClient = null;
        /**
         * @var SearchClient $client
         */
        $client = $this->client->getConnection();

        if (method_exists($client, 'getOpenSearchClient')) {
            $searchClient = $client->getOpenSearchClient();
        }

        if (!$searchClient instanceof OpenSearchClient) {
            throw new RuntimeException('Please select Opensearch as your search engine.');
        }

        return $searchClient;
    }
}
