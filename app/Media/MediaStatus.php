<?php

declare(strict_types=1);

namespace App\Media;

/** What happened to one image. Three outcomes, the same for every media job. */
enum MediaStatus: string
{
    case Written = 'écrit';
    case Skipped = 'inchangé';
    case Failed = 'échec';
}
