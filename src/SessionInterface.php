<?php

namespace KrystalCode\Api\Session;

/**
 * The interface for API client sessions.
 *
 * This session is designed for clients that require maintaining an access token
 * or cookie and using it in each request for authentication in API calls.
 * Applications can either use one single session, or create multiple sessions
 * to facilitate more complex cases e.g. maintain different sessions for
 * different processes.
 *
 * In the case of maintaining multiple sessions for different processes, the
 * session type ID should be used as an indicator of the purpose of the session.
 * For exampe, an application that integrates with an ERP/CRM system, regularly
 * performs user imports/exports, and wants to maintain separate sessions for
 * each one of these processes, could have two session types:
 * - `my_app.user_import`
 * - `my_app.user_export`
 *
 * The related processes would then request from the storage the session of the
 * corresponding type.
 *
 * Sessions unused for a certain period of time are commonly cleaned up by the
 * API provider, while access tokens expire after a given time as well. However,
 * it is recommended that sessions are properly closed and deleted/expired in
 * storage after the set of calls that it is opened for has finished. Leaving
 * sessions open can result in hitting session rate limits for some API
 * providers.
 *
 * See the session storage for more on deleting/expiring sessions.
 *
 * The type ID and other session values, such access token or cookie, should not
 * be changed after the session is created. If a session expires and a new 
 * session of the same type is needed, a new session object should be created
 * because it is essentially a new session. No setter methods are therefore
 * foreseen by the interface. Implementations should be requiring the values in
 * their constructors.
 *
 * An exception is when working with access tokens but considering them as one
 * session from the perspective of the application. In that case the access
 * token may be refreshed for the same session instead of creating a new one. 
 * Thus the access token session interface and implementation does provide a
 * setter for the access token.
 *
 * @see \KrystalCode\Api\Session\SessionStorageInterface
 * @see \KrystalCode\Api\Session\SupportsExpirationSessionStorageInterface
 * @see \KrystalCode\Api\Session\AccessTokenSessionInterface
 *
 * @I Introduce a session ID for uniquely identifying specific sessions
 *    type     : improvement
 *    priority : normal
 *    labels   : session
 *    notes    : Might be useful for logging sessions for debugging purposes.
 */
interface SessionInterface
{
    /**
     * The default session type ID.
     *
     * Provided for convenience for not needing to generate session type IDs for
     * applications that do not need to maintain multiple sessions.
     * Implementations can use it as the default value.
     *
     * Applications that do work with multiple session types should not use this
     * ID. Rather, they should create their own session type ID patterns using a
     * unique prefix to prevent conflicts. For example, `my_app.user_import`
     * or `my_app.user_export`.
     */
    public const SESSION_TYPE_ID_DEFAULT = 'default';

    /**
     * Returns the session type ID.
     *
     * @return string
     *   The session type ID.
     */
    public function getTypeId(): string;
}
