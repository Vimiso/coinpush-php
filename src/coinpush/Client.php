<?php

namespace Coinpush;

use Coinpush\Config;
use Coinpush\Request;

class Client extends Request
{
    /**
     * The configuration options.
     *
     * @var \Coinpush\Config
     */
    protected $config;

    /**
     * Make the client with the given config.
     *
     * @param \Coinpush\Config $config
     * @return void
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->client = $this->makeClient(
            $this->config->getBaseUri(),
            $this->config->getTimeout()
        );
    }

    /**
     * Convert the fiat amount into the given cryptocurrency.
     *
     * @param string $fiat
     * @param float $amount
     * @param string $currency
     * @return array
     */
    public function convert(string $fiat, float $amount, string $currency)
    {
        $path = $this->makePath("/convert/{$fiat}/{$amount}/{$currency}");
        $options = $this->makeOptions();

        return $this->setMethod('GET')->setPath($path)->setOptions($options)->make();
    }

    /**
     * Create a new payment address given the cryptocurrency and parameters.
     *
     * @param string $currency
     * @param array $params
     * @return array
     */
    public function create(string $currency, array $params)
    {
        $path = $this->makePath("/create/{$currency}");
        $options = $this->makeOptions($params);

        return $this->setMethod('POST')->setPath($path)->setOptions($options)->make();
    }

    /**
     * Create a new charge resource given the fiat and parameters.
     *
     * @param string $fiat
     * @param array $params
     * @return array
     */
    public function charge(string $fiat, array $params)
    {
        $path = $this->makePath("/charge/{$fiat}");
        $options = $this->makeOptions($params);

        return $this->setMethod('POST')->setPath($path)->setOptions($options)->make();
    }

    /**
     * Check charge payment details given the token.
     *
     * @param string $token
     * @return array
     */
    public function chargeView(string $token)
    {
        $path = $this->makePath("/charge/{$token}");
        $options = $this->makeOptions();

        return $this->setMethod('GET')->setPath($path)->setOptions($options)->make();
    }

    /**
     * Check payment statuses given the label.
     *
     * @param string $label
     * @return array
     */
    public function statuses(string $label)
    {
        $path = $this->makePath("/statuses/{$label}");
        $options = $this->makeOptions();

        return $this->setMethod('GET')->setPath($path)->setOptions($options)->make();
    }

    /**
     * Check address details given the label.
     *
     * @param string $label
     * @return array
     */
    public function address(string $label)
    {
        $path = $this->makePath("/address/{$label}");
        $options = $this->makeOptions();

        return $this->setMethod('GET')->setPath($path)->setOptions($options)->make();
    }

    /**
     * Make a consistent request path.
     *
     * @param string $path
     * @return string
     */
    protected function makePath(string $path)
    {
        $path = trim($path, '/');
        $versionPath = $this->config->getVersionPathSegment();
        $usingTestnet = $this->config->isUsingTestnet();

        if (! $usingTestnet) {
            return "/{$versionPath}/{$path}";
        }

        return "/{$versionPath}/testnet/{$path}";
    }

    /**
     * Make the options and include the parameters.
     *
     * @param array $params
     * @return array
     */
    protected function makeOptions(array $params = [])
    {
        return [
            'headers' => $this->getPackageHeaders(),
            'form_params' => $params,
        ];
    }

    /**
     * @return array
     */
    protected function getPackageHeaders()
    {
        return [
            'X-Package-Manager' => 'composer',
            'X-Package-Version' => $this->config->getVersion(),
        ];
    }
}
