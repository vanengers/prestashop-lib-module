<?php

namespace Vanengers\PrestashopLibModule\Module\Abstract\Configuration;

class ConfigurationItem
{
    public string $name;
    public mixed $value;
    public array $shops;
    public bool $override;

    public function __construct(string $name, mixed $value, array $shops = [], bool $override = true)
    {
        $this->name = $name;
        $this->value = $value;
        $this->shops = $shops;
        $this->override = $override;
    }
}