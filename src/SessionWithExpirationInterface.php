<?php

namespace KrystalCode\Api\Session;

/**
 * The interface for API client sessions that have an expiration time.
 *
 * We do not provide a setter method for the expiration time in this interface
 * because in some cases, such as in OAuth2 token-based sessions, the expiration
 * time is provided by the authorization server and it is normally not changed.
 * The token expires at the provided time no matter what.
 *
 * In other cases, such as in cookie-based sessions, the session may be kept
 * open until the client closes it. Depending on the application logic, there
 * may exist a need for setting such open sessions to expire. We may provide a
 * separate interface for those.
 *
 * @I Add interface for sessions with expiration set by the client
 *    type     : improvement
 *    priority : low
 *    labels   : cookie, session
 */
interface SessionWithExpirationInterface
{
    /**
     * Returns the expiration time as a Unix timestamp, in seconds.
     *
     * @return int|null
     *   The expiration time of the session, or `null` if not defined.
     */
    public function getExpired(): int|null;

    /**
     * Returns whether the session has expired.
     *
     * @return bool
     * `true` if the session has expired, `false` otherwise.
     */
    public function hasExpired();
}
