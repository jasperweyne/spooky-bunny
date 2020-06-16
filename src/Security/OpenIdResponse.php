<?php

namespace App\Security;

use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use OpenIDConnectServer\IdTokenResponse;

class OpenIdResponse extends IdTokenResponse
{
    protected function getExtraParams(AccessTokenEntityInterface $accessToken)
    {
        $scopeNames = json_decode(json_encode($accessToken->getScopes()));

        $array = parent::getExtraParams($accessToken);
        $array['scope'] = implode(' ', $scopeNames);
        return $array;
    }
}