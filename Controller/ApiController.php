<?php
/**
 * Created by PhpStorm.
 * User: shafiq
 * Date: 9/29/19
 * Time: 9:09 PM
 */

namespace Terminalbd\CrmBundle\Controller;

use App\Entity\Core\Agent;
use App\Entity\User;
use App\Entity\Admin\Location;
use App\Service\SmsSender;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Terminalbd\CrmBundle\Entity\Api;
use Terminalbd\CrmBundle\Entity\ApiDetails;
use Terminalbd\CrmBundle\Entity\BroilerLifeCycle;
use Terminalbd\CrmBundle\Entity\BroilerStandard;
use Terminalbd\CrmBundle\Entity\ChickLifeCycle;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\CrmVisit;
use Terminalbd\CrmBundle\Entity\CrmVisitDetails;
use Terminalbd\CrmBundle\Entity\FarmerComplain;
use Terminalbd\CrmBundle\Entity\FarmerComplainDetails;
use Terminalbd\CrmBundle\Entity\FcrDetails;
use Terminalbd\CrmBundle\Entity\LayerPerformanceDetails;
use Terminalbd\CrmBundle\Entity\Setting;
use Terminalbd\CrmBundle\Entity\SettingLifeCycle;
use Terminalbd\CrmBundle\Entity\SonaliStandard;
use Terminalbd\CrmBundle\Form\CrmVisitFormType;
use Terminalbd\CrmBundle\Form\FcrDetailsFormType;
use Terminalbd\KpiBundle\Entity\LocationSalesTarget;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use function Doctrine\Common\Cache\Psr6\set;

/**
 * Class ApiController
 * @package Terminalbd\CrmBundle\Controller
 * @Route("/crm/api")
 */

