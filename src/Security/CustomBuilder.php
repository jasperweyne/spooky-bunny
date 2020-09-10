<?php

namespace App\Security;

use Lcobucci\JWT\Builder;

class CustomBuilder extends Builder
{
    /**
     * Configures the nonce.
     *
     * @param string $nonceValue
     * @param bool   $replicateAsHeader
     *
     * @return Builder
     */
    public function nonce($nonceValue, $replicateAsHeader = false)
    {
        return $this->setRegisteredClaim('nonce', (string) $nonceValue, $replicateAsHeader);
    }
}
