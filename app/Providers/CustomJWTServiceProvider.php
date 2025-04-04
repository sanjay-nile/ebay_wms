<?php

namespace App\Providers;

use Tymon\JWTAuth\Providers\JWT\Namshi as JWTProvider;
use Tymon\JWTAuth\PayloadFactory;

class CustomJWTServiceProvider extends JWTProvider
{
    public function createPayload(array $claims): PayloadFactory
    {
        if (isset($claims['iat'])) {
            $claims['iat'] = (new \DateTimeImmutable())->getTimestamp();
        }
        if (isset($claims['exp'])) {
            $claims['exp'] = (new \DateTimeImmutable())->getTimestamp();
        }
        if (isset($claims['nbf'])) {
            $claims['nbf'] = (new \DateTimeImmutable())->getTimestamp();
        }

        return parent::createPayload($claims);
    }
}
