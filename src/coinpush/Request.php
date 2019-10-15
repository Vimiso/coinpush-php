<?php

namespace Coinpush;

use Throwable;
use GuzzleHttp\Client;
use Coinpush\Exceptions\RequestException;
use GuzzleHttp\Exception\RequestException as GuzzleException;

abstract class Request
{
    /**
     * The Guzzle client.
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * The request timeout seconds.
     *
     * @var int
     */
    protected $timeout = 60;

    /**
     * The base URI address.
     *
     * @var string
     */
    protected $baseUri;

    /**
     * The request method.
     *
     * @var string
     */
    protected $method;

    /**
     * The request path.
     *
     * @var string
     */
    protected $path;

    /**
     * The request options.
     *
     * @var array
     */
    protected $options = [];

    /**
     * Get the request method.
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Get the request path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get the request options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set the request method.
     *
     * @param string $method
     * @return $this
     */
    public function setMethod(string $method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Set the request path.
     *
     * @param string $path
     * @return $this
     */
    public function setPath(string $path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Set the request options.
     *
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Merge the request options.
     *
     * @param array $options
     * @return $this
     */
    public function mergeOptions(array $options)
    {
        $this->options = array_merge($this->options, $options);

        return $this;
    }

    /**
     * Make the request.
     *
     * @return array
     * @throws \Throwable
     */
    public function make()
    {
        try {
            $response = $this->client->request(
                $this->getMethod(),
                $this->getPath(),
                $this->getOptions()
            );

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            [$statusCode, $response] = $this->getException($e);

            throw new RequestException($e->getMessage(), $statusCode, $response, $e);
        } catch (Throwable $e) {
            throw $e;
        }
    }

    /**
     * Make the client given the arguments.
     *
     * @param string $baseUri
     * @param int $timeout
     * @return \GuzzleHttp\Client
     */
    protected function makeClient(string $baseUri, int $timeout)
    {
        return new Client([
            'base_uri' => $baseUri,
            'timeout' => $timeout,
        ]);
    }

    /**
     * Unpack the exception's status code and contents. Fallback to default values.
     *
     * @param \Throwable $e
     * @param int $statusCode
     * @param array $response
     * @return array
     */
    protected function getException(Throwable $e, $statusCode = 0, array $response = [])
    {
        if ($e->hasResponse()) {
            $statusCode = $e->getResponse()->getStatusCode();
            $contents = $e->getResponse()->getBody()->getContents();

            if (! empty($contents)) {
                $response = json_decode($contents, true);

                if (! is_array($response)) {
                    $response = ['contents' => $contents];
                }
            }
        }

        return [$statusCode, $response];
    }
}
