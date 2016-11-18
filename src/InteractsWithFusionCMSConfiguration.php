<?php

namespace Efelle\FusionInstaller;

use Exception;

trait InteractsWithFusionCMSConfiguration
{
    /**
     * Get the FusionCMS registration token from the configuration file.
     *
     * @return string
     * @throws Exception
     */
    protected function readToken()
    {
        if (! $this->configExists()) {
            throw new Exception("FusionCMS configuration file not found. Please register a token.");
        }

        return json_decode(
            file_get_contents($this->configPath()), true
        )['token'];
    }

    /**
     * Write the FusionCMS registration token to the configuration.
     *
     * @param  string  $token
     * @return void
     */
    protected function storeToken($token)
    {
        file_put_contents($this->configPath(), json_encode([
            'token' => $token
        ], JSON_PRETTY_PRINT).PHP_EOL);
    }

    /**
     * Determine if the FusionCMS configuration file exists.
     *
     * @return bool
     */
    protected function configExists()
    {
        return file_exists($this->configPath());
    }

    /**
     * Get the FusionCMS configuration file path.
     *
     * @return string
     */
    protected function configPath()
    {
        return $this->homePath().'/.fusioncms/config.json';
    }

    /**
     * Get the user's home path.
     *
     * @return string
     * @throws Exception
     */
    protected function homePath()
    {
        if (! empty($_SERVER['HOME'])) {
            return $_SERVER['HOME'];
        } elseif (! empty($_SERVER['HOMEDRIVE']) and ! empty($_SERVER['HOMEPATH'])) {
            return $_SERVER['HOMEDRIVE'].$_SERVER['HOMEPATH'];
        } else {
            throw new Exception('Cannot determine home directory.');
        }
    }
}
