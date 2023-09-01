<?php

namespace KrystalCode\Api\Session;

/**
 * The interface for the session storages that support garbage collection.
 *
 * API providers commonly clean up sessions that are not used for a certain
 * period of time. Access tokens expire after some time as well and API
 * providers reject requests that use expired tokens. For access tokens - which
 * is the currently supported authentication method - applications should also
 * check whether a given token has expired before issuing requests and request a
 * new one if it has.
 *
 * When possible, however, applications should be either deleting sessions when
 * they finished with their requests, or setting them to expire at the
 * expiration time given by the API provider for access tokens or as needed
 * for cookie authentication. This way, subsequent calls will know whether there
 * is a valid session and create a new one if not, instead of performing
 * unnecessary validations or calls using an expired session and handling the
 * rejecting response before creating a new one.
 *
 * This interface provides a method for scheduling a session to expire. How the
 * expiration is actually done highly depends on the type of storage and the
 * application; it is therefore left for the actual storage implementation.
 *
 * The statement that "applications should be either deleting sessions when they
 * finished with their requests, or setting them to expire" might not be true if
 * the application maintains only one access token for all its requests i.e. one
 * session. In such cases there is no risk of hitting session limits and it is
 * not necessary to delete sessions.
 *
 * @see \KrystalCode\Api\Session\SessionInterface
 * @see \KrystalCode\Api\Session\SessionWithExpirationInterface
 */
interface SessionStorageWithGarbageCollectionInterface
{
    /**
     * Schedules the session with the given type ID for expiration.
     *
     * This method does not need to return anything nor throw an exception if
     * there is no session with the given type ID.
     *
     * @param string $typeId
     *   The session type ID.
     * @param int $interval
     *   The time interval in seconds from the present moment that the session
     *   should expire at.
     *
     * @throws \RuntimeException
     *   When the session of the given type ID does not support scheduling its
     *   expiration.
     */
    public function expire(
        string $typeId = SessionInterface::SESSION_TYPE_ID_DEFAULT,
        int $interval = 0
    ): void;

    /**
     * Deletes expired sessions of the given type, or all if no type given.
     *
     * @param string|null $typeId
     *   The session type ID, or `null` to delete all expired sessions of all
     *   types.
     */
    public function deleteExpired(string $typeId = null): void;
}
