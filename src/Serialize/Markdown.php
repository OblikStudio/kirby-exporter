<?php

namespace Oblik\Outsource\Serialize;

use Kirby\Text\Markdown as MarkdownParser;
use League\HTMLToMarkdown\HtmlConverter;

class Markdown
{
    public static $decodeOptions = [
        'header_style' => 'atx',
        'suppress_errors' => true,
        'strip_tags' => false,
        'bold_style' => '__',
        'italic_style' => '_',
        'remove_nodes' => '',
        'hard_break' => true,
        'list_item_style' => '-'
    ];

    public static function decode(string $text, $options = [])
    {
        $parser = new MarkdownParser($options);
        $output = $parser->parse($text);
        return str_replace(">\n<", '><', $output);
    }

    public static function encode(string $text, $options = [])
    {
        $options = array_merge(self::$decodeOptions, $options);
        $converter = new HtmlConverter($options);
        return $converter->convert($text);
    }
}