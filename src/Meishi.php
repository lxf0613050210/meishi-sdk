<?php

namespace Xiaofei\MeishiSDK;

use Hanson\Foundation\Foundation;

class Meishi extends Foundation
{
    protected $providers = [
        ServiceProvider::class
    ];

    public function __construct($config)
    {
        $config['debug'] = $config['debug'] ?? false;
        parent::__construct($config);
    }

    public function request(string $api, string $interfacename, array $params)
    {
        return $this->api->request($api, $interfacename, $params);
    }
}
