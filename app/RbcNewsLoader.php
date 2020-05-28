<?php

namespace App;

use App\Parser\ParserRbc;
use Illuminate\Database\Eloquent\Model;

class RbcNewsLoader extends Model
{
    /**
     * @var ParserRbc
     */
    private $parser;

    public function __construct(ParserRbc $parser) {

        /*
         *  SourceParserRbc $parser,
        NewsRepository $repository,
        NewsEntityManager $entityManager*/
          $this->parser = $parser;
        /*$this->repository = $repository;
        $this->entityManager = $entityManager;*/
    }

    /**
     * Loads news from source to database.
     */
    public function get(): void
    {
        $news = $this->parser->getNews();
        die();
        foreach ($news as $newsItem) {
            if ($this->repository->findOne($newsItem->getId()) !== null) {
                continue;
            }
            $this->entityManager->insert($newsItem);
        }
    }
}
