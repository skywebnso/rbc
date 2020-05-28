<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ParsedNews
 *
 * @property int    $id
 * @property string $url
 * @property string $title
 * @property string $image
 * @property string $body
 */
class ParsedNews extends Model
{
    public const DEFAULT_LENGTH = 200;

    /**
     * @var int
     */
    protected $length;

    protected $fillable = ['url', 'title', 'image', 'body'];

    /**
     * @param int $length
     * @return string
     */
    public function getDescription(int $length = self::DEFAULT_LENGTH): string
    {
        $this->length = $length;

        if (strlen($this->body) <= $this->length) {
            return $this->body;
        }

        $prepareDescription = $this->prepareShortDescription();

        return substr($this->body, 0, $prepareDescription) . '...';
    }

    /**
     * @return int
     */
    private function prepareShortDescription(): int
    {
        return strrpos(substr($this->body, 0, $this->length), ' ') ?: $this->length;
    }
}
