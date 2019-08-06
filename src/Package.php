<?php

namespace Isofman\LaravelRestier;

use Closure;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use RuntimeException;

/**
 * Class Package
 * @package App\Libraries\Aliniex\Http
 */
class Package
{
    /**
     * @var \Requests_Response
     */
    protected $request;

    /**
     * @array
     */
    protected $data;

    /**
     * @var array
     */
    protected $payload;

    /**
     * Package constructor.
     * @param \Requests_Response $request
     */
    public function __construct(\Requests_Response $request)
    {
        $this->request = $request;
    }

    /**
     * @param $payload
     * @return Package
     */
    public function attachPayload($payload)
    {
        $this->payload = $payload;
        return $this;
    }

    /**
     * @return $this
     * @throws RuntimeException
     */
    public function mustSuccess()
    {
        if ($this->isNotSuccess()) {
            throw new NoLuckException(
                $this->getErrorMessage(),
                [
                    'url' => $this->request->url,
                    'payload' => $this->payload,
                    'raw_output' => $this->getRawBody(),
                    'code' => $this->request->status_code,
                ]
            );
        }

        return $this;
    }

    /**
     * @param $prop
     * @return $this
     * @throws RuntimeException
     */
    public function mustHas($prop)
    {
        $this->mustSuccess();

        if (!$this->hasProp($prop)) {
            throw new RuntimeException(
                "Service response without prop",
                [
                    'url' => $this->request->url,
                    'response' => $this->getRawData()
                ]
            );
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return $this->request->success;
    }

    /**
    * @return bool
    */
    public function isNotSuccess()
    {
        return ! $this->isSuccess();
    }

    /**
     * @return mixed
     */
    public function getErrorMessage()
    {
        return $this->getData('error') ?? 'Error while calling service';
    }

    /**
     * @param $prop
     * @return bool
     */
    public function hasProp($prop)
    {
        $this->getData();
        return Arr::has($this->data, $prop);
    }

    /**
     * @param $path
     * @return mixed
     */
    protected function queryData($path)
    {
        return Arr::get($this->data, $path);
    }

    /**
     * @return string
     */
    public function getRawData()
    {
        return $this->request->body;
    }

    public function getRawBody()
    {
        return $this->request->raw;
    }

    /**
     * @param null $path
     * @return mixed
     */
    public function getData($path = null)
    {
        if (empty($this->data)) {
            $this->data = json_decode($this->request->body, true);
        }

        return $path ? $this->queryData($path) : $this->data;
    }

    protected function executeProcessor($processor)
    {
        if (is_string($processor)) {
            $processor($this->getData());
        } else if ($processor instanceof Closure) {
            $processor($this);
        } else if (is_callable($processor)) {
            is_array($processor) ? call_user_func($processor, $this) : $processor($this);
        } else {
            throw new InvalidArgumentException();
        }
    }

    protected function executeProcessorOnError($processor)
    {
        $error = new NoLuckException(
            $this->getErrorMessage(),
            [
                'url' => $this->request->url,
                'payload' => $this->payload,
                'raw_output' => $this->getRawBody(),
                'code' => $this->request->status_code,
            ]
        );

        if (is_string($processor)) {
            $processor($error->getMessage());
        } else if ($processor instanceof Closure) {
            $processor($error);
        } else if (is_callable($processor)) {
            is_array($processor) ? call_user_func($processor, $error) : $processor($error);
        } else {
            throw new InvalidArgumentException();
        }
    }

    protected function executeFormatter($value, $processor)
    {
        if (is_string($processor)) {
            return $processor($value);
        } else if ($processor instanceof Closure) {
            return $processor($value);
        } else if (is_callable($processor)) {
            return is_array($processor) ? call_user_func($processor, $value) : $processor($value);
        } else {
            throw new InvalidArgumentException();
        }
    }

    public function then($processor)
    {
        if (!$this->isSuccess()) {
            return $this;
        }

        $this->executeProcessor($processor);

        return $this;
    }

    public function catch($processor)
    {
        if ($this->isSuccess()) {
            return $this;
        }

        $this->executeProcessorOnError($processor);

        return $this;
    }

    public function format($processor)
    {
        return $this->executeFormatter($this->getData(), $processor);
    }

    public function formatField($field, $processor)
    {
        return $this->executeFormatter(
            $this->getData($field),
            $processor
        );
    }
}