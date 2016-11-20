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
    public function __construct(NewCommand $command, $name)
    {
        $this->name    = $name;
        $this->command = $command;
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
     * @param  string  $version
     * @return $this
     */
    protected function download($zipFile)
    {
        $token    = $this->readToken();
        $response = (new Client)->get($this->launchpadUrl.'/release/download/'.$token);

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
        $archive = new ZipArchive;
        $archive->open($zipFile);
        $archive->extractTo($directory);
        $archive->close();
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
