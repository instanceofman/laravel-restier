<?php


namespace Isofman\LaravelRestier;


use Throwable;

class NoLuckException extends \RuntimeException
{
    /**
     * @var array
     */
    protected $meta;

    public function __construct($message = "", $meta = [], $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->meta = $meta;
    }

    public function getErrorMeta(): array
    {
        return $this->meta;
    }
}