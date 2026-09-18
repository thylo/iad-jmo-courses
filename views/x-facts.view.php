<?php
/**
 * The facts: an entity's typed fields, set as a list of pairs.
 *
 * A value is already an anchor or a piece of text when it gets here — a field
 * may point at an entity, at an outside URL, or at nothing but itself, and
 * which one it is belongs to the schema, not to this file.
 *
 * The loop is a plain foreach: a <dt> and its <dd> are siblings, so there is no
 * element to hang :foreach on that the grid in facts.css would survive.
 *
 * @var array<int, array{label: string, value: string}> $rows
 */
?>
<dl class="c-facts">
    <?php foreach ($rows as $row): ?>
        <dt>{{ $row['label'] }}</dt>
        <dd>{!! $row['value'] !!}</dd>
    <?php endforeach; ?>
</dl>
