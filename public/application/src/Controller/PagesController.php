<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */

namespace App\Controller;

use App\Model\Document\Tweet;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Elastica\Client;
use Elastica\Request;

/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link https://book.cakephp.org/3.0/en/controllers/pages-controller.html
 *
 * @property \App\Controller\Component\TwitterComponent $Twitter
 * @property \App\Model\Type\TweetsType $Tweets
 *
 * @method \App\Model\Document\Tweet[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class PagesController extends AppController
{
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('Twitter');

        ini_set('upload_max_filesize', '1G');
    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        // 'Elastic' プロバイダーを利用して Type を読み込む
        $this->loadModel('Tweets', 'Elastic');
    }

    /**
     * Displays a view
     *
     * @param array ...$path Path segments.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Network\Exception\ForbiddenException When a directory traversal attempt.
     * @throws \Cake\Network\Exception\NotFoundException When the view file could not
     *   be found or \Cake\View\Exception\MissingTemplateException in debug mode.
     */
    public function display(...$path)
    {
        $count = count($path);
        if (!$count) {
            return $this->redirect('/');
        }
        if (in_array('..', $path, true) || in_array('.', $path, true)) {
            throw new ForbiddenException();
        }
        $page = $subpage = null;

        if (!empty($path[0])) {
            $page = $path[0];
        }
        if (!empty($path[1])) {
            $subpage = $path[1];
        }
        $this->set(compact('page', 'subpage'));

        try {
            $this->render(implode('/', $path));
        } catch (MissingTemplateException $exception) {
            if (Configure::read('debug')) {
                throw $exception;
            }
            throw new NotFoundException();
        }
    }

    public function index()
    {

    }

    /**
     * @throws \Exception
     */
    public function search()
    {
        $status = null;

        if ($this->request->is('get')) {
            $params = $this->request->getQueryParams();

            if (isset($params['query'])) {
                $status = 'searched';
                $queryWord = $params['query'];
                $client = new Client();

                $index = $client->getIndex('twitter');
                $type = $index->getType('tweets');

                $query = [
                    'query' => [
                        'query_string' => [
                            'query' => $params['query'],
                        ]
                    ]
                ];

                $path = $index->getName() . '/' . $type->getName() . '/_search';
                $response = $client->request($path, Request::GET, $query);

                $tweets = [];
                foreach ($response->getData()['hits']['hits'] as $responseData) {
                    $source = $responseData['_source'];
                    $tweet = new Tweet();

                    $tweet->screen_name = $source['screen_name'];
                    $tweet->id = $source['id'];
                    $tweet->text = $source['text'];
                    $tweet->image_urls = $source['image_urls'];
                    $tweet->words = $source['words'];

                    $screen_name = $tweet->screen_name;
                    $tweet_id = $tweet->id;
                    $tweet->tweet_url = "https://twitter.com/${screen_name}/status/${tweet_id}";

                    $tweets[] = $tweet;
                }
                $this->set(compact('tweets', 'queryWord'));
            } else {

            }
        }

        $this->set(compact('status'));
    }

    public function upload()
    {

    }


    public function home()
    {

    }
}
