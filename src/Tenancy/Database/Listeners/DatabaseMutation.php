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

namespace Tenancy\Database\Listeners;

use Tenancy\Database\DatabaseResolver;
use Tenancy\Environment;

abstract class DatabaseMutation
{
    /**
     * @var null|\Tenancy\Database\Contracts\ProvidesDatabase
     */
    protected $driver;
    /**
     * @var \Illuminate\Database\ConnectionInterface
     */
    protected $connection;

    public function __construct(DatabaseResolver $resolver, Environment $environment)
    {
        $tenant = $environment->getTenant();

        $this->driver = $resolver->__invoke($tenant);
        $this->connection = $environment->getSystemConnection($tenant);
    }

    protected function processRawStatements(array $statements = null)
    {
        if ($statements === null) {
            return;
        }

        $this->connection->beginTransaction();

        foreach ($statements as $statement) {
            $this->connection->statement($statement);
        }

        $this->connection->commit();
    }
}
