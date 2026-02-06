<?php

declare(strict_types=1);

namespace Phaze\Common\Exceptions;

/** Thrown when an expectation Phaze has of a Response is not met (e.g. missing header). */
class ResponseException extends PhazeException
{
}
