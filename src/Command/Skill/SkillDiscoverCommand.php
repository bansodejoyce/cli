<?php

declare(strict_types=1);

namespace Acquia\Cli\Command\Skill;

use Acquia\Cli\Command\CommandBase;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'skill:discover',
    description: 'Discover and install Acquia skills for your AI coding agent'
)]
final class SkillDiscoverCommand extends CommandBase
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->localMachineHelper->commandExists('npx')) {
            $this->io->error('npx is required to install Acquia skills. Install Node.js 18 or later from https://nodejs.org and try again.');
            return Command::FAILURE;
        }

        $this->io->writeln('Installing Acquia skills (acquia/acquia-skills)...');

        $process = $this->localMachineHelper->execute(
            ['npx', '--yes', 'skills', 'add', 'acquia/acquia-skills'],
            null,
            null,
            true
        );

        if (!$process->isSuccessful()) {
            $this->io->error('Failed to install acquia/acquia-skills. Check that npx can reach the npm registry and try again.');
            return Command::FAILURE;
        }

        $this->io->success('Acquia skills installed. Restart your agent to pick up the new skills.');
        return Command::SUCCESS;
    }
}
