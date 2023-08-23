<?php

namespace KrystalCode\Api\Session\Exception;

/**
 * Exception for errors while establishing a connection for a session.
 *
 * Example errors that should throw this exception:
 * - Not being able to establish a connection.
 * - Connection rejected due to reaching session or other limits.
 * - Not being able to guarantee the requested session duration.
 */
class Connection extends \RuntimeException
{
}
