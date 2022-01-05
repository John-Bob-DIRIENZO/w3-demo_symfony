<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TrucRandomCommand extends Command
{
    protected static $defaultName = 'app:truc-random';
    protected static $defaultDescription = 'Je suis une commande de démo';

    protected function configure(): void
    {
        $this
            ->addArgument('prenom', InputArgument::OPTIONAL, 'Rentrez votre prénom')
            ->addOption('uppercase', null, InputOption::VALUE_NONE, 'La même chose mais en gros');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $prenom = $input->getArgument('prenom');

        $randoms = ['Francis', 'Pierre', 'Anne'];
        $random = $randoms[array_rand($randoms)];

        if ($prenom) {
            $io->note(sprintf('Bonjour: %s', $prenom));
        }

        if ($input->getOption('uppercase')) {
            $random = strtoupper($random);
        }

        $io->success($random);
        $io->warning('je suis un warning');

        return Command::SUCCESS;
    }
}