class ApiController extends AbstractController
{
    /**
     * @Route("/login", methods={"POST","GET"}, options={"expose"=true})
     * @param Request $request
     * @param SmsSender $smsSender
     * @param ParameterBagInterface $parameterBag
     * @return JsonResponse|Response
     */
    public function login(Request $request, SmsSender $smsSender, ParameterBagInterface $parameterBag)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if ($request->getMethod() == 'POST' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')){
            $userMobile = trim($request->request->get('mobile'));
            $findUser = $this->getDoctrine()->getRepository(User::class)->findOneBy(['enabled' => 1, 'mobile' => $userMobile]);
            if ($findUser){
                $userMobile = str_replace('-', '', $userMobile);
                $otp = (string)mt_rand(1000, 9999);
                $message = 'Your OTP is ' . $otp . '.';
                $smsResponse = $smsSender->sendSmsToAgent($message, $userMobile);
//                $smsResponse = json_decode($smsResponse, true);

//                if ($smsResponse['message'] === 'Success'){
                    $roles = unserialize(serialize($findUser->getAppRoles()));
                    $rolesSeparated = implode(",", $roles);
                    $upozilas =[];
                    foreach ($findUser->getUpozila() as $location):
                        $upozilas[]= $location->getId();
                    endforeach;
                    $locations = implode(",", $upozilas);
                    $data = [
                        'userId' => $findUser->getId(),
                        'username' => $findUser->getUsername(),
                        'name' => $findUser->getName(),
                        'email' => $findUser->getEmail(),
                        'roles' => $rolesSeparated,
                        'designation' => $findUser->getDesignation() ? $findUser->getDesignation()->getName() : '',
                        'lineManager' => $findUser->getLineManager() ? $findUser->getLineManager()->getId() : '' ,
                        'locations' => $locations,
                        'upozilas' => $upozilas,
                        'status' => '200',
                        'otp' => $otp,
                    ];
                    $response = new Response();
                    $response->headers->set('Content-Type', 'application/json');
                    $response->setContent(json_encode($data));
                    $response->setStatusCode(Response::HTTP_OK);
                    return $response;
//                }
            }else{
                $response = new Response();
                $response->headers->set('Content-Type', 'application/json');
                $response->setContent(json_encode([
                    'message' => 'Unregistered mobile number!',
                    'status' => 401,
                ]));
                $response->setStatusCode(Response::HTTP_UNAUTHORIZED);
                return $response;
            }
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode([
            'message' => 'Invalid request!',
            'status' => 405,
        ]));
        $response->setStatusCode(Response::HTTP_UNAUTHORIZED);
        return $response;
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
        $entities = $this->getDoctrine()->getRepository(Api::class)->customerApi(1,'farmer',$locations);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($entities));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }

    /**
     * @Route("/sub-agent", name="sub_agent_api")
     * @param Request $request
     * @param ParameterBagInterface $parameterBag
     * @return JsonResponse|Response
     */
    public function subAgentApi(Request $request, ParameterBagInterface $parameterBag)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if ($request->getMethod() == 'GET' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')){
            $locations = $request->query->get('locations');
            $entities = $this->getDoctrine()->getRepository(Api::class)->agentApi(1,'sub-agent',$locations);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($entities));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }
        return new JsonResponse([
            'status' => 404,
            'message' => 'Not Found!'
        ]);

    }

    /**
     * @Route("/other-agent", name="other_agent_api")
     * @param Request $request
     * @param ParameterBagInterface $parameterBag
     * @return JsonResponse|Response
     */
    public function otherAgent(Request $request, ParameterBagInterface $parameterBag)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if ($request->getMethod() == 'GET' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')){
            //$terminal = $this->getUser()->getTerminal()->getId();
            $locations = $request->query->get('locations');
            $entities = $this->getDoctrine()->getRepository(Api::class)->agentApi(1,'other-agent',$locations);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($entities));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }
        return new JsonResponse([
            'status' => 404,
            'message' => 'Not Found!'
        ]);
    }

    /**
     * @Route("/crmvisit", methods={"POST"}, name="crmvisit")
     */
    public function crmVisit(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $username = isset($_REQUEST['username']) ? $_REQUEST['username'] : "";
        //$terminal = $this->getUser()->getTerminal()->getId();
        $entities = $this->getDoctrine()->getRepository(Api::class)->crmVisit(1,$username);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($entities));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }

    /**
     * @Route("/employee", methods={"POST"}, name="employeeApi")
     * @param Request $request
     * @return Response
     */
    public function employeeApi(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $username = isset($_REQUEST['username']) ? $_REQUEST['username'] : "";
        //$terminal = $this->getUser()->getTerminal()->getId();
        $entities = $this->getDoctrine()->getRepository(Api::class)->employeeApi(1, $username);

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

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($entities));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }


    /**
     * @Route("/report/farmer-introduce-report", methods={"POST"}, name="farmerintroducereport")
     * @param Request $request
     * @return Response
     */
    public function farmerIntroduceReport(Request $request)
    {
        $breedName = $request->request->get('breed_name');
        $employeeName = $request->request->get('employee');

        $employee = $this->getDoctrine()->getRepository(User::class)->findOneBy(array('name' => $employeeName));
        //$agentName = $request->request->get('agent_name');

        set_time_limit(0);
        ignore_user_abort(true);
        // $terminal = $this->getUser()->getTerminal()->getId();
        $entities = $this->getDoctrine()->getRepository(Api::class)->farmerIntroduceReport(1,$breedName,$employee);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($entities));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }

    /**
     * @Route("/report/farmer-touch-report", methods={"POST"}, name="farmertouchreport")
     * @param Request $request
     * @return Response
     */
    public function farmerTouchReport(Request $request)
    {
        $startDate = $request->request->get('startDate');
        $endDate = $request->request->get('endDate');
        $reportSlug = $request->request->get('report');
        $employeeName = $request->request->get('employee');

        $report = $this->getDoctrine()->getRepository(Setting::class)->findOneBy(array('slug' => $reportSlug));

        $employee = $this->getDoctrine()->getRepository(User::class)->findOneBy(array('name' => $employeeName));

        set_time_limit(0);
        ignore_user_abort(true);
        $entities = $this->getDoctrine()->getRepository(Api::class)->farmerTouchReport(1,$startDate, $endDate, $report, $employee);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($entities));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }

    /**
     * @Route("/report/farmer-training-report", methods={"POST"}, name="farmer-training-report")
     * @param Request $request
     * @return Response
     */
    public function farmerTrainingReport(Request $request)
    {
        $breedName = $request->request->get('breed_name');
        $employeeName = $request->request->get('employee');

        $employee = $this->getDoctrine()->getRepository(User::class)->findOneBy(array('name' => $employeeName));
        //$agentName = $request->request->get('agent_name');
        set_time_limit(0);
        ignore_user_abort(true);
        // $terminal = $this->getUser()->getTerminal()->getId();
        $entities = $this->getDoctrine()->getRepository(Api::class)->farmerTrainingReport(1,$breedName, $employee);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($entities));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }


    /**
     * @Route("/report/poultry", methods={"POST"}, name="reportPoultry")
     * @param Request $request
     * @return Response
     */
    public function poultryLifeCylceReport(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $startDate = $request->request->get('startDate');
        $endDate = $request->request->get('endDate');
        $reportSlug = $request->request->get('report');
        $customerName = $request->request->get('customer');

        $report = $this->getDoctrine()->getRepository(Setting::class)->findOneBy(array('slug' => $reportSlug));

        $customer = $this->getDoctrine()->getRepository(CrmCustomer::class)->findOneBy(array('name' => $customerName));


        //$terminal = $this->getUser()->getTerminal()->getId();
        $entities = $this->getDoctrine()->getRepository(Api::class)->poultryLifeCylceReport(1, $startDate, $endDate,$reportSlug, $customerName);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($entities));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }

    /**
     * @Route("/cattle/crm-visit", methods={"POST"}, name="cattle-crm-visit")
     * @param Request $request
     * @return Response
     */
    public function farmCattleVisit(Request $request)
    {
        $startDate = $request->request->get('startDate');
        $endDate = $request->request->get('endDate');
        $employeeName = $request->request->get('employee');

        set_time_limit(0);
        ignore_user_abort(true);

        $employee = $this->getDoctrine()->getRepository(User::class)->findOneBy(array('name' => $employeeName));

        $entities = $this->getDoctrine()->getRepository(Api::class)->farmVisitCattle(1,$startDate,$endDate,$employee);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($entities));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }

    /**
     * @Route("/chick/fcr", methods={"POST"}, name="chickfcr")
     * @param Request $request
     * @return Response
     */
    public function frcReportPoulty(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);

        $startDate = $request->request->get('startDate');
        $endDate = $request->request->get('endDate');
        $reportSlug = $request->request->get('report');
        $employeeName = $request->request->get('employee');

        $report = $this->getDoctrine()->getRepository(Setting::class)->findOneBy(array('slug' => $reportSlug));

        $employee = $this->getDoctrine()->getRepository(User::class)->findOneBy(array('name' => $employeeName));

        $entities = $this->getDoctrine()->getRepository(Api::class)->frcReportPoulty(1, $startDate, $endDate, $reportSlug,$employeeName);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($entities));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }

    /**
     * @Route("/fcr/before/{type}/new", methods={"GET", "POST"}, name="api_fcr_before_new", options={"expose"=true})
     * @param Request $request
     * @param $type
     * @return Response
     * @throws Exception
     */
    // type = sonali or boiler
    public function apiFcrBeforeNew(Request $request, $type): Response
    {
        $data = $request->request->all();
        $slug = 'fcr-before-sale-'.$type;
        $report = $this->getDoctrine()->getRepository(Setting::class)->findOneBy(['slug'=>$slug, 'settingType'=>'FARMER_REPORT', 'status'=>1]);

        $hatchery = $this->getDoctrine()->getRepository(Setting::class)->findOneBy(['id'=>$data['hatchery_id'], 'status'=>1]);

        $breed = $this->getDoctrine()->getRepository(Setting::class)->findOneBy(['id'=>$data['breed_id'], 'status'=>1]);

        $feed = $this->getDoctrine()->getRepository(Setting::class)->findOneBy(['id'=>$data['feed_id'], 'status'=>1]);

        $feedMill = $this->getDoctrine()->getRepository(Setting::class)->findOneBy(['id'=>$data['feed_Mill'], 'status'=>1]);

        $feedType = $this->getDoctrine()->getRepository(Setting::class)->findOneBy(['id'=>$data['feedType'], 'status'=>1]);

        $employee = $this->getDoctrine()->getRepository(User::class)->find($data['user_id']);

        $customer = $this->getDoctrine()->getRepository(CrmCustomer::class)->find($data['customer_id']);

        $entity = new FcrDetails();
        $reportingDate = date('Y-m-d',strtotime('now'));
        $hatchingDate = date('Y-m-d',strtotime('now'));
        $proDate = date('Y-m-d',strtotime('now'));
        $entity->setReportingMonth(new \DateTime($reportingDate));
        $entity->setHatchingDate(new \DateTime($hatchingDate));
        $entity->setProDate(new \DateTime($proDate));
        $entity->setFcrOfFeed(strtoupper('before'));
        $entity->setCustomer($customer);
        $entity->setReport($report);
        $entity->setAgent($customer->getAgent());
        $entity->setEmployee($employee);
        $entity->setTotalBirds($data['totalBirds']);
        $entity->setAgeDay($data['age']);
        $entity->setMortalityPes($data['mortality_pcs']);
        $entity->setHatchery($hatchery?$hatchery:null);
        $entity->setBreed($breed?$breed:null);
        $entity->setFeed($feed?$feed:null);
        $entity->setFeedMill($feedMill?$feedMill:null);
        $entity->setFeedType($feedType?$feedType:null);
        $entity->setBatchNo($data['batch_no']);
        $entity->setRemarks($data['remarks']);

        if($report->getSlug()=='fcr-before-sale-sonali'){

            /* @var SonaliStandard $sonaliStandard*/
            $sonaliStandard= $this->getDoctrine()->getRepository(SonaliStandard::class)->findOneBy(array('age'=>$entity->getAgeDay()));
            if($sonaliStandard){
                $entity->setWeightStandard($sonaliStandard->getTargetBodyWeight());
                $entity->setFeedConsumptionStandard($sonaliStandard->getCumulativeFeedIntake());
            }
        }
        if($report->getSlug()=='fcr-before-sale-boiler'){

            /* @var BroilerStandard $broilerStandard*/
            $broilerStandard= $this->getDoctrine()->getRepository(BroilerStandard::class)->findOneBy(array('age'=>$entity->getAgeDay()));
            if($broilerStandard){
                $entity->setWeightStandard($broilerStandard->getTargetBodyWeight());
                $entity->setFeedConsumptionStandard($broilerStandard->getTargetFeedConsumption());
            }
        }
        $entity->setMortalityPercent($entity->calculateMortalityPercent());
        $entity->setFeedConsumptionPerBird($entity->calculatePerBird());
        $entity->setFcrWithoutMortality($entity->calculateWithoutMortality());
        $entity->setFcrWithMortality($entity->calculateWithMortality());


        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();

        return new JsonResponse('success');
    }

    /**
     * @Route("/crmvisitingarea", methods={"GET","POST"}, name="crmVisitingArea")
     * @param Request $request
     * @return Response
     */
    public function crmVisitingArea(Request $request)
    {

        $terminal = 1;

        $formData = $_REQUEST;
        $userId = $request->request->get('user_id');

        $user = $this->getDoctrine()->getRepository(User::class)->find($userId);

        if (!empty($user)) {

            /* @var $user User */

            $upozilas = [];
            foreach ($user->getUpozila() as $location):
                $upozilas[] = $location->getId();
                $upozilaName[] = $location->getName();
            endforeach;
            $locations = implode(",", $upozilas);
            $data = array(
                'upozilas' => $upozilas,
                'upozilaName' => $upozilaName
            );
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }
    }

    /**
     * @Route("/crmvisitnew", methods={"GET","POST"}, name="crmVisitNew")
     * @param Request $request
     * @return JsonResponse
     */
    public function new(Request $request)
    {
        $data = $request->request->all();

        $employee = $this->getDoctrine()->getRepository(User::class)->find($data['user_id']);

        $locations = $this->getDoctrine()->getRepository(Location::class)->find($data['id']);

        $entity = new CrmVisit();
        $entity->setEmployee($employee);
        $entity->setLocation($locations);
        $entity->setWorkingDuration($data['workingduration']);
        $entity->setWorkingDurationTo($data['workingdurationto']);
        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();

        return new JsonResponse('success');
    }

    /**
     * @Route("/dailyActiviesPurpose", methods={"GET"}, name="dailyActiviesPurpose")
     */
    public function dailyActiviesPurpose()
    {

        set_time_limit(0);
        ignore_user_abort(true);

        $entities = $this->getDoctrine()->getRepository(Api::class)->dailyActiviesPurpose(1);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($entities));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }

    /**
     * @Route("/vehicle", methods={"GET"}, name="vehicle")
     */
    public function vehicle()
    {

        set_time_limit(0);
        ignore_user_abort(true);

        $entities = $this->getDoctrine()->getRepository(Api::class)->vehicle(1);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($entities));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }

    /**
     * @Route("/farmerSelectPurpose", methods={"GET","POST"}, name="farmerSelectPurpose")
     * @param Request $request
     * @return Response
     */
    public function farmerSelectPurpose(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);

        $employeeId = $request->request->get('user_id');

        $employee = $this->getDoctrine()->getRepository(User::class)->findOneBy(array('id' => $employeeId));

        $entities = $this->getDoctrine()->getRepository(Api::class)->farmerSelectPurpose(1, $employee);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($entities));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }

    /**
     * @Route("/selectFarmType", methods={"GET"}, name="selectFarmType")
     */
    public function selectFarmType()
    {

        set_time_limit(0);
        ignore_user_abort(true);

        $entities = $this->getDoctrine()->getRepository(Api::class)->selectFarmType();
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($entities));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }



    /**
     * @Route("/farmSelectReport", methods={"GET"}, name="farmSelectReport")
     */
    public function farmSelectReport()
    {
        set_time_limit(0);
        ignore_user_abort(true);

        $entities = $this->getDoctrine()->getRepository(Api::class)->farmSelectReport(1);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($entities));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }

    /**
     * @Route("/searchfarmer", methods={"POST"}, name="searchfarmer")
     */
    public function searchfarmer(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);

        $data = $request->request->all();

        $employee = $this->getDoctrine()->getRepository(User::class)->find($data['user_id']);

        $entities = $this->getDoctrine()->getRepository(Api::class)->searchfarmer($employee);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($entities));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;

    }

    /**
     * @Route("/newfarmertype", methods={"GET"}, name="newfarmertype")
     */
    public function newFarmType()
    {

        set_time_limit(0);
        ignore_user_abort(true);

        $entities = $this->getDoctrine()->getRepository(Api::class)->newfarmertype(1);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($entities));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }

    /**
     * @Route("/ageweek", methods={"GET"}, name="ageweek")
     */
    public function ageweek()
    {

        set_time_limit(0);
        ignore_user_abort(true);

        $entities = $this->getDoctrine()->getRepository(Api::class)->ageweek(1);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($entities));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }

    /**
     * @Route("/feedType", methods={"GET"}, name="feedType")
     */
    public function feedType()
    {

        set_time_limit(0);
        ignore_user_abort(true);

        $entities = $this->getDoctrine()->getRepository(Api::class)->feedType(1);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($entities));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }

    /**
     * @Route("/fishFeedType", methods={"GET"}, name="fishFeedType")
     * @param Request $request
     * @param ParameterBagInterface $parameterBag
     * @return Response
     */
    public function fishFeedType(Request $request, ParameterBagInterface $parameterBag)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if ($request->getMethod() == 'GET' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')){
            $entities = $this->getDoctrine()->getRepository(Api::class)->fishFeedType();
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($entities));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }
        return new JsonResponse([
            'status' => 404,
            'message' => 'Not Found!'
        ]);
    }

    /**
     * @Route("/fishSpeciesName", methods={"GET"}, name="fishSpeciesName")
     * @param Request $request
     * @param ParameterBagInterface $parameterBag
     * @return JsonResponse|Response
     */
    public function fishSpeciesName(Request $request, ParameterBagInterface $parameterBag)
    {

        set_time_limit(0);
        ignore_user_abort(true);
        if ($request->getMethod() == 'GET' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')){
            $entities = $this->getDoctrine()->getRepository(Api::class)->fishSpeciesName();
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($entities));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }
        return new JsonResponse([
            'status' => 404,
            'message' => 'Not Found!'
        ]);


    }



    /**
     * @Route("/hatchery", methods={"GET"}, name="hatchery")
     */
    public function hatchery()
    {

        set_time_limit(0);
        ignore_user_abort(true);

        $entities = $this->getDoctrine()->getRepository(Api::class)->hatchery(1);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($entities));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }

    /**
     * @Route("/feedMill", methods={"GET"}, name="feedMill")
     */
    public function feedMill()
    {

        set_time_limit(0);
        ignore_user_abort(true);

        $entities = $this->getDoctrine()->getRepository(Api::class)->feedMill(1);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($entities));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }

    /**
     * @Route("/breedType", methods={"GET"}, name="breedType")
     */
    public function breedType()
    {

        set_time_limit(0);
        ignore_user_abort(true);

        $entities = $this->getDoctrine()->getRepository(Api::class)->breedType(1);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($entities));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }

    /**
     * @Route("/color", methods={"GET"}, name="color")
     */
    public function color()
    {

        set_time_limit(0);
        ignore_user_abort(true);

        $entities = $this->getDoctrine()->getRepository(Api::class)->color(1);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($entities));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }

    /**
     * @Route("/feed", methods={"GET"}, name="feed")
     */
    public function feed()
    {

        set_time_limit(0);
        ignore_user_abort(true);

        $entities = $this->getDoctrine()->getRepository(Api::class)->feed(1);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($entities));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }

    /**
     * @Route("/disease", methods={"GET"}, name="disease")
     * @param Request $request
     * @param ParameterBagInterface $parameterBag
     * @return JsonResponse|Response
     */
    public function disease(Request $request, ParameterBagInterface $parameterBag)
    {

        set_time_limit(0);
        ignore_user_abort(true);
//        if ($request->getMethod() == 'GET' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')){
        if ($request->getMethod() == 'GET'){
            $entities = $this->getDoctrine()->getRepository(Api::class)->disease(1);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($entities));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }
        return new JsonResponse([
            'status' => 404,
            'message' => 'Not Found!'
        ]);


    }

    /**
     * @Route("/product", methods={"GET"}, name="product")
     */
    public function product()
    {

        set_time_limit(0);
        ignore_user_abort(true);

        $entities = $this->getDoctrine()->getRepository(Api::class)->product(1);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($entities));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }

    /**
     * @Route("/mainculturespecies", methods={"GET"}, name="mainculturespecies")
     * @param Request $request
     * @param ParameterBagInterface $parameterBag
     * @return Response
     */
    public function mainCultureSpecies(Request $request, ParameterBagInterface $parameterBag)
    {

        set_time_limit(0);
        ignore_user_abort(true);
        if ($request->getMethod() == 'GET'){
            $entities = $this->getDoctrine()->getRepository(Api::class)->mainculturespecies();
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($entities));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }
        return new JsonResponse([
            'status' => 404,
            'message' => 'Not Found!'
        ]);

    }

    /**
     * @Route("/usercrmvisitingarea", methods={"POST"}, name="usercrmvisitingarea")
     */
    public function usercrmvisitingarea(Request $request)
    {

        set_time_limit(0);
        ignore_user_abort(true);

        $employeeId = $request->request->get('user_id');

        $entities = $this->getDoctrine()->getRepository(Api::class)->usercrmvisitingarea($employeeId);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($entities));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }

    /**
     * @Route("/dairyBreedType", methods={"GET"}, name="dairyBreedType")
     */
    public function dairyBreedType()
    {

        set_time_limit(0);
        ignore_user_abort(true);

        $entities = $this->getDoctrine()->getRepository(Api::class)->dairyBreedType(1);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($entities));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }

    /**
     * @Route("/fatteningBreedType", methods={"GET"}, name="fatteningBreedType")
     */
    public function fatteningBreedType()
    {

        set_time_limit(0);
        ignore_user_abort(true);

        $entities = $this->getDoctrine()->getRepository(Api::class)->fatteningBreedType(1);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($entities));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }


    /**
     * @Route("/store-json-data", methods={"POST"}, name="store_json_data")
     * @param Request $request
     * @return JsonResponse
     */
    public function storeAllJsonDataFromApi(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);

        $data = $request->request->all();

        if ($data){
            $em = $this->getDoctrine()->getManager();
            $findEmployee = $this->getDoctrine()->getRepository(User::class)->find($data['employee_id']);
            $findParent = $this->getDoctrine()->getRepository(Api::class)->findOneBy(['batchNo' => $data['batch_id'], 'employee' => $findEmployee]);
            if (!$findParent){
                $api = new Api();
                $api->setBatchNo($data['batch_id'] ?: null);
                $api->setEmployee($findEmployee);
                $api->setStatus(0);
                $api->setCreatedAt(new \DateTime('now'));

                $apiDetails = new ApiDetails();
                $apiDetails->setBatch($api);
                $apiDetails->setProcess($data['process']);
                $apiDetails->setJsonData($data['json_body']);
                $apiDetails->setStatus(0);
                $api->addApiDetails($apiDetails);
                $em->persist($api);
                $em->flush();

            } else {
                $apiDetails = new ApiDetails();
                $apiDetails->setBatch($findParent);
                $apiDetails->setProcess($data['process']);
                $apiDetails->setJsonData($data['json_body']);
                $apiDetails->setStatus(0);
                $em->persist($apiDetails);
                $em->flush();
            }
            return new JsonResponse([
                'status' => 200,
            ]);
        } else {
            return new JsonResponse([
                'status' => false,
            ]);
        }
    }

    /**
     * @Route("/list", name="api_response_list")
     */
    public function apiResponseList()
    {
        /*        $array = [
                    'crm_visit' => [
                        'id' => 1,
                        'duration_to' => null,
                        'duration_from' => '1:47 PM',
                        'employee_id' => 23,
                        'location_id' => 340,
                        'visitAreaName' => 'SARISHABARI UPAZILA',
                        'created_at' => '05-05-2021',
                    ],
                    'farmer_report' => [
                        [
                          "id" => 4,
                          "crm_visit_id" => 1,
                          "farmCapacity" => "5",
                          "updated" => null,
                          "comments" => "gvb",
                          "created" => "05-05-2021",
                          "customer_id" => 23,
                          "process" => null,
                          "agent_id" => 1742,
                          "purpose_id" => 16,
                          "firm_type_id" => 223,
                          "report_id" => 239,
                          "purposeName" => "Market Promotion",
                          "farmerName" => "Md Rakibul Hasan",
                          "phoneNumber" => "7000804",
                          "farmTypeName" => "Others (Poultry)",
                          "reportTypeName" => "Antibiotic Free Farm Poultry"
                        ],
                        [
                            "id" => 3,
                            "crm_visit_id" => 1,
                            "farmCapacity" => "5",
                            "updated" => null,
                            "comments" => "g",
                            "created" => "05-05-2021",
                            "customer_id" => 23,
                            "process" => null,
                            "agent_id" => 1742,
                            "purpose_id" => 13,
                            "firm_type_id" => 224,
                            "report_id" => 219,
                            "purposeName" => "Problem Farm Visit",
                            "farmerName" => "Tanveer",
                            "phoneNumber" => "4512",
                            "farmTypeName" => "Others (Fish)",
                            "reportTypeName" => "Farmer Touch Report"
                        ]
                    ],
                ];
                echo json_encode($array);
                die();*/

        $list = $this->getDoctrine()->getRepository(Api::class)->getData();
        return $this->render('@TerminalbdCrm/api/api-response-list.html.twig',[
            'list' => $list,
        ]);
    }

    /**
     * @Route("/{id}/insert-data", name="insert_json_data")
     * @param ApiDetails $apiDetails
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function insertDataIntoCorrespondingTable(ApiDetails $apiDetails)
    {
 /*       set_time_limit(0);
        ignore_user_abort(true);

        if ($apiDetails->isStatus() == 0){
            $jsonToArray = json_decode($apiDetails->getJsonData(), true);
            if ($apiDetails->getProcess() == 'crm_visit'){
                foreach($jsonToArray as $data){
                    $this->getDoctrine()->getRepository(CrmVisit::class)->insertDataFromApi($data);
                }
            }elseif ($apiDetails->getProcess() == 'farmer_report'){
                foreach($jsonToArray as $data){
                    if ($data['crm_visit_id'] !== null){
                        $findVisit = $this->getDoctrine()->getRepository(CrmVisit::class)->findOneBy(['appId' => $data['crm_visit_id']]);
                        if ($findVisit){
                            $this->getDoctrine()->getRepository(CrmVisitDetails::class)->insertDataFromApi($data, $findVisit->getId());
                        }
                    }
                }
            }elseif ($apiDetails->getProcess() == 'layer_performance_report'){
                foreach( $jsonToArray as $data){
                    $this->getDoctrine()->getRepository(LayerPerformanceDetails::class)->insertDataFromApi($data);
                }
            }elseif ($apiDetails->getProcess() == 'crm_visit_details'){
                dd($jsonToArray);
            }
            $apiDetails->setStatus(1);
            $em = $this->getDoctrine()->getManager();
            $em->persist($apiDetails);
            $em->flush();
            $this->addFlash('success', 'Data has been migrated!');
            return $this->redirectToRoute('api_response_list');
        }else{
            $this->addFlash('error', 'Somthing Wrong!');
            return $this->redirectToRoute('api_response_list');
        }*/
        return new Response(false);
    }

    /**
     * @Route("/companySpeciesWiseAvarageFCRBefore", methods={"GET"}, name="companySpeciesWiseAvarageFCRBefore")
     */

    public function companySpeciesWiseAvarageFCRBefore(){
        set_time_limit(0);
        ignore_user_abort(true);

        $entities = $this->getDoctrine()->getRepository(Api::class)->companySpeciesWiseAvarageFCRBefore(1);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($entities));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }


    /**
     * @Route("/fishSalesPrice", methods={"GET"}, name="fishSalesPrice")
     * @param Request $request
     * @param ParameterBagInterface $parameterBag
     * @return JsonResponse|Response
     */

    public function fishSalesPrice(Request $request, ParameterBagInterface $parameterBag){
        set_time_limit(0);
        ignore_user_abort(true);
        if ($request->getMethod() == 'GET' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')){
            $entities = $this->getDoctrine()->getRepository(Api::class)->fishSalesPrice(1);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($entities));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }
        return new JsonResponse([
            'status' => 404,
            'message' => 'Not Found!'
        ]);


    }

    /**
     * @Route("/companyWiseFeedSaleFish", methods={"GET"}, name="companyWiseFeedSaleFish")
     */

    public function companyWiseFeedSaleFish(){
        set_time_limit(0);
        ignore_user_abort(true);

        $entities = $this->getDoctrine()->getRepository(Api::class)->companyWiseFeedSaleFish(1);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($entities));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }

    /**
     * @Route("/company", methods={"GET"}, name="company")
     */
    public function company()
    {
        set_time_limit(0);
        ignore_user_abort(true);

        $entities = $this->getDoctrine()->getRepository(Api::class)->company(1);
        $response = New Response();
        $response->headers->set("Content-Type","application/json");
        $response->setContent(json_encode($entities));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }

    /**
     * @Route("/competitorsCompany", methods={"GET"}, name="competitorsCompany")
     */
    public function competitorsCompany()
    {
        set_time_limit(0);
        ignore_user_abort(true);

        $entities = $this->getDoctrine()->getRepository(Api::class)->competitorsCompany(1);
        $response = New Response();
        $response->headers->set("Content-Type","application/json");
        $response->setContent(json_encode($entities));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }

    /**
     * @Route("/complain", name="complain_api")
     * @param Request $request
     * @param ParameterBagInterface $parameterBag
     * @return JsonResponse
     */
    public function complain(Request $request, ParameterBagInterface $parameterBag)
    {
        set_time_limit(0);
        ignore_user_abort(true);

        if ($request->getMethod() == 'POST' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')){
            $data = $request->request->all();
            $findEmployee = $this->getDoctrine()->getRepository(User::class)->find($data['employee_id']);
            $findAgent = $this->getDoctrine()->getRepository(Agent::class)->find($data['agent_id']);
            $findFarmer = $this->getDoctrine()->getRepository(CrmCustomer::class)->find($data['farmer_id']);
            if ($findEmployee && $findAgent && $findFarmer){
                $em = $this->getDoctrine()->getManager();

                $complain = new FarmerComplain();
                $complain->setEmployee($findEmployee);
                $complain->setAgent($findAgent);
                $complain->setFarmer($findFarmer);

                $em->persist($complain);
                $em->flush();

                $comments = json_decode($data['comments'], true);
                foreach ($comments as $comment) {
                    $fileName = '';
                    if (preg_match('/^data:image\/(\w+);base64,/', $comment['attachment'], $type)){
                        $extension = $type[1];
                        $attachment = substr($comment['attachment'], strpos($comment['attachment'], ',') + 1);
                        $attachment = str_replace( ' ', '+', $attachment );
                        $attachment = base64_decode($attachment);
                        $fileName = $data['farmer_id'] . '_' . $comment['comment'] . '_' . date('d-m-Y') . '_' . time() . '.' . $extension;

                        file_put_contents($parameterBag->get('uploadDir') . '/public/uploads/crm/visit/complain/' . $fileName, $attachment);
                    }

                    $details = new FarmerComplainDetails();
                    $details->setComplain($complain);
                    $details->setComment($comment['comment']);
                    $details->setAttachment($fileName);
                    $em->persist($details);
                    $em->flush();

                }

                return new JsonResponse([
                    'status' => 200,
                    'message' => 'success'
                ]);
            }else{
                return new JsonResponse([
                    'status' => 500,
                    'message' => 'Server Error!'
                ]);
            }
        }

        return new JsonResponse([
            'status' => 500,
            'message' => 'Server Error!'
        ]);

    }

    /**
     * @Route("/agent-purpose", name="agent_purpose_api")
     * @param Request $request
     * @param ParameterBagInterface $parameterBag
     * @return JsonResponse|Response
     */
    public function agentPurposeApi(Request $request, ParameterBagInterface $parameterBag)
    {
        set_time_limit(0);
        ignore_user_abort(true);

        if ($request->getMethod() == 'GET' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')){
            $records =$this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType'=>'AGENT_PURPOSE'));
            $data = [];
            foreach ($records as $key => $record) {
                $data[$key]['id'] = $record->getId();
                $data[$key]['name'] = $record->getName();
            }
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }
        return new JsonResponse([
            'status' => 404,
            'message' => 'Not Found!'
        ]);

    }

    /**
     * @Route("/sub-agent-purpose", name="sub-agent_purpose_api")
     * @param Request $request
     * @param ParameterBagInterface $parameterBag
     * @return JsonResponse|Response
     */
    public function subAgentPurposeApi(Request $request, ParameterBagInterface $parameterBag)
    {
        set_time_limit(0);
        ignore_user_abort(true);

        if ($request->getMethod() == 'GET' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')){
            $records =$this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType'=>'SUB_AGENT_PURPOSE'));
            $data = [];
            foreach ($records as $key => $record) {
                $data[$key]['id'] = $record->getId();
                $data[$key]['name'] = $record->getName();
            }
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }
        return new JsonResponse([
            'status' => 404,
            'message' => 'Not Found!'
        ]);

    }

    /**
     * @Route("/other-agent-purpose", name="other-agent_purpose_api")
     * @param Request $request
     * @param ParameterBagInterface $parameterBag
     * @return JsonResponse|Response
     */
    public function otherAgentPurposeApi(Request $request, ParameterBagInterface $parameterBag)
    {
        set_time_limit(0);
        ignore_user_abort(true);

        if ($request->getMethod() == 'GET' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')){
            $records =$this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType'=>'OTHER_AGENT_PURPOSE'));
            $data = [];
            foreach ($records as $key => $record) {
                $data[$key]['id'] = $record->getId();
                $data[$key]['name'] = $record->getName();
            }
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }
        return new JsonResponse([
            'status' => 404,
            'message' => 'Not Found!'
        ]);
    }

    /**
     * @Route("/create-sub-agent", name="create_sub_agent_api")
     * @param Request $request
     * @param ParameterBagInterface $parameterBag
     * @return Response
     */
    public function createSubAgent(Request $request, ParameterBagInterface $parameterBag)
    {
        set_time_limit(0);
        ignore_user_abort(true);

        if ($request->getMethod() == 'POST' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')){
            $entity = new Agent();
            $allRequestData = $request->request->all();

            $group = $this->getDoctrine()->getRepository(\App\Entity\Core\Setting::class)->findOneBy(array('slug'=>'sub-agent'));
            $location = $this->getDoctrine()->getRepository(Location::class)->find($allRequestData['location']);
            $entity->setName($allRequestData['name']);
            $entity->setAddress($allRequestData['address']);
            $entity->setMobile($allRequestData['mobile']);
            $entity->setAgentGroup($group);
            $entity->setUpozila($location);
            $entity->setDistrict($location->getParent());
            if($allRequestData['agent']){
                $agent = $this->getDoctrine()->getRepository(Agent::class)->find($allRequestData['agent']);
                $entity->setParent($agent);
            }
            $em = $this->getDoctrine()->getManager();

            try {
                $em->persist($entity);
                $em->flush();

                return new JsonResponse([
                    'status' => 200,
                    'message' => 'Success'
                ]);
            }catch (Exception $e){
                $response = new Response();
                $response->headers->set('Content-Type', 'application/json');
                $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
                $response->setContent($e->getMessage());
                return $response;
            }
        }

        return new JsonResponse([
            'status' => 500,
            'message' => 'Server Error!'
        ]);
    }


    /**
     * @Route("/create-other-agent", name="create_other_agent_api")
     * @param Request $request
     * @param ParameterBagInterface $parameterBag
     * @return Response
     */
    public function createOtherAgent(Request $request, ParameterBagInterface $parameterBag)
    {
        set_time_limit(0);
        ignore_user_abort(true);

        if ($request->getMethod() == 'POST' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')){
            $entity = new Agent();
            $allRequestData = $request->request->all();
            $group = $this->getDoctrine()->getRepository(\App\Entity\Core\Setting::class)->findOneBy(array('slug'=>'other-agent'));
            $location = $this->getDoctrine()->getRepository(Location::class)->find($allRequestData['location']);
            $entity->setName($allRequestData['name']);
            $entity->setAddress($allRequestData['address']);
            $entity->setMobile($allRequestData['mobile']);
            $entity->setAgentGroup($group);
            $entity->setUpozila($location);
            $entity->setDistrict($location->getParent());
            $entity->setCreated(new \DateTime('now'));
            $em = $this->getDoctrine()->getManager();

            try {
                $em->persist($entity);
                $em->flush();

                return new JsonResponse([
                    'status' => 200,
                    'message' => 'Success'
                ]);
            }catch (Exception $e){
                $response = new Response();
                $response->headers->set('Content-Type', 'application/json');
                $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
                $response->setContent($e->getMessage());
                return $response;
            }
        }

        return new JsonResponse([
            'status' => 500,
            'message' => 'Server Error!'
        ]);
    }

    /**
     * @param Request $request
     * @param ParameterBagInterface $parameterBag
     * @Route("/chick-life-cycle", name="chick_life_cycle_api")
     * @return JsonResponse|Response
     */
    public function getChickLifeCycle(Request $request, ParameterBagInterface $parameterBag)
    {
        set_time_limit(0);
        ignore_user_abort(true);

        if ($request->getMethod() == 'POST' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')){
            $parameters = $request->request->all();
            $records = $this->getDoctrine()->getRepository(Api::class)->getLifeCycleData($parameters);

            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            if ($records){
                $response->setContent(json_encode($records));
            }else{
                $response->setContent(json_encode([
                    'status' => 404,
                    'message' => 'Not found!'
                ]));
            }
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }
        return new JsonResponse([
            'status' => 404,
            'message' => 'Not Found!'
        ]);
    }

    /**
     * @param Request $request
     * @param ParameterBagInterface $parameterBag
     * @Route("/layer-life-cycle", name="layer_life_cycle_api")
     * @return JsonResponse|Response
     */
    public function getLayerLifeCycle(Request $request, ParameterBagInterface $parameterBag)
    {
        set_time_limit(0);
        ignore_user_abort(true);

        if ($request->getMethod() == 'POST' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')){
            $parameters = $request->request->all();
            $records = $this->getDoctrine()->getRepository(Api::class)->getLayerLifeCycleData($parameters);

            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            if ($records){
                $response->setContent(json_encode($records));
            }else{
                $response->setContent(json_encode([
                    'status' => 404,
                    'message' => 'Not found!'
                ]));
            }
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }
        return new JsonResponse([
            'status' => 404,
            'message' => 'Not Found!'
        ]);
    }

    /**
     * @param Request $request
     * @param ParameterBagInterface $parameterBag
     * @Route("/cattle-life-cycle", name="cattle_life_cycle_api")
     * @return JsonResponse|Response
     */
    public function getCattleLifeCycle(Request $request, ParameterBagInterface $parameterBag)
    {
        set_time_limit(0);
        ignore_user_abort(true);

        if ($request->getMethod() == 'POST' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')){
            $parameters = $request->request->all();
            $records = $this->getDoctrine()->getRepository(Api::class)->getCattleLifeCycleData($parameters);

            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            if ($records){
                $response->setContent(json_encode($records));
            }else{
                $response->setContent(json_encode([
                    'status' => 404,
                    'message' => 'Not found!'
                ]));
            }
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }
        return new JsonResponse([
            'status' => 404,
            'message' => 'Not Found!'
        ]);
    }

    /**
     * @param Request $request
     * @param ParameterBagInterface $parameterBag
     * @return Response
     * @Route("/app/version", name="app_version")
     */
    public function appVersion(Request $request, ParameterBagInterface $parameterBag)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $response = new Response();

        if ($request->getMethod() == 'GET' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')){
            $version = $this->getDoctrine()->getRepository(Api::class)->getCurrentVersion();
            $response->headers->set('Content-Type', 'application/json');
            if ($version){
                $response->setContent(json_encode($version));
                $response->setStatusCode(Response::HTTP_OK);
                return $response;
            }
        }
        $response->setContent(json_encode([
            'status' => 404,
            'message' => 'Not found!'
        ]));
        return $response;

    }

    /**
     * @param Request $request
     * @param ParameterBagInterface $parameterBag
     * @return Response
     * @Route("/complain-doc", name="complain_doc_api")
     */
    public function complainDocApi(Request $request, ParameterBagInterface $parameterBag)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        if ($request->getMethod() == 'GET' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')){
            $complainDoc = $this->getDoctrine()->getRepository(Api::class)->getComplainType('COMPLAIN_DOC');
            if ($complainDoc){
                $response->setContent(json_encode($complainDoc));
                $response->setStatusCode(Response::HTTP_OK);
                return $response;
            }
            $response->setContent(json_encode([
                'status' => 404,
                'message' => 'Not found!'
            ]));
            return $response;

        }
        $response->setContent(json_encode([
            'status' => 401,
            'message' => 'Invalid Request!'
        ]));
        return $response;
    }

    /**
     * @param Request $request
     * @param ParameterBagInterface $parameterBag
     * @return Response
     * @Route("/complain-feed", name="complain_feed_api")
     */
    public function complainFeedApi(Request $request, ParameterBagInterface $parameterBag)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        if ($request->getMethod() == 'GET' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')){
            $complainFeed = $this->getDoctrine()->getRepository(Api::class)->getComplainType('COMPLAIN_FEED');
            if ($complainFeed){
                $response->setContent(json_encode($complainFeed));
                $response->setStatusCode(Response::HTTP_OK);
                return $response;
            }
            $response->setContent(json_encode([
                'status' => 404,
                'message' => 'Not found!'
            ]));
            return $response;

        }
        $response->setContent(json_encode([
            'status' => 401,
            'message' => 'Invalid Request!'
        ]));
        return $response;
    }

    /**
     * @param Request $request
     * @param ParameterBagInterface $parameterBag
     * @return Response
     * @Route("/transport", name="transport_api")
     */
    public function transportApi(Request $request, ParameterBagInterface $parameterBag)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        if ($request->getMethod() == 'GET' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')){
            $transports = $this->getDoctrine()->getRepository(Api::class)->getTransport('TRANSPORT');
            if ($transports){
                $response->setContent(json_encode($transports));
                $response->setStatusCode(Response::HTTP_OK);
                return $response;
            }
            $response->setContent(json_encode([
                'status' => 404,
                'message' => 'Not found!'
            ]));
            return $response;

        }
        $response->setContent(json_encode([
            'status' => 401,
            'message' => 'Invalid Request!'
        ]));
        return $response;
    }

    /**
     * @param Request $request
     * @param ParameterBagInterface $parameterBag
     * @return Response
     * @Route("/hatchery-nourish", name="hatchery_nourish_api")
     */
    public function nourishHatcheryApi(Request $request, ParameterBagInterface $parameterBag)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        if ($request->getMethod() == 'GET' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')){
            $hatcheries = $this->getDoctrine()->getRepository(Api::class)->getNourishHatchery('HATCHERY_NOURISH');
            if ($hatcheries){
                $response->setContent(json_encode($hatcheries));
                $response->setStatusCode(Response::HTTP_OK);
                return $response;
            }
            $response->setContent(json_encode([
                'status' => 404,
                'message' => 'Not found!'
            ]));
            return $response;

        }
        $response->setContent(json_encode([
            'status' => 401,
            'message' => 'Invalid Request!'
        ]));
        return $response;
    }


    /**
     * @Route("/labName", methods={"GET"}, name="lab_name")
     * @param Request $request
     * @param ParameterBagInterface $parameterBag
     * @return JsonResponse|Response
     */
    public function labName(Request $request, ParameterBagInterface $parameterBag)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if ($request->getMethod() == 'GET' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')){
            $entities = $this->getDoctrine()->getRepository(Api::class)->labName();
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($entities));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }
        return new JsonResponse([
            'status' => 404,
            'message' => 'Not Found!'
        ]);
    }

    /**
     * @Route("/labServiceName", methods={"GET"}, name="lab_service_name")
     * @param Request $request
     * @param ParameterBagInterface $parameterBag
     * @return JsonResponse|Response
     */
    public function labServiceName(Request $request, ParameterBagInterface $parameterBag)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if ($request->getMethod() == 'GET' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')){
            $entities = $this->getDoctrine()->getRepository(Api::class)->labServiceName();
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($entities));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }
        return new JsonResponse([
            'status' => 404,
            'message' => 'Not Found!'
        ]);


    }

    /**
     * @Route("/chick-life-cycle-in-progress", name="chick_life_cycle_in_progress")
     * @param Request $request
     * @param ParameterBagInterface $parameterBag
     * @return JsonResponse|Response
     */
    public function chickLifeCycleInProgress(Request $request, ParameterBagInterface $parameterBag)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if ($request->getMethod() == 'POST' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')){
            $parameters = $request->request->all();

            $entities = $this->getDoctrine()->getRepository(Api::class)->chickLifeCycleInProgress($parameters);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($entities));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }
        return new JsonResponse([
            'status' => 404,
            'message' => 'Not Found!'
        ]);


    }


}