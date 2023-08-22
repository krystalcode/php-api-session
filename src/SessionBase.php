<?php

declare(strict_types=1);

namespace KrystalCode\Api\Session;

/**
 * Base class that facilitates session implementations.
 */
abstract class SessionBase implements SessionInterface
{
    /**
     * Constructs a new Session object.
     *
     * @param string $typeId
     *   The session type ID.
     */
    public function __construct(
        protected string $typeId = SessionInterface::SESSION_TYPE_ID_DEFAULT
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeId(): string
    {
        return $this->typeId;
    }
}
