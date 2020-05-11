<?php

namespace App\Security;

use OpenIDConnectServer\ClaimExtractor as NativeClaimExtractor;

class ClaimExtractor extends NativeClaimExtractor
{
    /**
     * @return \OpenIDConnectServer\Entities\ClaimSetInterface[] $claimSets
     */
    public function getClaimSets()
    {
        return $this->claimSets;
    }
}
