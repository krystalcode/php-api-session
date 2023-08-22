<?php

declare(strict_types=1);

namespace KrystalCode\Api\Session\OAuth2;

use League\OAuth2\Client\Token\AccessToken;

/**
 * Default implementation of the access token session.
 */
class AccessTokenSession extends SessionBase
{
    /**
     * Constructs a new AccessTokenSession object.
     *
     * @param \League\OAuth2\Client\Token\AccessToken $accessToken
     *   The access token.
     * @param string $typeId
     *   The session type ID.
     */
    public function __construct(
        protected AccessToken $accessToken,
        protected string $typeId = SessionInterface::SESSION_TYPE_ID_DEFAULT
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function setAccessToken(AccessToken $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessToken(): AccessToken
    {
        return $this->accessToken;
    }
}
