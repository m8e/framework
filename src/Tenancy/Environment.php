<?php

/*
 * This file is part of the tenancy/tenancy package.
 *
 * (c) Daniël Klabbers <daniel@klabbers.email>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see http://laravel-tenancy.com
 * @see https://github.com/tenancy
 */

namespace Tenancy;

use Illuminate\Database\Connection;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Traits\Macroable;
use Tenancy\Identification\Contracts\Tenant;
use Tenancy\Identification\Contracts\ResolvesTenants;

class Environment
{
    use Macroable;

    /**
     * @var Tenant|null
     */
    protected $tenant;

    /**
     * Whether the tenant has been identified previously.
     *
     * @var bool
     */
    protected $identified = false;

    public function setTenant(Tenant $tenant = null)
    {
        $this->tenant = $tenant;

        return $this;
    }

    public function getTenant(bool $refresh = false): ?Tenant
    {
        if (! $this->identified || $refresh) {
            $this->setTenant(
                app(ResolvesTenants::class)->__invoke()
            );

            $this->identified = true;
        }

        return $this->tenant;
    }

    public function isIdentified(): bool
    {
        return $this->identified;
    }

    public function setIdentified(bool $identified)
    {
        $this->identified = $identified;

        return $this;
    }

    public function getTenantConnection(): ?Connection
    {
        return app('db')->connection(
            static::getDefaultTenantConnectionName()
        );
    }

    public function getSystemConnection(Tenant $tenant = null): Connection
    {
        return app('db')->connection(
            optional($tenant ?? $this->getTenant())->getManagingSystemConnection() ??
            static::getDefaultSystemConnectionName()
        );
    }

    public static function getDefaultTenantConnectionName(): string
    {
        return config('tenancy.database.tenant-connection-name');
    }

    public static function getDefaultSystemConnectionName(): string
    {
        return config('tenancy.database.system-connection-name') ?? config('database.default');
    }
}
