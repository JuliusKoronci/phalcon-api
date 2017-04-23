<?php

namespace Application\Front\Controllers;

use Phalcon\Mvc\Controller;

/**
 * Class IndexController
 * @package Application\Front\Controllers
 */
class IndexController extends Controller
{
    /**
     * @SWG\Get(
     *     path="/",
     *     @SWG\Response(response="200", description="Testing index")
     * )
     */
    public function indexAction()
    {
        return 'test';
    }
}
