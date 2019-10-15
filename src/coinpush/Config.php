<?php

namespace Coinpush;

use Exception;

class Config
{
    /**
     * The supported API versions and their path segments.
     *
     * @var array
     */
    const VERSIONS = [
        1 => 'api',
    ];

    /**
     * The selected API version.
     *
     * @var int
     */
    protected $version = 1;

    /**
     * The base URI address.
     *
     * @var string
     */
    protected $baseUri = 'https://coinpush.io';

    /**
     * The request timeout seconds.
     *
     * @var int
     */
    protected $timeout = 30;

    /**
     * Whether to request the testnet or not.
     *
     * @var bool
     */
    protected $testnet = false;

    /**
     * If given, set a custom base URI on construct.
     *
     * @param null|string $baseUri
     * @return void
     */
    public function __construct($baseUri = null)
    {
        if (! empty($baseUri)) {
            $this->setBaseUri($baseUri);
        }
    }

    /**
     * Enable developer mode.
     *
     * @return $this
     */
    public function enableDevMode()
    {
        $this->setBaseUri('http://coinpush.test');

        return $this;
    }

    /**
     * Determine whether the testnet was enabled.
     *
     * @return bool
     */
    public function isUsingTestnet()
    {
        return $this->testnet;
    }

    /**
     * Use the testnet.
     *
     * @return $this
     */
    public function useTestnet()
    {
        $this->testnet = true;

        return $this;
    }

    /**
     * Use the mainnet.
     *
     * @return $this
     */
    public function useMainnet()
    {
        $this->testnet = false;

        return $this;
    }

    /**
     * Use the given version if it's supported.
     *
     * @param int $version
     * @return $this
     * @throws \Exception
     */
    public function useVersion($version)
    {
        if (array_key_exists($version, $this::VERSIONS)) {
            $this->version = $version;
        } else {
            throw new Exception("API version [{$version}] is not supported.");
        }

        return $this;
    }

    /**
     * Get the version number.
     *
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Get the version's path segment.
     *
     * @return string
     */
    public function getVersionPathSegment()
    {
        return $this::VERSIONS[$this->version];
    }

    /**
     * Get the base URI.
     *
     * @return string
     */
    public function getBaseUri()
    {
        return $this->baseUri;
    }

    /**
     * Get the timeout seconds.
     *
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * Set the given base URI.
     *
     * @param string $baseUri
     * @return void
     */
    public function setBaseUri(string $baseUri)
    {
        $this->baseUri = $baseUri;
    }

    /**
     * Set the given timeout seconds.
     *
     * @param int $timeout
     * @return void
     */
    public function setTimeout(int $timeout)
    {
        $this->timeout = $timeout;
    }
}
