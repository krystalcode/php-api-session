<?php

declare(strict_types=1);

namespace KrystalCode\Api\Session\OAuth2;

// This library.
use KrystalCode\Api\Session\SessionInterface;
// Third-party libraries.
use League\OAuth2\Client\Token\AccessTokenInterface;

/**
 * Provides a session manager that refreshes an existing token.
 *
 * That is, the Refresh Token grant.
 *
 * Additional options supported on top of what is provided by the parent class,
 * are:
 * - grant (array, required): An associative array containing information that
 *   the provider needs to request an access token. Supported array items are:
 *   - refresh_token (string, optional): The refresh token that will be used to
 *     get a new access token. If not provided, a refresh token should be made
 *     available by the access token stored in the session for the given session
 *     type ID.
 *   - scope (string, optional): The scope. Might be required by some
 *     authorization servers.
 */
class RefreshTokenSessionManager extends SessionManagerBase
{
    /**
     * {@inheritdoc}
     */
    public function setOptions(array $options): void
    {
        parent::setOptions($options);

        if (isset($this->options['grant']['refresh_token'])) {
            return;
        }

        $session = $this->sessionStorage->get(
            $this->options['type_id'] ?? SessionInterface::SESSION_TYPE_ID_DEFAULT
        );
        if ($session === null) {
            throw new \RuntimeException(
                'No refresh token was provided and no existing access token found to be refreshed.'
            );
        }

        $refresh_token = $session->getAccessToken()->getRefreshToken();
        if (!$refresh_token) {
            throw new \RuntimeException(
                'No refresh token found in the existing access token being refreshed.'
            );
        }

        $this->options['grant']['refresh_token'] = $refresh_token;
    }

    /**
     * {@inheritdoc}
     */
    protected function getAccessToken(): AccessTokenInterface
    {
        return $this->provider->getAccessToken(
            'refresh_token',
            $this->options['grant']
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getRequiredGrantOptions(): array
    {
        return ['refresh_token'];
    }
}
