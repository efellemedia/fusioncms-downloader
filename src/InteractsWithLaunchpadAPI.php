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
    protected $launchpadUrl = 'https://launchpad.efelle.co/api';

    /**
     * Get the latest version of FusionCMS.
     *
     * @return string
     */
    protected function latestFusionCMSRelease($token)
    {
        return json_decode((string) (new HttpClient)->post($this->launchpadUrl('/releases/latest'), [
            'form_params' => [
                'token' => $token,
                'local' => true,
            ]
        ])->getBody())->name;
    }
}
