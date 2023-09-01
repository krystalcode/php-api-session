<?php

namespace KrystalCode\Api\Session\OAuth2;

use KrystalCode\Api\Session\SessionInterface;
use League\OAuth2\Client\Token\AccessToken;

/**
 * The interface for access token sessions.
 */
interface AccessTokenSessionInterface extends
    SessionInterface,
    SessionWithExpirationInterface
{
    /**
     * Sets the access token.
     *
     * Normally the access token should be provided when creating a session. A
     * new access token would be considered a new session and a new session
     * object should be created. However, in cases where for the purposes of the
     * application it is considered that we maintain one session and getting a
     * new access token is considered as renewing the existing session, we
     * provide this setter so that the existing session can be updated.
     *
     * @param \League\OAuth2\Client\Token\AccessToken $accessToken
     *   The access token.
     */
    public function setAccessToken(AccessToken $accessToken): void;

    /**
     * Returns the access token.
     *
     * @return \League\OAuth2\Client\Token\AccessToken
     *   The access token.
     */
    public function getAccessToken(): AccessToken;
}
