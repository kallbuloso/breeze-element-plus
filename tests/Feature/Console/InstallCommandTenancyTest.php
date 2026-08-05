<?php

declare(strict_types=1);

namespace Kallbuloso\BreezeElementPlus\Tests\Feature\Console;

use Kallbuloso\BreezeElementPlus\Console\InstallCommand;
use Kallbuloso\BreezeElementPlus\Tests\TestCase;
use Symfony\Component\Console\Input\ArrayInput;

class InstallCommandTenancyTest extends TestCase
{
    public function test_it_defaults_to_single_tenancy_without_interactive_input(): void
    {
        $command = new class extends InstallCommand
        {
            public function resolveTenancyForTest(array $arguments): bool
            {
                $this->input = new ArrayInput($arguments, $this->getDefinition());
                $this->input->setInteractive(false);

                return $this->resolveTenancy();
            }

            public function tenancyForTest(): string
            {
                return $this->tenancy;
            }
        };

        $this->assertTrue($command->resolveTenancyForTest(['stack' => 'api']));
        $this->assertSame('single', $command->tenancyForTest());
    }

    public function test_it_accepts_explicit_multi_tenancy(): void
    {
        $command = new class extends InstallCommand
        {
            public function resolveTenancyForTest(array $arguments): bool
            {
                $this->input = new ArrayInput($arguments, $this->getDefinition());
                $this->input->setInteractive(false);

                return $this->resolveTenancy();
            }

            public function tenancyForTest(): string
            {
                return $this->tenancy;
            }
        };

        $this->assertTrue($command->resolveTenancyForTest(['stack' => 'api', '--tenancy' => 'multi']));
        $this->assertSame('multi', $command->tenancyForTest());
    }

    public function test_it_rejects_invalid_tenancy_before_installation(): void
    {
        $this->artisan('breeze-element-plus:install', [
            'stack' => 'api',
            '--lang' => 'pt_BR',
            '--tenancy' => 'invalid',
        ])
            ->expectsOutputToContain('Invalid tenancy mode: invalid.')
            ->assertExitCode(1);
    }
}
