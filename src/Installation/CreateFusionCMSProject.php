<?php

namespace Efelle\FusionInstaller\Installation;

use ZipArchive;
use RuntimeException;
use GuzzleHttp\Client;
use Efelle\FusionInstaller\NewCommand;
use Efelle\FusionInstaller\InteractsWithFusionCMSConfiguration;
use Efelle\FusionInstaller\InteractsWithLaunchpadAPI;
use Symfony\Component\Process\Process;

class CreateFusionCMSProject
{
    use InteractsWithFusionCMSConfiguration,
        InteractsWithLaunchpadAPI;

    protected $command;
    protected $name;

    /**
     * Create a new installation helper instance.
     *
     * @param  NewCommand  $command
     * @param  string  $name
     * @return void
     */
    public function __construct(NewCommand $command, $name, $release)
    {
        $this->name    = $name;
        $this->command = $command;
        $this->release = $release;
    }

    /**
     * Run the installation helper.
     *
     * @return void
     */
    public function install()
    {
        if (! class_exists('ZipArchive')) {
            throw new RuntimeException('The Zip PHP extension is not installed. Please install it and try again.');
        }

        $this->verifyProjectDoesntExist(
            $directory = ($this->command->input->getArgument('name')) ? getcwd().'/'.$this->command->input->getArgument('name') : getcwd()
        );

        $this->command->output->writeln('<info>Preparing lift-off...</info>');

        $zipFile = $this->makeFilename();

        $this->download($zipFile)
             ->extract($zipFile, $directory)
             ->cleanUp($zipFile);

        $this->command->output->writeln('<comment>And lift-off! Build something amazing.</comment>');
    }

    /**
     * Verify that the application does not already exist.
     *
     * @param  string  $directory
     * @return void
     */
    protected function verifyProjectDoesntExist($directory)
    {
        if ((is_dir($directory) || is_file($directory)) && $directory != getcwd()) {
            throw new RuntimeException('Project already exists!');
        }
    }

    /**
     * Generate a random temporary filename.
     *
     * @return string
     */
    protected function makeFilename()
    {
        return getcwd().'/fusioncms_'.md5(time().uniqid()).'.zip';
    }

    /**
     * Download the temporary Zip to the given file.
     *
     * @param  string  $zipFile
     * @param  string  $release
     * @return $this
     */
    protected function download($zipFile)
    {
        $token = $this->readToken();
        $url   = $this->launchpadUrl.'/release/download'
            .(is_null($this->release) ? null : '/'.$this->release);

        $response = (new Client)->post($url, [
            'form_params' => [
                'token' => $token
            ]
        ]);

        file_put_contents($zipFile, $response->getBody());

        return $this;
    }

    /**
     * Extract the Zip file into the given directory.
     *
     * @param  string  $zipFile
     * @param  string  $directory
     * @return $this
     */
    protected function extract($zipFile, $directory)
    {
        $tempDirectory = $directory.'-temp';

        $archive = new ZipArchive;
        $archive->open($zipFile);
        $archive->extractTo($tempDirectory);
        $archive->close();

        $files = scandir($tempDirectory);
        $root  = $tempDirectory.'/'.$files[2];

		rename($root, $directory);

        rmdir($tempDirectory);

        return $this;
    }

    /**
     * Clean-up the Zip file.
     *
     * @param  string  $zipFile
     * @return $this
     */
    protected function cleanUp($zipFile)
    {
        @chmod($zipFile, 0777);
        @unlink($zipFile);
        return $this;
    }
}
