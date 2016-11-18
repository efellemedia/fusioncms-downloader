<?php

namespace Efelle\FusionInstaller;

use GuzzleHttp\Client as HttpClient;

trait InteractsWithLaunchpadAPI
{
    /**
     * The launchpad base API URL.
     *
     * @var string
     */
    protected $launchpadUrl = 'http://launchpad.efelle.co/api';

    /**
     * Get the latest version of FusionCMS.
     *
     * @return string
     */
    protected function latestFusionCMSRelease()
    {
        return json_decode((string) (new HttpClient)->get(
            $this->launchpadUrl('/releases/latest')
        )->getBody())->name;
    }
}
