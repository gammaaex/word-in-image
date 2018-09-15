<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/09/15
 * Time: 11:30
 */

namespace App\Model\Document;

use Cake\ElasticSearch\Document;

/**
 * Class Tweet
 * @package App\Model\Document
 *
 * Tweet Document
 *
 * @property string $screen_name
 * @property int $id
 * @property string $text
 * @property array $image_urls
 * @property array $words
 *
 * @property string tweet_url
 */
class Tweet extends Document
{
    public $screen_name;
    public $id;
    public $text;
    public $image_urls;
    public $tweet_url;

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'screen_name' => true,
        'id' => true,
        'text' => true,
        'image_urls' => true,
        'words' => true,
    ];

    /**
     * Tweetのパーマリンクを取得する。
     *
     * @return string
     */
    protected function _getTweetURL()
    {
        $screen_name = $this->_properties['screen_name'];
        $tweet_id = $this->_properties['id'];

        return "https://twitter.com/${screen_name}/status/${tweet_id}";
    }
}