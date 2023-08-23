<?php

declare(strict_types=1);

namespace KrystalCode\Api\Session\OAuth2;

use KrystalCode\Api\Session\AccessTokenSession;
use KrystalCode\Api\Session\Exception\Connection as ConnectionException;
use KrystalCode\Api\Session\SessionInterface;
use KrystalCode\Api\Session\SessionManagerInterface;
use KrystalCode\Api\Session\SessionStorageInterface;
// Third-party libraries.
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessTokenInterface;

/**
 * Provides a session manager that uses Resource Owner OAuth2 flow.
 *
 * That is, the Resource Owner Password Credentials grant.
 *
 * Currently, this session manager supports maintaining one session with one
 * access token. The session and its access token can be reused as many times as
 * needed by multiple processes until it expires. When it expires - or when it
 * has less time to live than what is defined in configuration - a new access
 * token is requested. The API provider may consider the new access token as a
 * new session, but for our purposes we consider this as refreshing and
 * continuing the same session object.
 *
 * Some API providers limit the number of simultaneous sessions that can exist.
 * This implementation therefore ensures that there will only exist one active
 * session that multiple processes can safely use.
 *
 * Support for maintaining multiple concurrent sessions could be added, here or
 * in a separate session manager.
 *
 * Hitting API rate limits that restrict the number of concurrent or queued
 * requests that can be made can be avoided by spacing out requests by the same
 * process. This is the responsibility of the API client.
 */
class PasswordSessionManager implements SessionManagerInterface
{
    /**
     * Constructs a new PasswordSessionManager.
     *
     * @param \League\OAuth2\Client\Provider\AbstractProvider $provider
     *   The OAuth2 service provider.
     * @param \KrystalCode\Api\Session\SessionStorageInterface $sessionStorage
     *   The session storage.
     * @param array $options
     *   An assocative array of options. For supported options see
     *   `\KrystalCode\Acumatica\Api\Session\SessionManagerInterface::connect()`.
     *   Additional options added here are:
     *   - grant (array, required): An associative array containing information
     *     that the provider needs to request an access token. Supported array
     *     items are:
     *     - username (string, required): The username.
     *     - password (string, required): The password.
     *     - scope (string, optional): The scope. Might be required by some
     *       authorization servers.
     *   - type_id (string, optional): The ID of the session type. If not
     *     provided, the default type ID will be used. Leave to the default only
     *     if there is and will ever by only one session type throughout the
     *     application managed by this or other session managers.
     *
     * @see \KrystalCode\Api\Session\SessionInterface::SESSION_TYPE_ID_DEFAULT
     */
    public function __construct(
        protected AbstractProvider $provider,
        protected SessionStorageInterface $sessionStorage,
        protected array $options = []
    ) {
        $this->setOptions($options);
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions(array $options): void
    {
        $this->options = array_merge(
            $this->getDefaultOptions(),
            $options
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * {@inheritdoc}
     */
    public function connect(): SessionInterface
    {
        $this->validateDuration();

        // We always use one session with the session type ID provided in the
        // options. If we don't already have a session of this type, create one.
        $session = $this->sessionStorage->get($this->options['type_id']);

        // If we do not have an existing session, create a new one.
        if ($session === null) {
            return $this->createOrUpdateSession();
        }

        $token = $session->getAccessToken();

        // If the existing session has expired already, create a new one.
        if ($token->hasExpired()) {
            return $this->createOrUpdateSession($session);
        }

        // If the existing session hasn't expired yet, check if it has
        // sufficient time left.
        if ($this->hasTimeToExpire($token)) {
            return $session;
        }

        // If it does not, create a new one.
        return $this->createOrUpdateSession($session);
    }

    /**
     * Creates or updates an existing session by requesting a new access token.
     *
     * @param \KrystalCode\Acumatica\Api\Session\SessionInterface|null $session
     *   The session to update, or `null` to create a new one.
     *
     * @return \KrystalCode\Api\Session\SessionInterface
     *   The session object.
     *
     * @throws \KrystalCode\Api\Session\Exception\Connection
     *   When the requested session duration could not be guaranteed.
     *
     * @I Convert other exceptions to Connection exceptions
     *    type     : bug
     *    priority : normal
     *    labels   : manager
     *    notes    : To comply with the interface so that callers can properly
     *               catch exceptions by the expetected class.
     */
    protected function createOrUpdateSession(
        SessionInterface $session = null
    ): SessionInterface {
        $token = $this->authenticate();

        if (!$this->hasTimeToExpire($token)) {
            throw new ConnectionException(sprintf(
                'The token issued by the provider has an expiration life shorter than the requested session duration.'
            ));
        }

        if ($session === null) {
            $session = new AccessTokenSession($token, $this->options['type_id']);
        }
        else {
            $session->setAccessToken($token);
        }

        $this->sessionStorage->set($session);
        return $session;
    }

    /**
     * It authenticates to the authorization server and gets a new access token.
     *
     * @return \League\OAuth2\Client\Token\AccessTokenInterface
     *   The access token.
     *
     * @throws \InvalidArgumentException
     *   When the grant options are missing required options.
     */
    protected function authenticate(): AccessTokenInterface
    {
        $this->validateGrantOptions();

        return $this->provider->getAccessToken(
            'password',
            $this->options['grant']
        );
    }

    /**
     * Returns whether the token has at least the requested time till expiration.
     *
     * The requested time is defined in the options, as seconds.
     *
     * @param \League\OAuth2\Client\Token\AccessTokenInterface $token
     *   The access token.
     */
    protected function hasTimeToExpire(
        AccessTokenInterface $token
    ): bool {
        // If a session duration is not requested, we consider that the token has
        // sufficient time i.e. the requested duration is 0 seconds.
        if (!isset($this->options['duration']['interval'])) {
            return true;
        }

        return $token->getExpires() >= (
            $this->options['duration']['start'] + $this->options['duration']['interval']
        );
    }

    /**
     * Checks that the given duration options are valid.
     *
     * @throws \InvalidArgumentException
     *   When a minimum session duration is given but the session start time is
     *   not.
     * @throws \KrystalCode\Acumatica\Api\Exception\Connection
     *   When the given duration exceeds the maximum supported by this session
     *   manager.
     */
    protected function validateDuration(): void
    {
        $duration = &$this->options['duration'];

        if (!isset($duration['interval'])) {
            return;
        }

        if (!isset($duration['start'])) {
            throw new ConnectionException(
                'The start time must be defined when a session duration is given.'
            );
        }

        if (!isset($duration['limit'])) {
            return;
        }
        if ($duration['interval'] <= $duration['limit']) {
            return;
        }

        throw new ConnectionException(sprintf(
            '"%s" exceeds the maximum supported session duration of "%s" seconds.',
            $duration['interval'],
            $duration['limit']
        ));
    }

    /**
     * Validates the grant options.
     *
     * @throws \InvalidArgumentException
     *   When one or more required properties are missing from the grant options.
     */
    protected function validateGrantOptions(): void
    {
        $required_options = [
            'username',
            'password',
        ];
        foreach ($required_options as $option) {
            if (isset($this->options['grant'][$option])) {
                continue;
            }

            throw new \InvalidArgumentException(sprintf(
                'The "%s" grant option is required.',
                $option
            ));
        }
    }

    /**
     * Returns the default options.
     *
     * @return array
     *   An array containing the default options.
     */
    protected function getDefaultOptions(): array
    {
        return [
            'duration' => [
                'interval' => null,
                'limit' => null,
                'start' => time(),
            ],
            'grant' => [],
            'type_id' => null,
        ];
    }
}
