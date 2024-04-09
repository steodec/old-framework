<?php

namespace Humbrain\Framework\entity;

use DateTime;
use Exception;

trait Timestamp
{

    /**
     * @var DateTime|null
     */
    private ?DateTime $updatedAt;

    /**
     * @var DateTime|null
     */
    private ?DateTime $createdAt;

    /**
     * @return DateTime|null
     */
    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTime|string|null $datetime
     * @throws Exception
     */
    public function setUpdatedAt(DateTime|string|null $datetime): void
    {
        if (is_string($datetime)) {
            $this->updatedAt = new DateTime($datetime);
        } else {
            $this->updatedAt = $datetime;
        }
    }

    /**
     * @return DateTime|null
     */
    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime|string|null $datetime
     * @throws Exception
     */
    public function setCreatedAt(DateTime|string|null $datetime): void
    {
        if (is_string($datetime)) {
            $this->createdAt = new DateTime($datetime);
        } else {
            $this->createdAt = $datetime;
        }
    }
}
