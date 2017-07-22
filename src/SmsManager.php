<?php

namespace Utils;

use Illuminate\Support\Arr;
use Illuminate\Support\Manager;
use Utils\Provider\AliyunProvider;
use Utils\Provider\ChuanglanProvider;
use Utils\Provider\ChuanglanOldProvider;
use Utils\Provider\MontnetsProvider;
use Utils\Provider\InvalidArgumentException;

class SmsManager extends Manager implements Contracts\Factory
{
    /**
     * Get a driver instance.
     *
     * @param  string  $driver
     * @return mixed
     */
    public function with($driver)
    {
        return $this->driver($driver);
    }

    /**
     * Create an instance of the specified driver.
     *
     * @return \Utils\Provider\AbstractProvider
     */
    protected function createAliyunDriver()
    {
        $config = $this->app['config']['services.aliyun'];

        return $this->buildProvider(
            AliyunProvider::class, $config
        );
    }

    /**
     * Create an instance of the specified driver.
     *
     * @return \Utils\Provider\AbstractProvider
     */
    protected function createChuanglanDriver()
    {
        $config = $this->app['config']['services.chuanglan'];

        return $this->buildProvider(
            ChuanglanProvider::class, $config
        );
    }

    /**
     * Create an instance of the specified driver.
     *
     * @return \Utils\Provider\AbstractProvider
     */
    protected function createChuanglanOldDriver()
    {
        $config = $this->app['config']['services.chuanglanold'];

        return $this->buildProvider(
            ChuanglanOldProvider::class, $config
        );
    }

    /**
     * Create an instance of the specified driver.
     *
     * @return \Utils\Provider\AbstractProvider
     */
    protected function createMontnetsDriver()
    {
        $config = $this->app['config']['services.montnets'];

        return $this->buildProvider(
          MontnetsProvider::class, $config
        );
    }

    /**
     * Build an OAuth 2 provider instance.
     *
     * @param  string  $provider
     * @param  array  $config
     * @return \Utils\Provider\AbstractProvider
     */
    public function buildProvider($provider, $config)
    {
        return new $provider(
            $this->app['request'], $config['client_id'],
            $config['client_secret'],
            Arr::get($config, 'guzzle', [])
        );
    }

    /**
     * Format the server configuration.
     *
     * @param  array  $config
     * @return array
     */
    public function formatConfig(array $config)
    {
        return array_merge([
            'identifier' => $config['client_id'],
            'secret' => $config['client_secret'],
            'callback_uri' => value($config['redirect']),
        ], $config);
    }

    /**
     * Get the default driver name.
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        throw new InvalidArgumentException('No Socialite driver was specified.');
    }
}
