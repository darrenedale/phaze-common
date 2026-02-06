<?php

declare(strict_types=1);

namespace Phaze\Common\Utilities\DateTime;

use DateTimeImmutable;
use DateTimeInterface;
use Phaze\Common\Constants;

function formatForHeader(DateTimeInterface $dateTime): string
{
    if (0 !== $dateTime->getOffset()) {
        $dateTime = DateTimeImmutable::createFromFormat("U", (string) $dateTime->getTimestamp());
    }

    return $dateTime->format(Constants::DateTimeFormat);
}

function currentDateTimeForHeader(): string
{
    return formatForHeader(new DateTimeImmutable("now"));
}
