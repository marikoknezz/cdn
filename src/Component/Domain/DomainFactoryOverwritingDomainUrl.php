<?php

declare(strict_types=1);

namespace Shopsys\Cdn\Component\Domain;

use Shopsys\FrameworkBundle\Component\Domain\DomainFactoryOverwritingDomainUrl as BaseDomainFactoryOverwritingDomainUrl;

class DomainFactoryOverwritingDomainUrl extends BaseDomainFactoryOverwritingDomainUrl
{
    /**
     * @param string $domainsConfigFilepath
     * @param string $domainsUrlsConfigFilepath
     * @return \Shopsys\Cdn\Component\Domain\Domain
     */
    public function create($domainsConfigFilepath, $domainsUrlsConfigFilepath)
    {
        $domainConfigs = $this->domainsConfigLoader->loadDomainConfigsFromYaml($domainsConfigFilepath, $domainsUrlsConfigFilepath);
        if ($this->overwriteDomainUrl !== null) {
            $domainConfigs = $this->overwriteDomainUrl($domainConfigs);
        }

        $domain = new Domain($domainConfigs, $this->setting, null);

        $domainId = getenv('DOMAIN');
        if ($domainId !== false) {
            $domain->switchDomainById($domainId);
        }

        return $domain;
    }
}
