<?php

declare(strict_types=1);

namespace App\Parser;

use App\ParsedNews;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Interface for object that can parse data from one of news source.
 */
class ItemRbc implements ItemParser
{
    /**
     * @inheritDoc
     * @noinspection PhpHierarchyChecksInspection
     */
    public function parseNewsItem(string $url, string $html): ParsedNews
    {
        $crawler = new Crawler($html);

        $news = new ParsedNews();
        $news->url = $url;
        $news->title = $this->extractName($crawler);
        $news->image = $this->extractImage($crawler);
        $news->body = $this->extractText($crawler);

        return $news;
    }

    /**
     * Creates unique id for item using url.
     *
     * @param string $url
     *
     * @return string
     */
    private function createIdFromUrl(string $url): string
    {
        $id = trim($url, " \t\n\r\0\x0B\\/");
        $id = strtolower($id);

        return md5($id);
    }

    /**
     * Extracts image url from html.
     *
     * @param Crawler $node
     *
     * @return string
     */
    private function extractImage(Crawler $crawler): string
    {
        $nodeList = $crawler->filter('.article__main-image__wrap img');

        return $nodeList->count() > 0 ? $nodeList->first()->attr('src') : '';
    }

    /**
     * Extracts name from html.
     *
     * @param Crawler $node
     *
     * @return string
     */
    private function extractName(Crawler $crawler): string
    {
        $nodeList = $crawler->filter('h1');

        return $nodeList->count() > 0 ? $nodeList->first()->text() : '';
    }

    /**
     * Extracts text from html.
     *
     * @param Crawler $node
     *
     * @return string
     */
    private function extractText(Crawler $crawler): string
    {
        return implode(
            "\r\n",
            $crawler->filter('.article__content p')->each(
                function (Crawler $node) {
                    return $node->text();
                }
            )
        );
    }
}
