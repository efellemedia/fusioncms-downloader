<?php

namespace Efelle\FusionInstaller;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NewCommand extends Command
{
    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('new')
            ->setDescription('Create a new FusionCMS project')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the project')
            ->addOption('release', null, InputOption::VALUE_REQUIRED, 'Specify the release of FusionCMS to download', null);
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
        $this->input  = $input;
        $this->output = new SymfonyStyle($input, $output);

        $this->path = getcwd().'/'.$input->getArgument('name');

        $installers = [
            Installation\CreateFusionCMSProject::class,
        ];

        foreach ($installers as $installer) {
            (new $installer($this, $input->getArgument('name'), $input->getOption('release')))->install();
        }
    }
}
