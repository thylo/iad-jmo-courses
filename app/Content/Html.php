<?php

declare(strict_types=1);

namespace App\Content;

/**
 * Reading helpers for the content layer.
 *
 * There is no escaping here any more: nothing in PHP writes HTML, so nothing in
 * PHP has to escape it. A template does it, through {{ }}.
 */
final class Html
{
    /**
     * Reads a trimmed attribute.
     *
     * \Dom\Element::getAttribute() returns null for a missing attribute, where
     * the old DOMDocument returned an empty string. Going through here keeps
     * that away from the renderers.
     */
    public static function attribute(\Dom\Element $element, string $name): string
    {
        return trim($element->getAttribute($name) ?? '');
    }

    /**
     * An element's text as a single line: the indentation of the XML is not
     * part of what was written. Shared with XmlParser, which reads field values
     * the same way — a value and a description are both one line of prose.
     */
    public static function line(\Dom\Element $element): string
    {
        return trim(preg_replace('/\s+/u', ' ', $element->textContent) ?? '');
    }

    /**
     * Slug of a heading, using the same formula as tempest/markdown's HeadingRule,
     * so anchors coming from <section titre> and from ## headings are consistent.
     *
     * Deliberately not Tempest\Support\Str\to_slug(), which transliterates to
     * ASCII and would break that parity on accented headings.
     */
    public static function id(string $heading): string
    {
        $slug = preg_replace('/[^\p{L}\p{N}]+/u', '-', mb_strtolower($heading)) ?? '';

        return trim($slug, '-');
    }
}
