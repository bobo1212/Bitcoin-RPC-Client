<?php
/**
 * Created by PhpStorm.
 * User: miki
 * Date: 12.11.18
 * Time: 17:02
 */

namespace Bitcoin;


interface HttpClientInterface
{
    public function post(array $post):string;
}