<?php
/**
 * Created by PhpStorm.
 * User: shafiq
 * Date: 9/29/19
 * Time: 9:09 PM
 */

namespace Terminalbd\CrmBundle\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Terminalbd\CrmBundle\Entity\Api;
use Terminalbd\KpiBundle\Entity\LocationSalesTarget;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ApiController
 * @package Terminalbd\CrmBundle\Controller
 * @Route("/crm/api")
 */

class ApiController extends AbstractController
{


    /**
     * @Route("/login", methods={"POST","GET"}, options={"expose"=true})
     */
    public function login(Request $request)
    {
        // This data is most likely to be retrieven from the Request object (from Form)
        // But to make it easy to understand ...
        $terminal = 1;

        $formData = $_REQUEST;
        $_username = $formData['username'];
        $_password = $formData['password'];
        $user = $this->getDoctrine()->getRepository(User::class)->checkLoginUser($_username);
        /// End Retrieve user
        // Check if the user exists !
        if(!$user){
            return new Response(
                'Username doesnt exists',
                Response::HTTP_UNAUTHORIZED,
                array('Content-type' => 'application/json')
            );
        }
        if(!empty($user)){

            /* @var $user User */

            $roles = unserialize(serialize($user->getAppRoles()));
            $rolesSeparated = implode(",", $roles);
            $upozilas =[];
            foreach ($user->getUpozila() as $location):
                $upozilas[]= $location->getId();
            endforeach;
            $locations = implode(",", $upozilas);
            $data = array(
                'username' => $user->getUsername(),
                'name' => $user->getName(),
                'roles' => $rolesSeparated,
                'designation' => empty($user->getDesignation()) ? '' : $user->getDesignation()->getName(),
                'lineManager' => empty($user->getLineManager()) ? '' : $user->getLineManager()->getId(),
                'locations' => $locations,
                'upozilas' => $upozilas,
                'status' => 'success'
            );
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }
    }

