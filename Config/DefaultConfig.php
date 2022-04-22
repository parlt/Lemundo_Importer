<?php
declare(strict_types=1);

namespace Lemundo\Importer\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class DefaultConfig
{
    private ScopeConfigInterface $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function getImageUrlPrefix(): string
    {
        return (string)$this->scopeConfig->getValue(
            'lemundo_importer/general/image_url_prefix',
            ScopeInterface::SCOPE_STORES
        );
    }

    public function getJsonStartIndex(): string
    {
        return (string)$this->scopeConfig->getValue(
            'lemundo_importer/general/json_start_index',
            ScopeInterface::SCOPE_STORES
        );
    }

    public function getLandingPageAttributeSetName(): string
    {
        return (string)$this->scopeConfig->getValue(
            'lemundo_importer/general/landing_page_attributeset_name',
            ScopeInterface::SCOPE_STORES
        );
    }
}
