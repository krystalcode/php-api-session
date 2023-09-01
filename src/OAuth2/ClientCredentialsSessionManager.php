<?php

declare(strict_types=1);

namespace KrystalCode\Api\Session\OAuth2;

// Third-party libraries.
use League\OAuth2\Client\Token\AccessTokenInterface;

/**
 * Provides a session manager that uses Client Credentials OAuth2 flow.
 *
 * That is, the Client Credentials grant.
 *
 * Additional options supported on top of what is provided by the parent class,
 * are:
 * - grant (array, optional): An associative array containing information that
 *   the provider needs to request an access token. Supported array items are:
 *   - scope (string, optional): The scope. Might be required by some
 *     authorization servers.
 */
class ClientCredentialsSessionManager extends SessionManagerBase
{
    /**
     * {@inheritdoc}
     */
    protected function getAccessToken(): AccessTokenInterface
    {
        return $this->provider->getAccessToken(
            'client_credentials',
            $this->options['grant']
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getRequiredGrantOptions(): array
    {
        return [];
    }
}
