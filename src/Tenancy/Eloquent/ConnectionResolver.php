<?php

  namespace Tenancy\Eloquent;

  use Illuminate\Database\ConnectionResolverInterface;

  class ConnectionResolver implements ConnectionResolverInterface
  {
      /**
       * @var ConnectionResolverInterface
       */
      private $manager;
      /**
       * @var string
       */
      private $connection;

      public function __construct(string $connection, ConnectionResolverInterface $manager)
      {
          $this->connection = $connection;
          $this->manager = $manager;
      }

      /**
       * Get a database connection instance.
       *
       * @param  string $name
       * @return \Illuminate\Database\ConnectionInterface
       */
      public function connection($name = null)
      {
          dump($name, $this->getDefaultConnection());
          return $this->manager->connection($name ?? $this->getDefaultConnection());
      }

      /**
       * Get the default connection name.
       *
       * @return string
       */
      public function getDefaultConnection()
      {
          return $this->connection;
      }

      /**
       * Set the default connection name.
       *
       * @param  string $name
       * @return void
       */
      public function setDefaultConnection($name)
      {
          throw new \InvalidArgumentException("Tenancy does not allow overriding the default connection.");
      }
  }
