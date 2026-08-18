<?php

declare(strict_types=1);

namespace App\Content;

/** Escaping helpers, so nothing that renders has to remember the flags. */
final class Html
{
    /**
     * Escapes text and attribute values alike.
     *
     * Apostrophes are left alone — every attribute we emit is double-quoted, and
     * escaping them turns readable French prose into &#039; soup.
     */
    public static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_COMPAT | ENT_SUBSTITUTE, 'UTF-8');
    }

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
