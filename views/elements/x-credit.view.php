<?php
/**
 * Who holds the rights to an image, linked to where the file came from.
 *
 * @var string $name
 * @var ?string $url
 */
?>
<a :if="$url !== null" :href="$url" rel="noreferrer">{{ $name }}</a><span :else>{{ $name }}</span>
