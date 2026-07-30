<?php

declare(strict_types=1);

namespace Acquia\Cli\Tests\Commands\Skill;

use Acquia\Cli\Command\CommandBase;
use Acquia\Cli\Command\Skill\SkillDiscoverCommand;
use Acquia\Cli\Tests\CommandTestBase;
use Prophecy\Argument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Process\Process;

/**
 * @property \Acquia\Cli\Command\Skill\SkillDiscoverCommand $command
 */
class SkillDiscoverCommandTest extends CommandTestBase
{
    protected function createCommand(): CommandBase
    {
        return $this->injectCommand(SkillDiscoverCommand::class);
    }

    public function testSkillDiscoverCommandNpxMissing(): void
    {
        $localMachineHelper = $this->mockLocalMachineHelper();
        $localMachineHelper
            ->commandExists('npx')
            ->willReturn(false)
            ->shouldBeCalled();

        $this->executeCommand();
        $this->assertEquals(Command::FAILURE, $this->getStatusCode());
        $output = $this->getDisplay();
        $this->assertStringContainsString('npx', $output);
    }

    public function testSkillDiscoverCommandSuccess(): void
    {
        $localMachineHelper = $this->mockLocalMachineHelper();
        $localMachineHelper
            ->commandExists('npx')
            ->willReturn(true)
            ->shouldBeCalled();

        $process = $this->prophet->prophesize(Process::class);
        $process->isSuccessful()->willReturn(true);
        $process->getExitCode()->willReturn(0);

        $localMachineHelper
            ->execute(
                ['npx', '--yes', 'skills', 'add', 'acquia/acquia-skills'],
                Argument::any(),
                Argument::any(),
                Argument::any()
            )
            ->willReturn($process->reveal())
            ->shouldBeCalled();

        $this->executeCommand();
        $this->assertEquals(Command::SUCCESS, $this->getStatusCode());
        $output = $this->getDisplay();
        $this->assertStringContainsString('acquia/acquia-skills', $output);
    }

    public function testSkillDiscoverCommandInstallFails(): void
    {
        $localMachineHelper = $this->mockLocalMachineHelper();
        $localMachineHelper
            ->commandExists('npx')
            ->willReturn(true)
            ->shouldBeCalled();

        $process = $this->prophet->prophesize(Process::class);
        $process->isSuccessful()->willReturn(false);
        $process->getExitCode()->willReturn(1);

        $localMachineHelper
            ->execute(
                ['npx', '--yes', 'skills', 'add', 'acquia/acquia-skills'],
                Argument::any(),
                Argument::any(),
                Argument::any()
            )
            ->willReturn($process->reveal())
            ->shouldBeCalled();

        $this->executeCommand();
        $this->assertEquals(Command::FAILURE, $this->getStatusCode());
    }
}
