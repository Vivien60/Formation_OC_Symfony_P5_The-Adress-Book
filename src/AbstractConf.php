<?php
declare(strict_types=1);

abstract class AbstractConf
{
    private static ?self $instance = null;
    public readonly array $_config;

    protected function __construct(array $config)
    {
        $this->_config = array_merge($this->defaultConfig(), $config);
    }

    public static function fromInstance(array $config = []): static
    {
        if(static::$instance !== null) {
            return static::$instance;
        }
        return new static($config);
    }

    protected abstract function defaultConfig(): array;

}