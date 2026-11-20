<?php

namespace App\Services;

use App\Models\{User, Team};
use Psr\Log\LoggerInterface;

class Mailer
{
    public function __construct(
        private LoggerInterface $logger,
        private ?Transport $transport = null,
    ) {
    }

    public function send(User|Team $to, Message&Signed $message, int $retries = DEFAULT_RETRIES, string $mode = Mode::FAST): ?Receipt
    {
        return null;
    }

    public function variadic(Attachment ...$attachments): static
    {
        return $this;
    }

    public function scalars(int $a, string $b, array $c, callable $d, iterable $e, mixed $f, object $g): self
    {
        return $this;
    }
}