    public function checkDuplicateUserAction(Request $request)
    {
        $username = $request->request->get('username');
        $user = $this->getDoctrine()->getManager()->getRepository("UserBundle:User")
            ->checkExistingUser($username);
        if ($user == false) {
            $data = array(
                'status' => 'valid'
            );
        }else {
            $data = array(
                'status' => 'invalid'
            );
        }
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($data));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;


    }

    public function editUserAction(Request $request)
    {
        $username = $request->request->get('user_id');
        $user = $this->getDoctrine()->getManager()->getRepository("UserBundle:User")
            ->findOneBy(array('id' => $username));
        $data = array(
            'user_id' => $username,
            'username' => $user->getUsername(),
            'name' => $user->getName(),
            'role' => $user->getAndroidRole()
        );
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($data));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }

    public function updateUserAction(Request $request)
    {

        $formData = $request->request->all();
        $username = $formData['user_id'];
        $userExist = $this->getDoctrine()->getRepository('UserBundle:User')->find($username);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }elseif($userExist){

            $this->getDoctrine()->getRepository('UserBundle:User')->androidUserUpdate($userExist,$formData);
            $data = array('status' => 'success');
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;

        }else{

            return new Response('Unauthorized access.', 401);
        }
    }

    public function forgetPasswordAction(Request $request)
    {

        $username = $request->request->get('username');
        $user = $this->getDoctrine()->getManager()->getRepository("UserBundle:User")
            ->checkLoginUser($username);
        if (empty($user)) {
            return new Response('Unauthorized access.', 401);
        }else{
            $data = array(
                'licenseKey' => $user->getGlobalOption()->getUniqueCode(),
                'username' => $user->getUsername(),
                'name' => $user->getName(),
                'status' => 'success'
            );
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function resetPasswordAction(Request $request)
    {

        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{
            $entity = $this->checkApiValidation($request);
            $username = $request->request->get('username');
            $password = $request->request->get('password');
            $user = $this->getDoctrine()->getManager()->getRepository("UserBundle:User")
                ->findOneBy(array('username' => $username,'enabled' => 1));
            if(empty($user)){
                return new Response('Unauthorized access.', 401);
            }

            $user->setPlainPassword($password);
            $this->get('fos_user.user_manager')->updateUser($user);
            $data = array(
                'username' => $user->getUsername(),
                'name' => $user->getProfile()->getName(),
                'status' => 'success'
            );
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }
    }



    /**
     * @Route("/agent", name="crm_api_agent" , methods={"POST","GET"}, options={"expose"=true})
     */
    public function apiAgent()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $locations = isset($_REQUEST['locations']) ? $_REQUEST['locations'] : "";
        //$terminal = $this->getUser()->getTerminal()->getId();
        $entities = $this->getDoctrine()->getRepository(Api::class)->apiAgent(1,$locations);
        //dd($entities);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($entities));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }

    /**
     * @Route("/customer", name="customerApi")
     */
    public function customerApi()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        //$terminal = $this->getUser()->getTerminal()->getId();
        $locations = isset($_REQUEST['locations']) ? $_REQUEST['locations'] : "";
        $entities = $this->getDoctrine()->getRepository(Api::class)->customerApi(1,$locations);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($entities));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }

    /**
     * @Route("/broiler/standard", name="crm_api_brolier")
     */
    public function apiBroiler()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        //$terminal = $this->getUser()->getTerminal()->getId();
        $entities = $this->getDoctrine()->getRepository(Api::class)->apiBroiler(1);
        //dd($entities);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($entities));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }

    /**
     * @Route("/sonali/standard", name="crm_api_Sonali")
     */
    public function apiSonali()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        //$terminal = $this->getUser()->getTerminal()->getId();
        $entities = $this->getDoctrine()->getRepository(Api::class)->apiSonali(1);
        //dd($entities);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($entities));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }

    /**
     * @Route("/setting/life-cycle", name="crm_api_apiLifeCycleSetting")
     */
    public function apiLifeCycleSetting()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        //$terminal = $this->getUser()->getTerminal()->getId();
        $entities = $this->getDoctrine()->getRepository(Api::class)->apiLifeCycleSetting(1);
        //dd($entities);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($entities));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }

    /**
     * @Route("/setting", name="apiSetting")
     */
    public function apiSetting()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        //$terminal = $this->getUser()->getTerminal()->getId();
        $entities = $this->getDoctrine()->getRepository(Api::class)->apiSetting(1);
        //dd($entities);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($entities));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }

    /**
     * @Route("/layer/standard", name="apiLayer")
     */
    public function apiLayer()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        //$terminal = $this->getUser()->getTerminal()->getId();
        $entities = $this->getDoctrine()->getRepository(Api::class)->apiLayer(1);
        //dd($entities);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($entities));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }



    /**
     * @Route("/employee", name="employeeApi")
     */
    public function employeeApi()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        //$terminal = $this->getUser()->getTerminal()->getId();
        $entities = $this->getDoctrine()->getRepository(Api::class)->employeeApi(1);
        //dd($entities);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($entities));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }

    /**
     * @Route("/report/farmer-introduce-report", methods={"POST"}, name="farmer-introduce-report")
     */
    public function farmerIntroduceReport(Request $request)
    {
        $farmerType = $request->request->get('farmer_type');
        set_time_limit(0);
        ignore_user_abort(true);
        // $terminal = $this->getUser()->getTerminal()->getId();
        $entities = $this->getDoctrine()->getRepository(Api::class)->farmerIntroduceReport(1,$farmerType);
        //dd($entities);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($entities));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }

    /**
     * @Route("/report/sonali-life-cycle", name="sonali-life-cycle")
     */
    public function sonaliLifeCycleReportPoultry()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        //$terminal = $this->getUser()->getTerminal()->getId();
        $entities = $this->getDoctrine()->getRepository(Api::class)->sonaliLifeCycleReportPoultry(1);
        //dd($entities);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($entities));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }

    /**
     * @Route("/report/boiler-life-cycle", name="boiler-life-cycle")
     */
    public function boilerLifeCycleReportPoultry()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        //$terminal = $this->getUser()->getTerminal()->getId();
        $entities = $this->getDoctrine()->getRepository(Api::class)->boilerLifeCycleReportPoultry(1);
        //dd($entities);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($entities));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }

    /**
     * @Route("/report/layer-life-cycle", name="layer-life-cycle")
     */
    public function layerLifeCycleReportPoultry()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        //$terminal = $this->getUser()->getTerminal()->getId();
        $entities = $this->getDoctrine()->getRepository(Api::class)->layerLifeCycleReportPoultry(1);
        //dd($entities);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($entities));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }

    /**
     * @Route("/cattle/crm-visit", name="crm-visit")
     */
    public function farmCattleVisit()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        //$terminal = $this->getUser()->getTerminal()->getId();
        $entities = $this->getDoctrine()->getRepository(Api::class)->farmCattleVisit();
        //dd($entities);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($entities));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }

    /**
     * @Route("/crm/visit", name="crm-visit")
     */
    public function crmVisit()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        //$terminal = $this->getUser()->getTerminal()->getId();
        $entities = $this->getDoctrine()->getRepository(Api::class)->crmVisit(1);
        //dd($entities);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($entities));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }
}