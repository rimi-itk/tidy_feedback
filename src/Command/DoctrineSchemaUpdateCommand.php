<?php

namespace ItkDev\TidyFeedback\Command;

use ItkDev\TidyFeedback\TidyFeedbackHelper;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'tidy-feedback:doctrine:schema-update',
    description: 'Update Tidy feedback database schema',
)]
class DoctrineSchemaUpdateCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return TidyFeedbackHelper::updateSchema($output) ? self::SUCCESS : self::FAILURE;
    }
}
