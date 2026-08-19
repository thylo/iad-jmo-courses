<?php

declare(strict_types=1);

namespace App\Media\Sources;

use App\Content\Xml\XmlSource;

/** Somewhere an image of a work can be found. */
interface VisualSource
{
    /** Short name, shown in the report: "og:image", "youtube". */
    public function name(): string;

    public function look(XmlSource $entity): Attempt;
}
