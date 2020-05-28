<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\News;
use App\Entity\RbcNews;
use App\ParsedNews;
use RuntimeException;

/**
 * Interface for object that can parse data from one of news source.
 */
interface ItemParser
{
    /**
     * Parses news item from html.
     *
     * @param string $url
     * @param string $html
     *
     * @return News
     *
     * @throws RuntimeException
     */
    public function parseNewsItem(string $url, string $html): ParsedNews;
}
