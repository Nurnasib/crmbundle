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
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Terminalbd\CrmBundle\Entity\Api;
use Terminalbd\CrmBundle\Entity\ApiDetails;
use Terminalbd\CrmBundle\Entity\BroilerStandard;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\CrmVisit;
use Terminalbd\CrmBundle\Entity\FarmerComplain;
use Terminalbd\CrmBundle\Entity\FarmerComplainDetails;
use Terminalbd\CrmBundle\Entity\FcrDetails;
use Terminalbd\CrmBundle\Entity\FishLifeCycleCulture;
use Terminalbd\CrmBundle\Entity\FishLifeCycleCultureDetails;
use Terminalbd\CrmBundle\Entity\NewFarmerIntroduce\FarmerIntroduceDetails;
use Terminalbd\CrmBundle\Entity\Setting;
use Terminalbd\CrmBundle\Entity\SonaliStandard;
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
     * @param Request $request
     * @param SmsSender $smsSender
     * @param ParameterBagInterface $parameterBag
     * @return JsonResponse|Response
     */
    public function login(Request $request, SmsSender $smsSender, ParameterBagInterface $parameterBag)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if ($request->getMethod() == 'POST' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')) {
            $userId = trim($request->request->get('user_id'));
            $findUser = $this->getDoctrine()->getRepository(User::class)->findOneBy(['isDelete'=>'0','userId' => $userId]);
            if ($findUser) {
                if($findUser->isEnabled()){
                    if (!$findUser->getMobile()) {
                        $response = new Response();
                        $response->headers->set('Content-Type', 'application/json');
                        $response->setContent(json_encode([
                            'message' => 'Mobile number does not found!',
                            'status' => '404',
                        ]));
                        $response->setStatusCode(Response::HTTP_NOT_FOUND);
                        return $response;
                    }
                    $userMobile = str_replace('-', '', $findUser->getMobile());

                    $otp = (string)mt_rand(1000, 9999);
                    $message = 'Your OTP is ' . $otp . '.';
                    $smsResponse = $smsSender->sendSms($message, $userMobile);

//                $smsResponse = json_decode($smsResponse, true);

//                if ($smsResponse['message'] === 'Success'){
                    if ($findUser->getUserGroup()->getSlug() == 'administrator') {
                        $roles = $findUser->getUserGroup()->getSlug();
                    } else {
                        $roles = $findUser->getServiceMode() ? $findUser->getServiceMode()->getSlug() : '';
                    }
//                    $rolesSeparated = implode(",", $roles);
                    $upozilas = [];
                    foreach ($findUser->getUpozila() as $location) {
                        $upozilas[] = $location->getId();
                    }

                    $locations = implode(",", $upozilas);
                    $data = [
                        'userId' => $findUser->getId(),
                        'username' => $findUser->getUsername(),
                        'name' => $findUser->getName(),
                        'email' => $findUser->getEmail(),
                        'roles' => $roles,
                        'designation' => $findUser->getDesignation() ? $findUser->getDesignation()->getName() : '',
                        'lineManager' => $findUser->getLineManager() ? $findUser->getLineManager()->getId() : '',
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
                }else{
                    $response = new Response();
                    $response->headers->set('Content-Type', 'application/json');
                    $response->setContent(json_encode([
                        'message' => 'This employee is disabled!',
                        'status' => '401',
                    ]));
                    $response->setStatusCode(Response::HTTP_UNAUTHORIZED);
                    return $response;
                }
            } else {
                $response = new Response();
                $response->headers->set('Content-Type', 'application/json');
                $response->setContent(json_encode([
                    'message' => 'Unregistered employee!',
                    'status' => '401',
                ]));
                $response->setStatusCode(Response::HTTP_UNAUTHORIZED);
                return $response;
            }
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode([
            'message' => 'Invalid request!',
            'status' => '405',
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
        } else {
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
        if ($this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        } elseif ($userExist) {

            $this->getDoctrine()->getRepository('UserBundle:User')->androidUserUpdate($userExist, $formData);
            $data = array('status' => 'success');
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;

        } else {

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
        } else {
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

        if ($this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        } else {
            $entity = $this->checkApiValidation($request);
            $username = $request->request->get('username');
            $password = $request->request->get('password');
            $user = $this->getDoctrine()->getManager()->getRepository("UserBundle:User")
                ->findOneBy(array('username' => $username, 'enabled' => 1));
            if (empty($user)) {
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
        $entities = $this->getDoctrine()->getRepository(Api::class)->apiAgent(1, $locations);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($entities));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }

    /**
     * @Route("/nourish-agent", name="crm_api_rourish_agent" , methods={"POST","GET"}, options={"expose"=true})
     */
    public function apiNourishAgent()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $locations = isset($_REQUEST['locations']) ? $_REQUEST['locations'] : "";
        //$terminal = $this->getUser()->getTerminal()->getId();
        $entities = $this->getDoctrine()->getRepository(Api::class)->apiOnlyNourishAgent(1, $locations);

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
        $entities = $this->getDoctrine()->getRepository(Api::class)->customerApi(1, 'farmer', $locations);
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
        if ($request->getMethod() == 'GET' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')) {
            $locations = $request->query->get('locations');
            $entities = $this->getDoctrine()->getRepository(Api::class)->agentApi(1, 'sub-agent', $locations);
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
        if ($request->getMethod() == 'GET' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')) {
            //$terminal = $this->getUser()->getTerminal()->getId();
            $locations = $request->query->get('locations');
            $entities = $this->getDoctrine()->getRepository(Api::class)->agentApi(1, 'other-agent', $locations);
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
        $entities = $this->getDoctrine()->getRepository(Api::class)->crmVisit(1, $username);

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
        $entities = $this->getDoctrine()->getRepository(Api::class)->farmerIntroduceReport(1, $breedName, $employee);

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
        $entities = $this->getDoctrine()->getRepository(Api::class)->farmerTouchReport(1, $startDate, $endDate, $report, $employee);

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
        $entities = $this->getDoctrine()->getRepository(Api::class)->farmerTrainingReport(1, $breedName, $employee);

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
        $entities = $this->getDoctrine()->getRepository(Api::class)->poultryLifeCylceReport(1, $startDate, $endDate, $reportSlug, $customerName);
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

        $entities = $this->getDoctrine()->getRepository(Api::class)->farmVisitCattle(1, $startDate, $endDate, $employee);

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

        $entities = $this->getDoctrine()->getRepository(Api::class)->frcReportPoulty(1, $startDate, $endDate, $reportSlug, $employeeName);

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
        $slug = 'fcr-before-sale-' . $type;
        $report = $this->getDoctrine()->getRepository(Setting::class)->findOneBy(['slug' => $slug, 'settingType' => 'FARMER_REPORT', 'status' => 1]);

        $hatchery = $this->getDoctrine()->getRepository(Setting::class)->findOneBy(['id' => $data['hatchery_id'], 'status' => 1]);

        $breed = $this->getDoctrine()->getRepository(Setting::class)->findOneBy(['id' => $data['breed_id'], 'status' => 1]);

        $feed = $this->getDoctrine()->getRepository(Setting::class)->findOneBy(['id' => $data['feed_id'], 'status' => 1]);

        $feedMill = $this->getDoctrine()->getRepository(Setting::class)->findOneBy(['id' => $data['feed_Mill'], 'status' => 1]);

        $feedType = $this->getDoctrine()->getRepository(Setting::class)->findOneBy(['id' => $data['feedType'], 'status' => 1]);

        $employee = $this->getDoctrine()->getRepository(User::class)->find($data['user_id']);

        $customer = $this->getDoctrine()->getRepository(CrmCustomer::class)->find($data['customer_id']);

        $entity = new FcrDetails();
        $reportingDate = date('Y-m-d', strtotime('now'));
        $hatchingDate = date('Y-m-d', strtotime('now'));
        $proDate = date('Y-m-d', strtotime('now'));
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
        $entity->setHatchery($hatchery ? $hatchery : null);
        $entity->setBreed($breed ? $breed : null);
        $entity->setFeed($feed ? $feed : null);
        $entity->setFeedMill($feedMill ? $feedMill : null);
        $entity->setFeedType($feedType ? $feedType : null);
        $entity->setBatchNo($data['batch_no']);
        $entity->setRemarks($data['remarks']);

        if ($report->getSlug() == 'fcr-before-sale-sonali') {

            /* @var SonaliStandard $sonaliStandard */
            $sonaliStandard = $this->getDoctrine()->getRepository(SonaliStandard::class)->findOneBy(array('age' => $entity->getAgeDay()));
            if ($sonaliStandard) {
                $entity->setWeightStandard($sonaliStandard->getTargetBodyWeight());
                $entity->setFeedConsumptionStandard($sonaliStandard->getCumulativeFeedIntake());
            }
        }
        if ($report->getSlug() == 'fcr-before-sale-boiler') {

            /* @var BroilerStandard $broilerStandard */
            $broilerStandard = $this->getDoctrine()->getRepository(BroilerStandard::class)->findOneBy(array('age' => $entity->getAgeDay()));
            if ($broilerStandard) {
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
     * @Route("/dailyActivitiesPurposeForSalesAndMarketing", methods={"GET"}, name="dailyActivitiesPurposeForSalesAndMarketing")
     */
    public function dailyActivitiesPurposeForSalesAndMarketing(Request $request, ParameterBagInterface $parameterBag)
    {

        set_time_limit(0);
        ignore_user_abort(true);
        if ($request->getMethod() == 'GET' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')) {
            $entities = $this->getDoctrine()->getRepository(Api::class)->dailyActivitiesPurposeForSalesAndMarketing();
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
        if ($request->getMethod() == 'GET' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')) {
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
     * @Route("/fishFeedTypeForLifeCycle", methods={"GET"}, name="fishFeedTypeForLifeCycle")
     * @param Request $request
     * @param ParameterBagInterface $parameterBag
     * @return Response
     */
    public function fishFeedTypeForLifeCycle(Request $request, ParameterBagInterface $parameterBag)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if ($request->getMethod() == 'GET' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')) {
            $entities = $this->getDoctrine()->getRepository(Api::class)->fishFeedTypeForLifeCycle();
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
        if ($request->getMethod() == 'GET' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')) {
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
        if ($request->getMethod() == 'GET') {
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
     * @Route("/product-for-boiler-chick", methods={"GET"}, name="product_for_boiler_chick")
     */
    public function productForBoilerChick(Request $request, ParameterBagInterface $parameterBag)
    {

        set_time_limit(0);
        ignore_user_abort(true);

        if ($request->getMethod() == 'GET' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')) {
            $entities = $this->getDoctrine()->getRepository(Api::class)->productForBoilerChick();
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
     * @Route("/product-for-layer-chick", methods={"GET"}, name="product_for_layer_chick")
     */
    public function productForLayerChick(Request $request, ParameterBagInterface $parameterBag)
    {

        set_time_limit(0);
        ignore_user_abort(true);

        if ($request->getMethod() == 'GET' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')) {
            $entities = $this->getDoctrine()->getRepository(Api::class)->productForLayerChick();
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
     * @Route("/mainculturespecies", methods={"GET"}, name="mainculturespecies")
     * @param Request $request
     * @param ParameterBagInterface $parameterBag
     * @return Response
     */
    public function mainCultureSpecies(Request $request, ParameterBagInterface $parameterBag)
    {

        set_time_limit(0);
        ignore_user_abort(true);
        if ($request->getMethod() == 'GET') {
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

        if ($data) {
            if(!in_array($data['process'],['crm_fish_life_cycle_detail_species'])){
                $em = $this->getDoctrine()->getManager();
                $findEmployee = $this->getDoctrine()->getRepository(User::class)->find($data['employee_id']);
                $findParent = $this->getDoctrine()->getRepository(Api::class)->findOneBy(['batchNo' => $data['batch_id'], 'employee' => $findEmployee]);
                if (!$findParent) {
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
    /*    public function apiResponseList()
        {
            $list = $this->getDoctrine()->getRepository(Api::class)->getData();
            return $this->render('@TerminalbdCrm/api/api-response-list.html.twig', [
                'list' => $list,
            ]);
        }*/

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

    public function companySpeciesWiseAvarageFCRBefore()
    {
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

    public function fishSalesPrice(Request $request, ParameterBagInterface $parameterBag)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if ($request->getMethod() == 'GET' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')) {
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

    public function companyWiseFeedSaleFish()
    {
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
        $response = new Response();
        $response->headers->set("Content-Type", "application/json");
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
        $response = new Response();
        $response->headers->set("Content-Type", "application/json");
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

        if ($request->getMethod() == 'POST' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')) {
            $data = $request->request->all();
            $findEmployee = $this->getDoctrine()->getRepository(User::class)->find($data['employee_id']);
            $findAgent = $this->getDoctrine()->getRepository(Agent::class)->find($data['agent_id']);
            $findFarmer = $this->getDoctrine()->getRepository(CrmCustomer::class)->find($data['farmer_id']);
            $complainType = null;
            if (isset($data['complain_type_id']) && $data['complain_type_id'] != '') {
                $complainType = $this->getDoctrine()->getRepository(Setting::class)->find($data['complain_type_id']);
            }
            if ($findEmployee && $findAgent && $findFarmer) {
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
                    if (preg_match('/^data:image\/(\w+);base64,/', $comment['attachment'], $type)) {
                        $extension = $type[1];
                        $attachment = substr($comment['attachment'], strpos($comment['attachment'], ',') + 1);
                        $attachment = str_replace(' ', '+', $attachment);
                        $attachment = base64_decode($attachment);
                        $fileName = $data['farmer_id'] . '_' . $comment['comment'] . '_' . date('d-m-Y') . '_' . time() . '.' . $extension;

                        file_put_contents($parameterBag->get('projectRoot') . '/public/uploads/crm/visit/complain/' . $fileName, $attachment);
                    }

                    $details = new FarmerComplainDetails();
                    $details->setComplain($complain);
                    $details->setComment($comment['comment']);
                    $details->setAttachment($fileName);
                    if ($complainType) {
                        $details->setComplainType($complainType);
                    }
                    $em->persist($details);
                    $em->flush();

                }

                return new JsonResponse([
                    'status' => 200,
                    'message' => 'success'
                ]);
            } else {
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

        if ($request->getMethod() == 'GET' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')) {
            $records = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType' => 'AGENT_PURPOSE','status'=>1));
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

        if ($request->getMethod() == 'GET' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')) {
            $records = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType' => 'SUB_AGENT_PURPOSE'));
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

        if ($request->getMethod() == 'GET' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')) {
            $records = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType' => 'OTHER_AGENT_PURPOSE'));
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

        if ($request->getMethod() == 'POST' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')) {
            $entity = new Agent();
            $allRequestData = $request->request->all();

            $group = $this->getDoctrine()->getRepository(\App\Entity\Core\Setting::class)->findOneBy(array('slug' => 'sub-agent'));
            $location = $this->getDoctrine()->getRepository(Location::class)->find($allRequestData['location']);
            $entity->setName($allRequestData['name']);
            $entity->setAddress($allRequestData['address']);
            $entity->setMobile($allRequestData['mobile']);
            $entity->setAgentGroup($group);
            $entity->setUpozila($location);
            $entity->setDistrict($location->getParent());
            if ($allRequestData['agent']) {
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
            } catch (Exception $e) {
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

        if ($request->getMethod() == 'POST' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')) {
            $entity = new Agent();
            $allRequestData = $request->request->all();
            $group = $this->getDoctrine()->getRepository(\App\Entity\Core\Setting::class)->findOneBy(array('slug' => 'other-agent'));
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
            } catch (Exception $e) {
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

        if ($request->getMethod() == 'POST' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')) {
            $parameters = $request->request->all();
            $records = $this->getDoctrine()->getRepository(Api::class)->getLifeCycleData($parameters);

            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            if ($records) {
                $response->setContent(json_encode($records));
            } else {
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

        if ($request->getMethod() == 'POST' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')) {
            $parameters = $request->request->all();
            $records = $this->getDoctrine()->getRepository(Api::class)->getLayerLifeCycleData($parameters);

            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            if ($records) {
                $response->setContent(json_encode($records));
            } else {
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

        if ($request->getMethod() == 'POST' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')) {
            $parameters = $request->request->all();
            $records = $this->getDoctrine()->getRepository(Api::class)->getCattleLifeCycleData($parameters);

            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            if ($records) {
                $response->setContent(json_encode($records));
            } else {
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

        if ($request->getMethod() == 'GET' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')) {
            $version = $this->getDoctrine()->getRepository(Api::class)->getCurrentVersion();
            $response->headers->set('Content-Type', 'application/json');
            if ($version) {
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

        if ($request->getMethod() == 'GET' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')) {
            $complainDoc = $this->getDoctrine()->getRepository(Api::class)->getComplainType('COMPLAIN_DOC');
            if ($complainDoc) {
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

        if ($request->getMethod() == 'GET' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')) {
            $complainFeed = $this->getDoctrine()->getRepository(Api::class)->getComplainType('COMPLAIN_FEED');
            if ($complainFeed) {
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

        if ($request->getMethod() == 'GET' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')) {
            $transports = $this->getDoctrine()->getRepository(Api::class)->getTransport('TRANSPORT');
            if ($transports) {
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

        if ($request->getMethod() == 'GET' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')) {
            $hatcheries = $this->getDoctrine()->getRepository(Api::class)->getNourishHatchery('HATCHERY_NOURISH');
            if ($hatcheries) {
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
        if ($request->getMethod() == 'GET' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')) {
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
        if ($request->getMethod() == 'GET' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')) {
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
        if ($request->getMethod() == 'POST' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')) {
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

    /**
     * @Route("/layer-life-cycle-in-progress", name="layer_life_cycle_in_progress")
     * @param Request $request
     * @param ParameterBagInterface $parameterBag
     * @return JsonResponse|Response
     */
    public function layerLifeCycleInProgress(Request $request, ParameterBagInterface $parameterBag)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if ($request->getMethod() == 'POST' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')) {
            $parameters = $request->request->all();

            $entities = $this->getDoctrine()->getRepository(Api::class)->layerLifeCycleInProgress($parameters);
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
     * @param Request $request
     * @param ParameterBagInterface $parameterBag
     * @return JsonResponse|Response
     * @Route("/employee-location", name="employee_location")
     */
    public function employeeLocation(Request $request, ParameterBagInterface $parameterBag)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if ($request->getMethod() == 'POST' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')) {
            $lineManager = (int)$request->request->get('line_manager_id');
            $lineManager = $this->getDoctrine()->getRepository(User::class)->find($lineManager);

            if (!$lineManager){
                $response = new Response();
                $response->headers->set('Content-Type', 'application/json');
                $response->setContent(json_encode(['message' => 'Unregistered user', 'status' => Response::HTTP_NOT_FOUND]));
                $response->setStatusCode(Response::HTTP_NOT_FOUND);
                return $response;
            }

            if (in_array('ROLE_CRM_POULTRY_ADMIN', (array)$lineManager->getRoles()) || in_array('ROLE_CRM_CATTLE_ADMIN', (array)$lineManager->getRoles()) || in_array('ROLE_CRM_AQUA_ADMIN', (array)$lineManager->getRoles()) || in_array('ROLE_CRM_SALES_MARKETING_ADMIN', (array)$lineManager->getRoles())){
                $entities = $this->getDoctrine()->getRepository(Api::class)->getEmployeeLocation($lineManager, []);
                $response = new Response();
                $response->headers->set('Content-Type', 'application/json');
                $response->setContent(json_encode($entities));
                $response->setStatusCode(Response::HTTP_OK);
                return $response;
            }

            $userType = $this->getDoctrine()->getRepository(\App\Entity\Core\Setting::class)->findOneBy(['slug' => 'employee']);
            $allUser = $this->getDoctrine()->getRepository(User::class)->findBy(['enabled' => 1, 'userGroup' => $userType]); //136

            $users = [];
            foreach ($allUser as $user) {
                if ($user->getLineManager() && $user->isEnabled()) {
                    $users[] = [
                        'id' => $user->getId(),
                        'lineManager' => $user->getLineManager()->getId()
                    ];
                }
            }

            $members = $this->getMemberTree($users, $lineManager->getId());
            $ids = [];
            foreach ($members as $key => $item) {
                if (isset($item['ids'])) {
                    array_push($members[$key]['ids'], $item['id']);
                } else {
                    $members[$key]['ids'][] = $item['id'];

                }
                foreach ($members[$key]['ids'] as $id) {
                    if (!in_array($id, $ids)) {
                        $ids[] = $id;
                    }
                }
            }

            $entities = $this->getDoctrine()->getRepository(Api::class)->getEmployeeLocation($lineManager, $ids);

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

    private function getMemberTree(array $allEmployee, $lineManagerId)
    {
        $members = array();

        foreach ($allEmployee as $employee) {

            if ($employee['lineManager']) {
                if ($employee['lineManager'] == $lineManagerId) {

                    $member = $this->getMemberTree($allEmployee, $employee['id']);
                    if ($member) {
                        foreach ($member as $child) {
                            $employee['ids'][] = $child['id'];
                        }
                    }
                    $members[] = $employee;
                }
            }
        }
        return $members;

    }
    /*    private function getMemberTree($membersId, $lineManager)
        {
            $members = $this->getDoctrine()->getRepository(Api::class)->getMembers($lineManager);
            if ($members){
                foreach ($members as $member) {
                    array_push($membersId, $member['id']);
                    $child = $this->getMemberTree($membersId, $member['id']);
                    if ($child){
                        $membersId[] = $child;
                    }

                }
            }
            return $membersId;

        }*/

    /**
     * @param Request $request
     * @param ParameterBagInterface $parameterBag
     * @return JsonResponse|Response
     * @Route("/employee-location-update", name="employee_location_update")
     */
    public function setEmployeeLocation(Request $request, ParameterBagInterface $parameterBag)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if ($request->getMethod() == 'POST' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')) {
            $parameters = $request->request->all();

            if ($parameters['employee_id']) {
                $findUser = $this->getDoctrine()->getRepository(User::class)->find($parameters['employee_id']);
                if ($findUser) {
                    $findUser->setLatitude($parameters['latitude'] ?: null);
                    $findUser->setLongitude($parameters['longitude'] ?: null);
                    $this->getDoctrine()->getManager()->flush();

                    return new JsonResponse([
                        'status' => 200,
                        'message' => 'success'
                    ]);
                }
            }
        }
        return new JsonResponse([
            'status' => 404,
            'message' => 'Not Found!'
        ]);
    }

    /**
     * @param Request $request
     * @param ParameterBagInterface $parameterBag
     * @return JsonResponse
     * @Route("/regions", name="regions")
     */
    public function regions(Request $request, ParameterBagInterface $parameterBag)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if ($request->getMethod() == 'GET' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')) {
            $regions = $this->getDoctrine()->getRepository(Location::class)->findBy(['level' => 3]);
            $data = [];
            foreach ($regions as $region) {
                $data[] = [
                    'id' => $region->getId(),
                    'name' => $region->getName(),
                ];
            }
            return new JsonResponse($data);
        }
        return new JsonResponse([
            'status' => 404,
            'message' => 'Not Found!'
        ]);

    }

    /**
     * @param Request $request
     * @param ParameterBagInterface $parameterBag
     * @return JsonResponse
     * @Route("/chick-type", name="chick_type")
     */
    public function chickType(Request $request, ParameterBagInterface $parameterBag)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if ($request->getMethod() == 'GET' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')) {
            $chickTypes = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType' => 'CHICK_TYPE', 'status' => 1));

            $data = [];
            foreach ($chickTypes as $chickType) {
                $data[] = [
                    'id' => $chickType->getId(),
                    'name' => $chickType->getName(),
                ];
            }
            return new JsonResponse($data);
        }
        return new JsonResponse([
            'status' => 404,
            'message' => 'Not Found!'
        ]);

    }

    /**
     * @param Request $request
     * @param ParameterBagInterface $parameterBag
     * @return JsonResponse
     * @Route("/poultry-meat-egg-type", name="poultry_meat_egg_type")
     */
    public function poultryMeatEggType(Request $request, ParameterBagInterface $parameterBag)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if ($request->getMethod() == 'GET' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')) {
            $breedTypes = $this->getDoctrine()->getRepository(Setting::class)->findBy(['settingType' => 'MEAT_EGG_TYPE', 'status' => 1]);

            $data = [];
            foreach ($breedTypes as $breedType) {
                $data[] = [
                    'id' => $breedType->getId(),
                    'name' => $breedType->getName(),
                ];
            }
            return new JsonResponse($data);
        }
        return new JsonResponse([
            'status' => 404,
            'message' => 'Not Found!'
        ]);

    }

    /**
     * @param Request $request
     * @param ParameterBagInterface $parameterBag
     * @return JsonResponse
     * @Route("/complain-type", name="complain_type")
     */
    public function complainType(Request $request, ParameterBagInterface $parameterBag)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if ($request->getMethod() == 'GET' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')) {
            $types = $this->getDoctrine()->getRepository(Setting::class)->findBy(['settingType' => 'COMPLAIN_TYPE', 'status' => 1]);

            $data = [];
            foreach ($types as $type) {
                $data[] = [
                    'id' => $type->getId(),
                    'name' => $type->getName(),
                ];
            }
            return new JsonResponse($data);
        }
        return new JsonResponse([
            'status' => 404,
            'message' => 'Not Found!'
        ]);

    }


    /**
     * @param Request $request
     * @param ParameterBagInterface $parameterBag
     * @return JsonResponse
     * @Route("/fcr-hatchery-company", name="fcr_hatchery_company")
     */
    public function fcrHatcheryCompany(Request $request, ParameterBagInterface $parameterBag)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if ($request->getMethod() == 'GET' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')) {
            $fcrCompanies = $this->getDoctrine()->getRepository(Api::class)->fcrHatcheryCompany();

            $data = [];
            foreach ($fcrCompanies as $company) {
                $data[] = [
                    'id' => $company['id'],
                    'name' => $company['name'],
                ];
            }
            return new JsonResponse($data);
        }
        return new JsonResponse([
            'status' => 404,
            'message' => 'Not Found!'
        ]);

    }

    /**
     * @param Request $request
     * @param ParameterBagInterface $parameterBag
     * @return JsonResponse
     * @Route("/visit-working-mode", name="visit_working_mode")
     */
    public function visitWorkingMode(Request $request, ParameterBagInterface $parameterBag)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if ($request->getMethod() == 'GET' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')) {
            $workingModes = $this->getDoctrine()->getRepository(Setting::class)->findBy(['status' => 1, 'settingType' => 'WORKING_MODE']);

            $data = [];
            foreach ($workingModes as $workingMode) {
                $data[] = [
                    'id' => $workingMode->getId(),
                    'name' => $workingMode->getName(),
                ];
            }
            return new JsonResponse($data);
        }
        return new JsonResponse([
            'status' => 404,
            'message' => 'Not Found!'
        ]);

    }

    /**
     * @param Request $request
     * @param ParameterBagInterface $parameterBag
     * @return JsonResponse
     * @Route("/create-farmer", name="create_farmer")
     */
    public function createFarmer(Request $request, ParameterBagInterface $parameterBag)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if ($request->getMethod() == 'POST' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')) {
            $parameters = $request->request->all();

            $agent = null;
            $subAgent = null;
            $otherAgent = null;
            $location = null;

            if (isset($parameters['agentId']) && !empty($parameters['agentId'])){
                $agent = $this->getDoctrine()->getRepository(Agent::class)->find($parameters['agentId']);
            }
            if (isset($parameters['subAgentId']) && !empty($parameters['subAgentId'])){
                $subAgent = $this->getDoctrine()->getRepository(Agent::class)->find($parameters['subAgentId']);
            }
            if (isset($parameters['otherAgentId']) && !empty($parameters['otherAgentId'])){
                $otherAgent = $this->getDoctrine()->getRepository(Agent::class)->find($parameters['otherAgentId']);
            }
            if (isset($parameters['locationId']) && !empty($parameters['locationId'])) {
                $location = $this->getDoctrine()->getRepository(Location::class)->find($parameters['locationId']);
            }
            if (
                (isset($parameters['name']) && !empty($parameters['name'])) &&
                (isset($parameters['mobile']) && !empty($parameters['mobile'])) &&
                ($agent || $subAgent || $otherAgent) &&
                (isset($parameters['feedId']) && !empty($parameters['feedId'])) &&
                (isset($parameters['farmerType']) && !empty($parameters['farmerType'])) &&
                (isset($parameters['employeeId']) && !empty($parameters['employeeId']))
            ) {

                $farmerType = $this->getDoctrine()->getRepository(Setting::class)->find($parameters['farmerType']);
                $feed = $this->getDoctrine()->getRepository(Setting::class)->find($parameters['feedId']);
                $employee = $this->getDoctrine()->getRepository(User::class)->find($parameters['employeeId']);
                $farmerGroup = $this->getDoctrine()->getRepository(Setting::class)->findOneBy(['slug' => 'farmer']);

//                duplicate check
                $existingFarmerCheck= $this->getDoctrine()->getRepository(CrmCustomer::class)->duplicateCustomerCheckByMobileAndType($parameters['mobile'], $parameters['farmerType']);

                if($existingFarmerCheck){
                    return new JsonResponse(['statusCode'=>'409','message'=>'This Farmar Already Exist.']);
                }else{
                    // Add new Farmer
                    $newFarmer = new CrmCustomer();
                    $newFarmer->setName($parameters['name']);
                    $newFarmer->setMobile($parameters['mobile']);
                    $newFarmer->setAddress(isset($parameters['address']) ? $parameters['address'] : null);
                    $newFarmer->setAgent($agent ?: ($subAgent ?: ($otherAgent ?: null)));
                    $newFarmer->setOtherAgent($otherAgent);
                    $newFarmer->setLocation($location);
                    $newFarmer->setCustomerGroup($farmerGroup);
                    $newFarmer->setCreated(new \DateTime('now'));
                    $this->getDoctrine()->getManager()->persist($newFarmer);
                    $this->getDoctrine()->getManager()->flush();

                    // Introduce new Farmer
                    $introduceFarmer = new FarmerIntroduceDetails();
                    $introduceFarmer->setAgent($agent ?: ($subAgent ?: ($otherAgent ?: null)));
                    $introduceFarmer->setCustomer($newFarmer);
                    $introduceFarmer->setSubAgent($subAgent);
                    $introduceFarmer->setEmployee($employee);
                    $introduceFarmer->setOtherAgent($otherAgent);
                    $introduceFarmer->setFarmerType($farmerType);
                    if ($feed->getName() != 'Nourish'){
                        $introduceFarmer->setOtherFeed($feed);
                    }
                    $introduceFarmer->setFeed($feed);
                    $introduceFarmer->setCultureSpeciesItemAndQty(isset($parameters['cultureSpeciesItemAndQty']) ? $parameters['cultureSpeciesItemAndQty'] : null);
                    $introduceFarmer->setCreatedAt(new \DateTime('now'));

                    if ((isset($parameters['agentId']) && !empty($parameters['agentId'])) && ($feed && $feed->getName() == 'Nourish')){
                        $introduceFarmer->setIntroduceDate(new \DateTime('now'));
                    }else{
                        $introduceFarmer->setIntroduceDate(null);
                    }

                    $this->getDoctrine()->getManager()->persist($introduceFarmer);
                    $this->getDoctrine()->getManager()->flush();

                    return new JsonResponse([
                        'statusCode' => 201,
                        'message' => 'success'
                    ]);
                }
            }else{
                return new JsonResponse([
                    'statusCode' => 422,
                    'message' => 'invalid data format'
                ]);
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
     * @return JsonResponse
     * @Route("/training-material", name="training_material")
     */
    public function trainingMaterial(Request $request, ParameterBagInterface $parameterBag)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if ($request->getMethod() == 'POST' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')) {

            $user = $this->getDoctrine()->getRepository(User::class)->find($request->request->get('user_id'));

            if ($user){
                if($user->getServiceMode() && $user->getServiceMode()->getSlug() != 'sales-marketing' ) {
                    $serviceModeExplode = explode('-', $user->getServiceMode()->getSlug());
                    $lastElement = end($serviceModeExplode);
                    $breedName = $this->getDoctrine()->getRepository(Setting::class)->findOneBy(['settingType' => 'BREED_NAME', 'slug' => $lastElement . '-breed', 'status' => 1]);
                    $materials = $this->getDoctrine()->getRepository(Setting::class)->findBy(['settingType' => 'TRAINING_MATERIAL', 'parent' => $breedName]);
                    $data = [];
                    foreach ($materials as $material) {
                        $data[] = [
                            'id' => $material->getId(),
                            'name' => $material->getName(),
                        ];
                    }
                    return new JsonResponse($data);
                }else{
                    return new JsonResponse([
                        'status' => 404,
                        'message' => 'Not Found!'
                    ]);
                }

            }else{
                return new JsonResponse([
                    'status' => 404,
                    'message' => 'Not Found!'
                ]);
            }
        }
        return new JsonResponse([
            'status' => 404,
            'message' => 'Not Found!'
        ]);

    }
    /**
     * @param Request $request
     * @param ParameterBagInterface $parameterBag
     * @return JsonResponse
     * @Route("/breed-name", name="breed_name")
     */
    public function breedName(Request $request, ParameterBagInterface $parameterBag)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if ($request->getMethod() == 'GET' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')) {
            $breedNames = $this->getDoctrine()->getRepository(Setting::class)->findBy(['settingType' => 'BREED_NAME', 'status' => 1]);

            $data = [];
            foreach ($breedNames as $breedName) {
                $data[] = [
                    'id' => $breedName->getId(),
                    'name' => $breedName->getName(),
                ];
            }
            return new JsonResponse($data);
        }
        return new JsonResponse([
            'status' => 404,
            'message' => 'Not Found!'
        ]);

    }


    /**
     * @Route("/agent-purpose-for-report", name="agent_purpose_for_report_api")
     * @param Request $request
     * @param ParameterBagInterface $parameterBag
     * @return JsonResponse|Response
     */
    public function agentPurposeForReportApi(Request $request, ParameterBagInterface $parameterBag)
    {
        set_time_limit(0);
        ignore_user_abort(true);

        if ($request->getMethod() == 'GET' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')) {
            $records = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType' => 'AGENT_PURPOSE', 'slug'=>['farmer-training','agent-upgradation']));
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
     * @Route("/selectFarmTypeForMarketing", methods={"GET"}, name="selectFarmTypeForMarketing")
     * @param Request $request
     * @param ParameterBagInterface $parameterBag
     * @return JsonResponse|Response
     */
    public function selectFarmTypeForMarketing(Request $request, ParameterBagInterface $parameterBag)
    {

        set_time_limit(0);
        ignore_user_abort(true);
        if ($request->getMethod() == 'GET' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')) {

            $entities = $this->getDoctrine()->getRepository(Api::class)->selectFarmTypeForMarketing();
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
     * @Route("/farmSelectReportForMarketing", methods={"GET"}, name="farmSelectReportMarketing")
     * @param Request $request
     * @param ParameterBagInterface $parameterBag
     * @return JsonResponse|Response
     */
    public function farmSelectReportForMarketing(Request $request, ParameterBagInterface $parameterBag)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if ($request->getMethod() == 'GET' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')) {

            $entities = $this->getDoctrine()->getRepository(Api::class)->farmSelectReportForMarketing();

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
     * @Route("/productName", methods={"GET"}, name="productName")
     * @param Request $request
     * @param ParameterBagInterface $parameterBag
     * @return Response
     */
    public function productName(Request $request, ParameterBagInterface $parameterBag)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if ($request->getMethod() == 'GET' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')) {
            $entities = $this->getDoctrine()->getRepository(Api::class)->productName();
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
     * @Route("/challengeName", methods={"GET"}, name="challengeName")
     * @param Request $request
     * @param ParameterBagInterface $parameterBag
     * @return Response
     */
    public function challengeName(Request $request, ParameterBagInterface $parameterBag)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if ($request->getMethod() == 'GET' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')) {
            $entities = $this->getDoctrine()->getRepository(Api::class)->challengeName();
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
     * @Route("/area", methods={"GET"}, name="area")
     * @param Request $request
     * @param ParameterBagInterface $parameterBag
     * @return Response
     */
    public function area(Request $request, ParameterBagInterface $parameterBag)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if ($request->getMethod() == 'GET' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')) {
            $entities = $this->getDoctrine()->getRepository(Api::class)->area();
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
     * @param Request $request
     * @param ParameterBagInterface $parameterBag
     * @return JsonResponse
     * @Route("/fcr-different-feed-company-for-broiler", name="fcr_different_feed_company_for_broiler")
     */
    public function fcrDifferentFeedCompanyForBroiler(Request $request, ParameterBagInterface $parameterBag)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if ($request->getMethod() == 'GET' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')) {
            $fcrCompanies = $this->getDoctrine()->getRepository(Api::class)->fcrDifferentFeedCompanyForBroiler();

            $data = [];
            foreach ($fcrCompanies as $company) {
                $data[] = [
                    'id' => $company['id'],
                    'name' => $company['name'],
                ];
            }
            return new JsonResponse($data);
        }
        return new JsonResponse([
            'status' => 404,
            'message' => 'Not Found!'
        ]);

    }

    /**
     * @param Request $request
     * @param ParameterBagInterface $parameterBag
     * @return JsonResponse
     * @Route("/fcr-different-feed-company-for-sonali", name="fcr_different_feed_company_for_sonali")
     */
    public function fcrDifferentFeedCompanyForSonali(Request $request, ParameterBagInterface $parameterBag)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if ($request->getMethod() == 'GET' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')) {
            $fcrCompanies = $this->getDoctrine()->getRepository(Api::class)->fcrDifferentFeedCompanyForSonali();

            $data = [];
            foreach ($fcrCompanies as $company) {
                $data[] = [
                    'id' => $company['id'],
                    'name' => $company['name'],
                ];
            }
            return new JsonResponse($data);
        }
        return new JsonResponse([
            'status' => 404,
            'message' => 'Not Found!'
        ]);

    }



    /**
     * @Route("/fish-life-cycle-culture-in-progress", name="fish_life_cycle_culture_in_progress")
     * @param Request $request
     * @param ParameterBagInterface $parameterBag
     * @return JsonResponse|Response
     */
    public function fishLifeCycleCultureInProgress(Request $request, ParameterBagInterface $parameterBag)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if ($request->getMethod() == 'POST' && $request->headers->get('X-API-KEY') == $parameterBag->get('crm_api_key')) {
            $parameters = $request->request->all();
            $arrayData=[];
            if(isset($parameters['employee_id'])&&$parameters['employee_id']!=""){
                $employee = $this->getDoctrine()->getRepository(User::class)->find($parameters['employee_id']);

                $entities = $this->getDoctrine()->getRepository(FishLifeCycleCulture::class)->findBy(['employee'=>$employee, 'status'=>'IN_PROGRESS'],['id'=>'ASC']);

                if($entities){
                    /** @var FishLifeCycleCulture  $entity */
                    foreach ($entities as $entity) {
                        $detailData=[];
                        if($entity->getFishLifeCycleCultureDetails()){
                            /* @var FishLifeCycleCultureDetails $fishLifeCycleCultureDetail*/
                            foreach ($entity->getFishLifeCycleCultureDetails() as $fishLifeCycleCultureDetail) {
                                $detailData[]=[
                                    "id"=>$fishLifeCycleCultureDetail->getAppId()?$fishLifeCycleCultureDetail->getAppId():"",
                                    "sampling_date"=>$fishLifeCycleCultureDetail->getSamplingDate()->format('Y-m-d'),
                                    "avg_present_weight_gm"=>(string)$fishLifeCycleCultureDetail->getAveragePresentWeight(),
                                    "cur_survival_rate"=>(string)$fishLifeCycleCultureDetail->getSrPercentage(),
                                    "cur_feed_consumption"=>(string)$fishLifeCycleCultureDetail->getCurrentFeedConsumptionKg(),
                                    "farmer_comment"=>(string)$fishLifeCycleCultureDetail->getFarmerRemarks()?$fishLifeCycleCultureDetail->getFarmerRemarks():"",
                                    "visitor_comment"=>(string)$fishLifeCycleCultureDetail->getEmployeeRemarks()?$fishLifeCycleCultureDetail->getEmployeeRemarks():"",
                                    "pond_number"=>$entity->getPondNumber()?(string)$entity->getPondNumber():(string)1,
                                    "cur_fcr"=>(string)$fishLifeCycleCultureDetail->getCurrentFcr(),
                                    "web_life_cycle_details_id"=>(string)$fishLifeCycleCultureDetail->getId(),
                                    "created_at"=>$fishLifeCycleCultureDetail->getCreatedAt()->format('Y-m-d H:i:s')
                                ];
                            }
                        }
                        $customerGroup=$entity->getCustomer()->getFarmerIntroduce() && $entity->getCustomer()->getFarmerIntroduce()->getFarmerType()?'('.$entity->getCustomer()->getFarmerIntroduce()->getFarmerType()->getName().')':null;
                        $arrayData[]=[
                            "id"=> $entity->getAppId(),
                            "report_id"=> (string)$entity->getAppReportId(),
                            "report_type_id"=> $entity->getReport()?(string)$entity->getReport()->getId():null,
                            "agent_id"=> $entity->getAgent()?(string)$entity->getAgent()->getId():null,
                            "agent_name"=> $entity->getAgent()?$entity->getAgent()->getName():null,
                            "customer_id"=> $entity->getCustomer()?(string)$entity->getCustomer()->getId():null,
                            "customer_name"=> $entity->getCustomer()?$entity->getCustomer()->getName().' '.$customerGroup:null,
                            "pond_number"=> (string)$entity->getPondNumber(),
                            "reporting_date"=> $entity->getCreatedAt()->format('Y-m-d H:i:s'),
                            "address"=> $entity->getAgent() && $entity->getAgent()->getUpozila()?$entity->getAgent()->getUpozila()->getName():null,
                            "feed_company_id"=> $entity->getFeed()?(string)$entity->getFeed()->getId():null,
                            "feed_company_name"=> $entity->getFeed()?(string)$entity->getFeed()->getName():null,
                            "hatchery_id"=> $entity->getHatchery()?(string)$entity->getHatchery()->getId():null,
                            "hatchery_name"=> $entity->getHatchery()?$entity->getHatchery()->getName():null,
                            "feed_item_name"=> $entity->getFeedItemName(),
                            "culture_species_main_id"=> $entity->getMainCultureSpecies()?(string)$entity->getMainCultureSpecies()->getId():null,
                            "culture_species_main_name"=> $entity->getMainCultureSpecies()?(string)$entity->getMainCultureSpecies()->getName():null,
                            "culture_species_optional_id"=> $entity->getOtherCultureSpecies()?(string)$entity->getOtherCultureSpecies()->getId():null,
                            "culture_species_optional_name"=> $entity->getOtherCultureSpecies()?$entity->getOtherCultureSpecies()->getName():null,
                            "feed_type_id"=> $entity->getFeedType()?(string)$entity->getFeedType()->getId():null,
                            "feed_type_name"=> $entity->getFeedType()?$entity->getFeedType()->getName():"",
                            "stocking_date"=> $entity->getStockingDate()->format('Y-m-d'),
                            "culture_area_decimal"=> (string)$entity->getCultureAreaDecimal(),
                            "no_of_initial_fish_pcs"=> (string)$entity->getNoOfInitialFish(),
                            "density_per_decimal_pcs"=> (string)$entity->getStockingDensity(),
                            "avg_initial_weight_gm"=> (string)$entity->getAverageInitialWeightGm(),
                            "total_initial_weight_kg"=> (string)$entity->getTotalInitialWeightKg(),
                            "culture_species_desc"=> (string)$entity->getSpeciesDescription(),
                            "life_cycle_details"=>sizeof($detailData)>0?json_encode($detailData):"",
                            "status"=> $entity->getStatus(),
                            "feed_item_name_other"=> $entity->getFeedItemNameOther(),
                            "web_life_cycle_id"=> (string)$entity->getId(),
                            "employee_id"=> $entity->getEmployee()?(string)$entity->getEmployee()->getId():null
                        ];
                    }
                }
            }

            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($arrayData));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }
        return new JsonResponse([
            'status' => 404,
            'message' => 'Not Found!'
        ]);


    }
}