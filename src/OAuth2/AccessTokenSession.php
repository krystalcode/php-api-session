<?php

declare(strict_types=1);

namespace KrystalCode\Api\Session\OAuth2;

use KrystalCode\Api\Session\SessionBase;
use League\OAuth2\Client\Token\AccessToken;

/**
 * Default implementation of the access token session.
 */
class AccessTokenSession extends SessionBase implements
    AccessTokenSessionInterface
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

    /**
     * {@inheritdoc}
     */
    public function getExpired(): int|null
    {
        return $this->accessToken->getExpired();
    }

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     *   When the expiration time is not defined in the access token.
     */
    public function hasExpired()
    {
        return $this->accessToken->hasExpired();
    }
}
