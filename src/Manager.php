<?php

namespace Jundayw\Passport;

use Closure;
use InvalidArgumentException;
use Jundayw\Passport\Algorithms\HashHmacSigner;
use Jundayw\Passport\Contracts\Signer;

class Manager
{
    /**
     * The registered custom driver creators.
     *
     * @var string[]
     */
    protected array $customCreators = [];

    /**
     * The array of resolved drivers.
     *
     * @var array
     */
    protected array $drivers = [];

    public function driver(string $name = 'hash_hmac'): Signer
    {
        return $this->drivers[$name] ??= $this->resolve($name);
    }

    protected function resolve(string $name): Signer
    {
        if (isset($this->customCreators[$name])) {
            return $this->callCustomCreator($name);
        }

        $driverMethod = 'create'.$this->studly($name).'Driver';

        if (method_exists($this, $driverMethod)) {
            return call_user_func([$this, $driverMethod]);
        }

        throw new InvalidArgumentException(
            "A driver [{$name}] for Signer [{$name}] is not defined."
        );
    }

    private function studly(string $value): string
    {
        return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $value)));
    }

    /**
     * Call a custom driver creator.
     *
     * @param string $name
     *
     * @return Signer
     */
    protected function callCustomCreator(string $name): Signer
    {
        return call_user_func($this->customCreators[$name], $name);
    }

    public function createHashHmacDriver(): Signer
    {
        return new HashHmacSigner();
    }

    /**
     * Register a custom driver creator Closure.
     *
     * @param string  $driver
     * @param Closure $callback
     *
     * @return static
     */
    public function extend(string $driver, Closure $callback): static
    {
        $this->customCreators[$driver] = $callback;

        return $this;
    }

    /**
     * Dynamically call the default driver instance.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        return call_user_func_array([$this->driver(), $method], $parameters);
    }

}
