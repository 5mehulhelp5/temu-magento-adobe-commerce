<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Listing\Ui;

class RuntimeStorage
{
    private \M2E\Temu\Model\Listing $listing;

    public function hasListing(): bool
    {
        return isset($this->listing);
    }

    public function setListing(\M2E\Temu\Model\Listing $listing): void
    {
        $this->listing = $listing;
    }

    public function getListing(): \M2E\Temu\Model\Listing
    {
        if (!$this->hasListing()) {
            throw new \LogicException('Listing was not initialized.');
        }

        return $this->listing;
    }
}
