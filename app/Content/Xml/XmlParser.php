<?php

declare(strict_types=1);

namespace App\Content\Xml;

use App\Content\ContentException;
use App\Content\Html;

/**
 * Reads one .xml file into an XmlSource, refusing anything the schema does not allow.
 *
 * Parsing never renders. The whole index is built from what this returns, so a
 * page may reference an entity that has not been parsed yet.
 *
 * Error messages are in French and always carry file and line: a content error
 * has to be fixable without opening the code.
 */
final readonly class XmlParser
{
    private const string ID_PATTERN = '/^[a-z0-9]+(?:-[a-z0-9]+)*$/';

    public function __construct(
        private SchemaRegistry $schemas,
        private ElementRegistry $elements,
    ) {}

    public function parse(string $path, string $slug): XmlSource
    {
        $root = $this->load($path)->documentElement;
        $type = $root->localName;

        $schema = $this->schemas->get($type) ?? throw ContentException::at(
            $path,
            sprintf('Type « %s » inconnu. Types connus : %s.', $type, implode(', ', $this->schemas->types())),
            $root->getLineNo(),
        );

        $this->checkAttributes($path, $root, $schema);
        $this->checkRoot($path, $root, $schema);

        $data = $this->collect($root, $schema);
        $this->checkRequired($path, $root, $schema, $data);

        return new XmlSource(
            path: $path,
            slug: $slug,
            type: $type,
            id: $this->id($path, $root),
            title: $data['titre'][0],
            summary: $data['resume'][0] ?? null,
            root: $root,
            data: $data,
            references: $this->references($root, $schema, $data),
        );
    }

    private function load(string $path): \Dom\XMLDocument
    {
        $previous = libxml_use_internal_errors(true);
        libxml_clear_errors();

        try {
            return \Dom\XMLDocument::createFromString((string) file_get_contents($path));
        } catch (\Throwable) {
            $error = libxml_get_errors()[0] ?? null;

            throw ContentException::at(
                $path,
                'XML mal formé : ' . trim($error?->message ?? 'cause inconnue'),
                $error?->line,
            );
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }
    }

    private function id(string $path, \Dom\Element $root): string
    {
        $id = Html::attribute($root, 'id');

        if ($id === '') {
            throw ContentException::at($path, 'Attribut « id » manquant sur la racine.', $root->getLineNo());
        }

        if (preg_match(self::ID_PATTERN, $id) !== 1) {
            throw ContentException::at(
                $path,
                sprintf('id « %s » invalide : minuscules, chiffres et tirets uniquement.', $id),
                $root->getLineNo(),
            );
        }

        return $id;
    }

    private function checkAttributes(string $path, \Dom\Element $root, Schema $schema): void
    {
        foreach ($root->attributes as $attribute) {
            if ($attribute->name === 'id') {
                continue;
            }

            if (! array_key_exists($attribute->name, $schema->attributes)) {
                throw ContentException::at(
                    $path,
                    sprintf(
                        'Attribut « %s » inconnu sur <%s>. Attendus : id, %s.',
                        $attribute->name,
                        $schema->type,
                        implode(', ', array_keys($schema->attributes)),
                    ),
                    $root->getLineNo(),
                );
            }

            $allowed = $schema->attributes[$attribute->name];

            if ($allowed !== null && ! in_array($attribute->value, $allowed, true)) {
                throw ContentException::at(
                    $path,
                    sprintf(
                        '%s="%s" : valeur non prévue. Attendues : %s.',
                        $attribute->name,
                        $attribute->value,
                        implode(' | ', $allowed),
                    ),
                    $root->getLineNo(),
                );
            }
        }
    }

    /** The root accepts data fields and blocks. */
    private function checkRoot(string $path, \Dom\Element $root, Schema $schema): void
    {
        $seen = [];

        foreach ($this->elementChildren($path, $root) as $node) {
            $name = $node->localName;

            if ($this->elements->knows($name)) {
                $this->checkBlocks($path, $node);

                continue;
            }

            $field = $schema->field($name) ?? throw ContentException::at(
                $path,
                sprintf(
                    'Élément <%s> inconnu pour le type « %s ». Champs : %s. Blocs : %s.',
                    $name,
                    $schema->type,
                    implode(', ', array_keys($schema->fields)),
                    implode(', ', $this->elements->names()),
                ),
                $node->getLineNo(),
            );

            if (! $field->repeatable && isset($seen[$name])) {
                throw ContentException::at($path, sprintf('<%s> ne peut apparaître qu\'une fois.', $name), $node->getLineNo());
            }

            if ($field->isReference && Html::attribute($node, 'ref') === '') {
                throw ContentException::at($path, sprintf('<%s> attend un attribut ref.', $name), $node->getLineNo());
            }

            $seen[$name] = true;
        }
    }

    /** Below the root, only rendering elements are allowed. */
    private function checkBlocks(string $path, \Dom\Element $parent): void
    {
        // <markdown> is opaque on purpose: its content is prose, not a tree.
        if ($parent->localName === 'markdown') {
            return;
        }

        foreach ($this->elementChildren($path, $parent) as $node) {
            if (! $this->elements->knows($node->localName)) {
                throw ContentException::at(
                    $path,
                    sprintf('Élément <%s> inconnu ici. Blocs : %s.', $node->localName, implode(', ', $this->elements->names())),
                    $node->getLineNo(),
                );
            }

            $this->checkBlocks($path, $node);
        }
    }

    /** @param array<string, string[]> $data */
    private function checkRequired(string $path, \Dom\Element $root, Schema $schema, array $data): void
    {
        foreach ($schema->fields as $field) {
            if ($field->required && ($data[$field->name] ?? []) === []) {
                throw ContentException::at($path, sprintf('<%s> est obligatoire et ne peut pas être vide.', $field->name), $root->getLineNo());
            }
        }
    }

    /**
     * Element children, refusing loose prose along the way.
     *
     * @return iterable<\Dom\Element>
     */
    private function elementChildren(string $path, \Dom\Element $parent): iterable
    {
        foreach ($parent->childNodes as $node) {
            if ($node instanceof \Dom\Element) {
                yield $node;

                continue;
            }

            if (trim($node->textContent) !== '') {
                throw ContentException::at(
                    $path,
                    sprintf('Du texte hors balise dans <%s>. La prose se met dans <markdown>.', $parent->localName),
                    $node->getLineNo(),
                );
            }
        }
    }

    /** @return array<string, string[]> */
    private function collect(\Dom\Element $root, Schema $schema): array
    {
        $data = [];

        foreach ($root->children as $child) {
            $field = $schema->field($child->localName);

            if ($field === null) {
                continue;
            }

            $value = $field->isReference ? Html::attribute($child, 'ref') : $this->text($child);

            if ($value !== '') {
                $data[$field->name][] = $value;
            }
        }

        return $data;
    }

    /**
     * Every id this entity points at: ref= attributes and [[wikilinks]] alike.
     *
     * Wikilinks are read straight from the <markdown> source, so backlinks exist
     * before anything is rendered.
     *
     * @param array<string, string[]> $data
     * @return string[]
     */
    private function references(\Dom\Element $root, Schema $schema, array $data): array
    {
        $references = [];

        foreach ($schema->fields as $field) {
            if ($field->isReference) {
                $references = [...$references, ...($data[$field->name] ?? [])];
            }
        }

        foreach ($root->getElementsByTagName('markdown') as $markdown) {
            $references = [...$references, ...WikiLinks::targets($markdown->textContent)];
        }

        return array_values(array_unique($references));
    }

    /** Field values are single-line: the XML indentation is not part of the value. */
    private function text(\Dom\Element $element): string
    {
        return trim(preg_replace('/\s+/u', ' ', $element->textContent) ?? '');
    }
}
