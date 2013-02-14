<?php

namespace TrainingWheels\Console;
use TrainingWheels\Log\Log;
use TrainingWheels\Conn\KeyManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Exception;

class KeyCreate extends Command {
  private $config;

  public function __construct($config) {
    parent::__construct();
    $this->config = $config;
  }

  protected function configure() {
    $this->setName('key:create')
         ->setDescription('Create a new keypair.');
  }

  protected function execute(InputInterface $input, OutputInterface $output) {
    Log::log('CLI command: KeyCreate', L_INFO);

    $dialog = $this->getHelperSet()->get('dialog');
    if (!$dialog->askConfirmation($output, 'Generate a new keypair? Existing keys are backed up. (y/n) ', FALSE)) {
      $output->writeln('<comment>Aborting.</comment>');
      return;
    }

    $gen = new KeyManager($this->config['base_path']);
    $output->writeln('');
    $output->writeln('<comment>' . trim($gen->createKey()) . '</comment>');
    $output->writeln('');
    $output->writeln('<info>Keypair generated.</info>');
  }
}
