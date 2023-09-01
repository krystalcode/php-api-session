<?php

declare(strict_types=1);

namespace KrystalCode\Api\Session\OAuth2;

// Third-party libraries.
use League\OAuth2\Client\Token\AccessTokenInterface;

/**
 * Provides a session manager that uses Resource Owner OAuth2 flow.
 *
 * That is, the Resource Owner Password Credentials grant.
 *
 * Additional options supported on top of what is provided by the parent class,
 * are:
 * - grant (array, required): An associative array containing information that
 *   the provider needs to request an access token. Supported array items are:
 *   - username (string, required): The username.
 *   - password (string, required): The password.
 *   - scope (string, optional): The scope. Might be required by some
 *     authorization servers.
 */
class PasswordSessionManager extends SessionManagerBase
{
    /**
     * {@inheritdoc}
     */
    protected function getAccessToken(): AccessTokenInterface
    {
        return $this->provider->getAccessToken(
            'password',
            $this->options['grant']
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getRequiredGrantOptions(): array
    {
        return [
            'username',
            'password',
        ];
    }
}
