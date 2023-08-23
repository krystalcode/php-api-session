<?php

namespace KrystalCode\Api\Session;

/**
 * The interface for session storage implementations.
 *
 * API providers require an access token or cookie to be used in each request
 * for authentication. Applications are therefore required to store those so
 * API calls that belong to the same session can reuse them. This interface
 * provides an abstraction layer for the session storage.
 *
 * This library is agnostic to the application and the data stores it uses; it
 * therefore does not provide a session storage implementation. It needs to be
 * provided by the application.
 *
 * Depending on the data store used, it may be a good idea to store all session
 * values serialized instead of just the token/cookie value. More values may be
 * added, such as creation and expiration timestamps, instance or API ID for
 * for applications that work with multiple API providers, or even username for
 * applications that connect with different credentials on behalf of different
 * users.
 *
 * This interface provides methods that should be transparent to the application
 * when it comes to session expiration i.e. all expired sessions should be
 * treated by these methods as if they don't exist. If the application needs to
 * be aware of expired sessions, the session storage needs to implement the
 * related interface.
 *
 * @see \KrystalCode\Api\Session\SessionInterface
 * @see \KrystalCode\Api\Session\SupportsExpirationSessionStorageInterface
 */
interface SessionStorageInterface
{
    /**
     * Sets the given session in the storage.
     *
     * If a session with the given type ID already exists, it will be
     * overridden.
     *
     * @param \KrystalCode\Api\Session\SessionInterface $session
     *   The session to store.
     */
    public function set(SessionInterface $session): void;

    /**
     * Returns the session for the given type ID.
     *
     * `null` should be returned for expired sessions.
     *
     * @param string $typeId
     *   The session type ID.
     *
     * @return \KrystalCode\Api\Session\SessionInterface|null
     *   The session, or `null` if there is no session for the given type ID, or
     *   if there is a session but it has expired.
     */
    public function get(
        string $typeId = self::SESSION_TYPE_ID_DEFAULT
    ): SessionInterface|null;

    /**
     * Returns whether a session with given type ID exists.
     *
     * `false` should be returned for expired sessions.
     *
     * @param string $typeId
     *   The session type ID.
     *
     * @return bool
     *   `true` if the session exists, `false` otherwise.
     */
    public function exists(
        string $typeId = self::SESSION_TYPE_ID_DEFAULT
    ): bool;

    /**
     * Deletes the session with the given type ID.
     *
     * This method does not need to return anything nor throw an exception if
     * there is no session with the given type ID.
     *
     * @param string $typeId
     *   The session type ID.
     */
    public function delete(
        string $typeId = self::SESSION_TYPE_ID_DEFAULT
    ): void;

    /**
     * Returns the number of existing sessions.
     *
     * Expired sessions should not be counted.
     *
     * @return int
     *   The number of existing sessions.
     */
    public function getCount(): int;
}
