<?php

namespace KrystalCode\Api\Session;

/**
 * Defines the interface for the session manager.
 *
 * The session manager is responsible for opening and managing sessions.
 * Currently, this involves:
 * - Authenticating into the remote system.
 * - Storing the session details.
 * - Providing the session details to API so that they can make calls with the
 *   credentials for the session.
 */
interface SessionManagerInterface
{
    /**
     * Sets the options for the session.
     *
     * Options that session managers should support are:
     * - duration (array, optional): An associative array containing information
     *   that instructs the session manager to ensure that sessions will have a
     *   minimum duration. That is, any authentication credentials e.g. access
     *   token, will be valid for at least the requested time. This is meant to
     *   be used, for example, in cases where several API calls will be made
     *   over a longer session and we want to minimize refreshing or recreating
     *   sessions to avoid exceeding session limits. Supported array items are:
     *   - interval (int, required): The minimum duration of the session.
     *   - limit (int, optional): The maximum duration of the session supported
     *     by the API provider, if known.
     *   - start (int, optional): The time, as Unix timestamp, from which the
     *     duration will start counting. Defaults to the current time as
     *     returned by PHP's `time()` function.
     *
     * Session manager implementations might support additional options, which
     * they should clearly document.
     *
     * @param array $options
     *   The options array.
     */
    public function setOptions(array $options): void;

    /**
     * Returns the options for the session.
     *
     * @return array
     *   The options array.
     */
    public function getOptions(): array;

    /**
     * Returns an active session.
     *
     * This method should do the following:
     * - Open a session i.e. request an access token or cookie ID, as needed by
     *   the authentication method being used by this session manager.
     * - Ensure that the session has sufficient time before its expiration
     *   respecting the requested time limit (see options).
     * - Optionally store the session details in storage, as required by the
     *   application.
     *
     * It returns the session object that contains the details required for API
     * clients to make calls as part of the session.
     *
     * @return KrystalCode\Api\Session\SessionInterface
     *   The session object.
     *
     * @throws \KrystalCode\Api\Session\Exception\Connection
     *   When a connection could not be established, or when the requested
     *   session duration could not be guaranteed.
     */
    public function connect(): SessionInterface;
}
