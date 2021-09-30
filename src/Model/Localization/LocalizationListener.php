<?php

declare(strict_types=1);

namespace Shopsys\Cdn\Model\Localization;

use Shopsys\FrameworkBundle\Model\Localization\LocalizationListener as BaseLocalizationListener;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * @property \Shopsys\Cdn\Component\Domain\Domain $domain
 */
class LocalizationListener extends BaseLocalizationListener
{
    /**
     * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if ($this->domain->isOnCdnDomain() === false) {
            parent::onKernelRequest($event);
        }
    }
}
