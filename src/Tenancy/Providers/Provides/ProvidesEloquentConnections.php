<?php

namespace Tenancy\Providers\Provides;

use Illuminate\Config\Repository;
use Illuminate\Database\ConnectionResolverInterface;
use Tenancy\Eloquent\ConnectionResolver;
use Tenancy\Environment;

trait ProvidesEloquentConnections
{
    protected function bootProvidesEloquentConnections()
    {
        $this->app->extend('db', function (ConnectionResolverInterface $manager) {
            return new ConnectionResolver(
                $this->defaultEloquentConnection(),
                $manager
            );
        });
    }

    protected function defaultEloquentConnection(): string
    {
        /** @var Repository $config */
        $config = $this->app['config'];
        $connection = $config->get('database.default');

        if ($config->get('tenancy.database.models-default-to-tenant-connection-only-when-identified') === true) {
            /** @var Environment $environment */
            $environment = $this->app->make(Environment::class);
            $connection = $config->get('database.default');
            if($environment->isIdentified() && $environment->getTenant()) {
                $connection = Environment::getDefaultTenantConnectionName();
            }
        }

        if ($config->get('tenancy.database.models-default-to-tenant-connection') === true) {
            $connection = Environment::getDefaultTenantConnectionName();
        }

        if ($config->get('tenancy.database.models-default-to-system-connection') === true) {
            $connection = Environment::getDefaultSystemConnectionName();
        }

        return $connection;
    }
}
