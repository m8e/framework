<?php

namespace Tenancy\Tests\Identification\Drivers\Console;

use Illuminate\Contracts\Console\Kernel;
use Tenancy\Identification\Contracts\ResolvesTenants;
use Tenancy\Identification\Drivers\Console\Providers\IdentificationProvider;
use Tenancy\Tests\Identification\Drivers\Console\Mocks\Tenant;
use Tenancy\Tests\TestCase;

class IdentifyByConsoleTest extends TestCase
{
    protected $additionalProviders = [IdentificationProvider::class];
    protected $additionalMocks = [__DIR__ . '/Mocks/factories/'];

    /** @var User */
    protected $user;

    /** @var Tenant */
    protected $tenant;

    protected function afterSetUp()
    {
        /** @var ResolvesTenants $resolver */
        $resolver = $this->app->make(ResolvesTenants::class);
        $resolver->addModel(Tenant::class);

        $this->tenant = factory(Tenant::class)->create();

        $this->app->make(Kernel::class)->command(
            'identifies {--tenant}',
            function () {}
        );
    }

    /**
     * @test
     */
    public function artisan_identifies_tenant()
    {
        $this->assertFalse($this->environment->isIdentified());

        $this->artisan('identifies', [
            '--tenant' => $this->tenant->name
        ]);

        $this->assertEquals($this->tenant->name, optional($this->environment->getTenant())->name);

        $this->assertTrue($this->environment->isIdentified());
    }

    /**
     * @test
     */
    public function artisan_does_not_identify_multiple()
    {
        $this->assertFalse($this->environment->isIdentified());

        $this->artisan('identifies', [
            '--tenant' => $this->tenant->name,
            '--tenant' => 'foo'
        ]);

        $this->assertNull(optional($this->environment->getTenant())->name);

        $this->assertTrue($this->environment->isIdentified());
    }
}
