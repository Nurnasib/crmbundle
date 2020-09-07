<?php
/**
 * Created by PhpStorm.
 * User: shafiq
 * Date: 9/29/19
 * Time: 9:09 PM
 */

namespace Terminalbd\CrmBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{


    /**
     * @Route("demo", name="demo_index")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    function index() {
        return $this->render('@TerminalbdDemo/defult/index.html.twig');
    }

}