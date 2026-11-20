<?php

namespace App\Concerns;

trait HasUuid
{
    use GeneratesIds, Sub\Tracks;

    public function uuid(): Uuid
    {
        return Uuid::v4();
    }
}
