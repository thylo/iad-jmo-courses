<?php

declare(strict_types=1);

namespace App\Content\Xml;

use App\Content\Html;

/** The one place that turns an entity id into an anchor. */
final class EntityLink
{
    public static function html(ContentIndex $index, string $id, ?string $label = null): string
    {
        $target = $index->find($id);

        // Unresolved is not fatal: you write before you create the target.
        // The anchor stays in the flow, without href, and content:check reports it.
        if ($target === null) {
            return sprintf('<a class="lien-manquant">%s</a>', Html::escape($label ?? $id));
        }

        return sprintf(
            '<a href="%s">%s</a>',
            Html::escape($target->slug),
            Html::escape($label ?? $target->title),
        );
    }
}
