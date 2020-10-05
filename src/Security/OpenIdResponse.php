<?php

namespace App\Security;

use App\Repository\NonceRepository;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use OpenIDConnectServer\IdTokenResponse;
use OpenIDConnectServer\Repositories\IdentityProviderInterface;

class OpenIdResponse extends IdTokenResponse
{
    /**
     * @var String
     */
    protected $codeId;

    /**
     * @var NonceRepository
     */
    protected $nonceRepository;

    public function __construct(
        IdentityProviderInterface $identityProvider,
        ClaimExtractor $claimExtractor,
        NonceRepository $nonceRepository
    ) {
        $this->identityProvider = $identityProvider;
        $this->claimExtractor = $claimExtractor;
        $this->nonceRepository = $nonceRepository;
    }

    protected function getExtraParams(AccessTokenEntityInterface $accessToken)
    {
        $scopeNames = json_decode(json_encode($accessToken->getScopes()));

        $array = parent::getExtraParams($accessToken);
        $array['scope'] = implode(' ', $scopeNames);

        return $array;
    }

    protected function getBuilder(AccessTokenEntityInterface $accessToken, UserEntityInterface $userEntity)
    {
        $nonce = $this->nonceRepository->findByAuthCode($this->codeId);
        $builder = new CustomBuilder();

        // Add required id_token claims, nonce or no nonce.
        if (null != $nonce) {
            $builder->setAudience($accessToken->getClient()->getIdentifier())
                ->setIssuer('https://'.$_SERVER['HTTP_HOST'])
                ->setIssuedAt(time())
                ->setExpiration($accessToken->getExpiryDateTime()->getTimestamp())
                ->setSubject($userEntity->getIdentifier())
                ->nonce($nonce->getString());
        } else {
            $builder->setAudience($accessToken->getClient()->getIdentifier())
                ->setIssuer('https://'.$_SERVER['HTTP_HOST'])
                ->setIssuedAt(time())
                ->setExpiration($accessToken->getExpiryDateTime()->getTimestamp())
                ->setSubject($userEntity->getIdentifier())
            ;
        }

        return $builder;
    }

    public function setCodeId($codeId)
    {
        $this->codeId = $codeId;
    }
}
