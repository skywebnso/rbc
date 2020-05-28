<?php

namespace App\Http\Controllers;

use App\ParsedNews;
use App\Parser\ParserRbc;
use Illuminate\Http\Request;

class ParserController extends Controller
{
    public function view(){
        $parser = new ParserRbc();
        $news = $parser->getNews();

        foreach ($news as $newsItem) {
            if (ParsedNews::where('url',$newsItem->url)->first() !== null) {
                continue;
            }
            $newsItem->saveOrFail();
        }
    }
}
