<?php

namespace Tenancy\Tests\Identification\Drivers\Http;

use Illuminate\Database\Schema\Blueprint;
use Tenancy\Identification\Contracts\ResolvesTenants;
use Tenancy\Identification\Drivers\Http\Providers\IdentificationProvider;
use Tenancy\Tests\Identification\Drivers\Http\Mocks\Hostname;
use Tenancy\Tests\Identification\Drivers\Http\Mocks\Tenant;
use Tenancy\Tests\TestCase;

class IdentifyByHttpTest extends TestCase
{
    protected $additionalProviders = [IdentificationProvider::class];
    protected $additionalMocks = [__DIR__ . '/Mocks/factories/'];

    /** @var User */
    protected $user;

    /** @var Hostname */
    protected $hostname;

    protected function afterSetUp()
    {
        /** @var ResolvesTenants $resolver */
        $resolver = $this->app->make(ResolvesTenants::class);
        $resolver->addModel(Hostname::class);

        $this->createSystemTable('hostnames', function (Blueprint $table) {
            $table->increments('id');
            $table->string('fqdn');
            $table->timestamps();
        });

        $this->hostname = factory(Hostname::class)->create();
    }

    /**
     * @test
     */
    public function request_identifies_hostname()
    {
        $this->assertFalse($this->environment->isIdentified());

        $this->get("http://" . $this->hostname->fqdn);

        $this->assertTrue($this->environment->isIdentified());

        $this->assertEquals($this->hostname->fqdn, optional($this->environment->getTenant())->fqdn);
    }
}
