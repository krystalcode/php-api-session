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
 * @see \KrystalCode\Api\Session\SessionStorageWithGarbageCollectionInterface
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
     * @param string $typeId
     *   The session type ID.
     * @param bool $ignore_expired
     *   `true` to treat expired sessions as if they do not exist.
     *
     * @return \KrystalCode\Api\Session\SessionInterface|null
     *   The session, or `null` in the following cases:
     *   - If the session has expired and `$ignore_expired` is `true`.
     *   - If there is no session for the given type ID.
     */
    public function get(
        string $typeId = SessionInterface::SESSION_TYPE_ID_DEFAULT,
        bool $ignore_expired = true
    ): SessionInterface|null;

    /**
     * Returns whether a session with given type ID exists.
     *
     * @param string $typeId
     *   The session type ID.
     * @param bool $ignore_expired
     *   `true` to treat expired sessions as if they do not exist.
     *
     * @return bool
     *   `true` in the following cases:
     *   - The session exists and has not expired.
     *   - The session exists, has expired, and `$ignore_expired` is `false`.
     *   `false` in the following cases:
     *   - The session exists, has expired, and `$ignore_expired` is `true`.
     *   - There is no session for the given type ID.
     */
    public function exists(
        string $typeId = SessionInterface::SESSION_TYPE_ID_DEFAULT,
        bool $ignore_expired = true
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
     * @param bool $ignore_expired
     *   `true` to treat expired sessions as if they do not exist.
     *
     * @return int
     *   The number of existing sessions, including or excluding expired session
     *   depending on the value of `$ignore_expired`.
     */
    public function count(bool $ignore_expired = true): int;
}
