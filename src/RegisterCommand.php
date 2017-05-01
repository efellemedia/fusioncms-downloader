<?php

namespace Efelle\FusionInstaller;

use Exception;
use GuzzleHttp\Client as HttpClient;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;

class RegisterCommand extends Command
{
    use InteractsWithLaunchpadAPI,
        InteractsWithFusionCMSConfiguration;

    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('register')
            ->setDescription('Register a registration token with the installer')
            ->addArgument('token', InputArgument::REQUIRED, 'The registration token');
    }

    /**
     * Execute the command.
     *
     * @param  InputInterface  $input
     * @param  OutputInterface  $output
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        if (! $this->valid($input->getArgument('token'))) {
            return $this->tokenIsInvalid($output);
        }

        if (! $this->configExists()) {
            mkdir($this->homePath().'/.fusioncms');
        }

        $this->storeToken($input->getArgument('token'));

        $this->tokenIsValid($output);
    }

    /**
     * Determine if the given token is valid
     *
     * @param  string  $token
     * @return bool
     */
    protected function valid($token)
    {
        try {
            (new HttpClient)->post($this->launchpadUrl.'/token/validate/', [
                'form_params' => [
                    'token' => $token
                ]
            ]);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Inform the user that the token is valid.
     *
     * @param  OutputInterface  $output
     * @return void
     */
    protected function tokenIsValid($output)
    {
        $output->writeln('Validating Token: <info>✔</info>');
        $output->writeln('<info>Thanks for registering FusionCMS!</info>');
    }

    /**
     * Inform the user that the token is invalid.
     *
     * @param  OutputInterface  $output
     * @return void
     */
    protected function tokenIsInvalid($output)
    {
        $output->writeln('Validating Token: <fg=red>✘</>');
        $output->writeln('<comment>This registration token is invalid.</comment>');
    }
}
