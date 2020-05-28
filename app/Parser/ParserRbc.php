<?php

namespace App\Parser;

use App\ParsedNews;
use GuzzleHttp\Client;
use http\Exception\RuntimeException;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Promise;

class ParserRbc extends Model
{
    public const MAIN_URL = 'https://www.rbc.ru/';

    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $baseUrl;
    /**
     * @var array
     */
    private $itemParsers;

    /**
     * @var array
     */
    private $newsUrls = [
        'www.rbc.ru'
    ];

    /**
     * @param Client $client
     * @param string $baseUrl
     */
    public function __construct(string $baseUrl = self::MAIN_URL) {
        $this->client = new Client();
        $this->baseUrl = $baseUrl;

        foreach ($this->newsUrls as $url){
            $this->itemParsers = [$url => new ItemRbc()];
        }
    }

    /**
     * @return array
     */
    public function getNews(): array
    {
        $listOfUrlsForNews = $this->findNews();
        $contents = $this->getUrlContent($listOfUrlsForNews);

        return $this->parseContents($contents);
    }

    /**
     * Find news from main page
     *
     * @return string[]
     */
    private function findNews(): array
    {
        $results = $this->getUrlContent([$this->baseUrl]);
        $crawler = new Crawler($results);

        return $crawler
            ->filter('.js-news-feed-list > a.news-feed__item')
            ->reduce(
                function (Crawler $node) {
                    $href = $node->attr('href');
                    return $this->getNewsItem($href) !== null;
                }
            )
            ->each(
                function (Crawler $node) {
                    return $node->attr('href');
                }
            );
    }

    /**
     * Parses html to news entities.
     *
     * @param array<string, string> $contents
     *
     * @return News[]
     */
    private function parseContents(array $contents): array
    {
        $news = [];

        foreach ($contents as $url => $html) {
            $newsItem = $this->parseContentToEntity($url, $html);
            if ($newsItem !== null) {
                $news[] = $newsItem;
            }
        }

        return $news;
    }

    /**
     * Parses single news html to news entity.
     *
     * @param string $url
     * @param string $html
     *
     * @return ParsedNews|null
     */
    private function parseContentToEntity(string $url, string $html): ?ParsedNews
    {
        $parser = $this->getNewsItem($url);

        if ($parser === null) {
            return null;
        }

        return $parser->parseNewsItem($url, $html);
    }

    /**
     * @param array $urls
     * @return array
     */
    private function getUrlContent(array $urls): array
    {
        $contents = [];

        $promises = [];
        foreach ($urls as $url) {
            $promises[$url] = $this->client->getAsync($url);
        }

        $responses = Promise\unwrap($promises);
        $responses = Promise\settle($promises)->wait();

        foreach ($responses as $url => $response) {
            $responseObject = $response['value'] ?? null;
            if ($responseObject->getStatusCode() !== 200) {
                $message = sprintf('Error while loading main page: got %s status.', $responseObject->getStatusCode());
                throw new RuntimeException($message);
            }
            $contents[$url] = (string) $responseObject->getBody();
        }

        return $contents;
    }

    /**
     * Get parser for news item by url.
     *
     * @param string $url
     *
     * @return ItemParser|null
     */
    private function getNewsItem(string $url): ?ItemParser
    {
        $host = strtolower(parse_url($url, PHP_URL_HOST));

        return $this->itemParsers[$host] ?? null;
    }
}
