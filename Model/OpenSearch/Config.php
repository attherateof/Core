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

namespace MageStack\Core\Model\OpenSearch;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use MageStack\Core\Api\OpenSearch\ConfigInterface;

/**
 * Configuration model for MageStack OpenSearch Logger
 *
 * Class Config
 *
 * namespace MageStack\Core\Model\OpenSearch
 */
class Config implements ConfigInterface
{
    /**
     * Configuration paths
     */
    public const INDEX = 'catalog/search/opensearch_index_prefix';
    public const OPENSEARCH_HOST = 'catalog/search/opensearch_server_hostname';
    public const OPENSEARCH_PORT = 'catalog/search/opensearch_server_port';
    public const OPENSEARCH_ENABLE_AUTH = 'catalog/search/opensearch_enable_auth';
    public const OPENSEARCH_USER_NAME = 'catalog/search/opensearch_username';
    public const OPENSEARCH_PASSWORD = 'catalog/search/opensearch_password';

    public const OPENSEARCH_DEFAULT_PORT = '9200';

    /**
     * constructor
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getIndexPrefix(): string
    {
        $prefix = $this->scopeConfig->getValue(
            self::INDEX,
            ScopeInterface::SCOPE_STORE
        );

        return is_string($prefix) ? $prefix : '';
    }

    /**
     * @inheritDoc
     */
    public function getOpensearchHost(): string
    {
        $host = $this->scopeConfig->getValue(
            self::OPENSEARCH_HOST,
            ScopeInterface::SCOPE_STORE
        );

        return is_string($host) ? $host : '';
    }

    /**
     * @inheritDoc
     */
    public function getOpensearchPort(): string
    {
        $port = $this->scopeConfig->getValue(
            self::OPENSEARCH_PORT,
            ScopeInterface::SCOPE_STORE
        );

        return is_string($port) ? $port : self::OPENSEARCH_DEFAULT_PORT;
    }

    /**
     * @inheritDoc
     */
    public function isOpensearchAuthEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::OPENSEARCH_ENABLE_AUTH,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @inheritDoc
     */
    public function getOpenSearhUserName(): ?string
    {
        $user = $this->scopeConfig->getValue(
            self::OPENSEARCH_USER_NAME,
            ScopeInterface::SCOPE_STORE
        );

        return is_string($user) ? $user : null;
    }

    /**
     * @inheritDoc
     */
    public function getOpenSearhPassword(): ?string
    {
        $pasword = $this->scopeConfig->getValue(
            self::OPENSEARCH_PASSWORD,
            ScopeInterface::SCOPE_STORE
        );

        return is_string($pasword) ? $pasword : null;
    }
}
