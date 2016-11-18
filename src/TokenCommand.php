<?php

namespace Efelle\FusionInstaller;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;

class TokenCommand extends Command
{
    use InteractsWithFusionCMSConfiguration;

    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('token')
            ->setDescription('Display the currently registered FusionCMS registration token');
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
        $output->writeln('<info>FusionCMS Registration Token:</info> '.$this->readToken());
    }
}
