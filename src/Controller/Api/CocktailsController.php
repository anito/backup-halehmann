<?php
namespace App\Controller\Api;

use App\Controller\Api\AppController;

class CocktailsController extends AppController
{
    public $paginate = [
        'page' => 1,
        'limit' => 50,
        'maxLimit' => 150,
        'sortWhitelist' => [
            'id', 'name'
        ]
    ];
}