<?php

function addTextNode(DOMDocument $root, $tag, $text, DOMElement $parent = null)
{
    $node = $root->createElement($tag);
    $node->appendChild($root->createTextNode($text));

    if ($parent === null) {
        $root->appendChild($node);
    }
    else {
        $parent->appendChild($node);
    }
}

/**
 * @var $programme Programme
 */

// http://www.sitepoint.com/create-a-podcast-feed-with-php/

$xml = new DOMDocument('1.0', 'utf-8');

/*
 * Root element
 */
$rss = $xml->createElement('rss');

$rss->setAttribute('xmlns:itunes', 'http://www.itunes.com/dtds/podcast-1.0.dtd');
$rss->setAttribute('version', '2.0');

$xml->appendChild($rss);

/*
 * Channel
 */
$channel = $xml->createElement('channel');

// Feed metadata
addTextNode($xml, 'title', $programme->title, $channel);
addTextNode($xml, 'link', URL::to('/'), $channel);
addTextNode($xml, 'copyright', 'All content &#xA9; ' . date('Y') . ' British Broadcasting Corporation, podcast feed &#xA9; ' . date('Y') . ' Christopher Paslawski', $channel);
addTextNode($xml, 'itunes:subtitle', $programme->description, $channel);
addTextNode($xml, 'itunes:author', $programme->title, $channel);
addTextNode($xml, 'itunes:summary', $programme->description, $channel);
addTextNode($xml, 'description', $programme->description, $channel);
addTextNode($xml, 'itunes:image', $programme->image, $channel);
addTextNode($xml, 'itunes:category', 'Music', $channel);

/*
// Owner information
$owner = $xml->createElement('itunes:owner');
$owner->appendChild($xml->createElement('itunes:name', 'Christopher Paslawski'));
$owner->appendChild($xml->createElement('itunes:email', 'chris@paslawski.me'));
$channel->appendChild($owner);
*/

/*
 * Items
 */
foreach ($programme->entries as $entry) {
    $item = $xml->createElement('item');

    addTextNode($xml, 'title', $entry->title, $item);
    addTextNode($xml, 'itunes:subtitle', trim(strtok($entry->description, "\n")), $item);
    addTextNode($xml, 'description', $entry->description, $item);

    addTextNode($xml, 'itunes:image', $entry->image, $item);
    addTextNode($xml, 'guid', URL::to('/' . $entry->mediator_id . '.m4a'), $item);
    addTextNode($xml, 'itunes:duration', $entry->duration, $item);
    addTextNode($xml, 'pubDate', date('r', strtotime($entry->broadcast_at)), $item);

    //$item->appendChild($xml->createTextNode('itunes:author', $programme->title));
    //$item->appendChild($xml->createElement('itunes:subtitle', $entry->description));
    //$item->appendChild($xml->createElement('itunes:summary', $entry->description));

    $enclosure = $xml->createElement('enclosure');
    $enclosure->setAttribute('url', $entry->url);
    $enclosure->setAttribute('length', $entry->size);
    $enclosure->setAttribute('type', 'audio/x-m4a');
    $item->appendChild($enclosure);

    $channel->appendChild($item);
}

$rss->appendChild($channel);

$xml->formatOutput = true;
echo $xml->saveXML();
