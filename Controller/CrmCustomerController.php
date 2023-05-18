<?php
/**
 * Created by PhpStorm.
 * User: sayem
 * Date: 9/8/20
 * Time: 4:13 PM
 */
namespace Terminalbd\CrmBundle\Controller;

use App\Entity\Admin\Location;
use App\Entity\Core\Agent;
use Doctrine\ORM\QueryBuilder;
use http\Url;
use Knp\Component\Pager\PaginatorInterface;
use Omines\DataTablesBundle\Adapter\ArrayAdapter;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\Column\TwigStringColumn;
use Omines\DataTablesBundle\DataTableFactory;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\CrmVisitDetails;
use Terminalbd\CrmBundle\Entity\NewFarmerIntroduce\FarmerIntroduceDetails;
use Terminalbd\CrmBundle\Entity\NewFarmerTouch\FarmerTouchReport;
use Terminalbd\CrmBundle\Entity\Setting;
use Terminalbd\CrmBundle\Form\CrmCustomerFormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

use Terminalbd\CrmBundle\Repository\CrmCustomerRepository;

/**
 * @Route("/crm/customer")
 * @Security("is_granted('ROLE_CRM_POULTRY_USER') or is_granted('ROLE_CRM_CATTLE_USER') or is_granted('ROLE_CRM_AQUA_USER') or is_granted('ROLE_CRM_SALES_MARKETING_USER') or is_granted('ROLE_CRM_POULTRY_ADMIN') or is_granted('ROLE_CRM_CATTLE_ADMIN') or is_granted('ROLE_CRM_AQUA_ADMIN') or is_granted('ROLE_CRM_SALES_MARKETING_ADMIN') or is_granted('ROLE_DEVELOPER')")
 */

class CrmCustomerController extends AbstractController
{
    /**
     * @Route("/{customerType}/{mode}", defaults={"customerType" = "poultry", "mode" = null}, methods={"GET","POST"}, name="crm_customer")
     */
    public function index($customerType, $mode, Request $request, PaginatorInterface $paginator)
    {

        $entities=$this->getDoctrine()->getRepository(CrmCustomer::class)->getCustomerByLocationAndType($customerType, $this->getUser(), 'farmer');

        $data = $paginator->paginate(
            $entities,
            $request->query->get('page', 1)/*page number*/,
            50  /*limit per page*/
        );

        $species=[];

        $breedName= $this->getDoctrine()->getRepository(Setting::class)->findOneBy(['settingType'=>'BREED_NAME','slug'=>$customerType.'-breed','status'=>1]);
        if($breedName){
            $species = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('status'=>1,'settingType'=>'SPECIES_TYPE','parent'=>$breedName));
        }

        if ($mode == 'excel'){
            return $this->render('@TerminalbdCrm/crmcustomer/customer-excel.html.twig',[
                'entities' => $entities,
                'customerType'=>$customerType,
                'species'=>$species,
//                'form' => $form->createView()
            ]);
        }


        return $this->render('@TerminalbdCrm/crmcustomer/index.html.twig',[
            'entities' => $data,
            'customerType'=>$customerType,
            'species'=>$species,
//            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/new", methods={"GET", "POST"}, name="crm_customer_new")
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {

        $entity = new CrmCustomer();

        $agentRepo = $this->getDoctrine()->getRepository(Agent::class);
        $form = $this->createForm(CrmCustomerFormType::class, $entity,array('user' => $this->getUser(),'agentRepo' => $agentRepo))
            ->add('SaveAndCreate', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->addFlash('success', 'post.created_successfully');
            if ($form->get('SaveAndCreate')->isClicked()) {
                return $this->redirectToRoute('crm_customer_new');
            }
            return $this->redirectToRoute('crm_customer');
        }
        return $this->render('@TerminalbdCrm/crmcustomer/new.html.twig', [
            'entity' => $entity,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing Post entity.
     *
     * @Route("/{id}/edit", methods={"GET", "POST"}, name="crm_customer_edit")
     * @Security("is_granted('ROLE_CRM_POULTRY_ADMIN') or is_granted('ROLE_CRM_CATTLE_ADMIN') or is_granted('ROLE_CRM_AQUA_ADMIN') or is_granted('ROLE_CRM_SALES_MARKETING_ADMIN') or is_granted('ROLE_DEVELOPER')")

     * @param Request $request
     * @param CrmCustomer $entity
     * @return Response
     */

    public function edit(Request $request, CrmCustomer $entity): Response
    {
        $agentRepo = $this->getDoctrine()->getRepository(Agent::class);
        $form = $this->createForm(CrmCustomerFormType::class, $entity,array('user' => $this->getUser(),'agentRepo' => $agentRepo))
            ->add('SaveAndCreate', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $this->getDoctrine()->getManager()->flush();
            $farmerIntroduce = $entity->getFarmerIntroduce();
            if($farmerIntroduce){
                $farmerIntroduce->setAgent($entity->getAgent());
                $this->getDoctrine()->getManager()->persist($farmerIntroduce);
            }
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'post.updated_successfully');
            if ($form->get('SaveAndCreate')->isClicked()) {
                return $this->redirectToRoute('crm_customer_edit', ['id' => $entity->getId()]);
            }
            return $this->redirectToRoute('crm_customer');
        }
        return $this->render('@TerminalbdCrm/crmcustomer/new.html.twig', [
            'entity' => $entity,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @param Request $request
     * @Route("/{id}/farmer-create/ajax" ,name="new_farmer_ajax", methods={"POST"}, options={"expose"=true})
     * @return JsonResponse
     */
    public function createFarmer(Request $request,$id){


        $allRequestData = $request->request->all();
        $group = $this->getDoctrine()->getRepository(Setting::class)->findOneBy(array('slug'=>'farmer'));
        $feed = $this->getDoctrine()->getRepository(Setting::class)->find($allRequestData['feed_id']);
        $location = $this->getDoctrine()->getRepository(Location::class)->find($allRequestData['location']);
        $otherAgent = $this->getDoctrine()->getRepository(Agent::class)->find($allRequestData['other_agent']);
        $subAgent = $this->getDoctrine()->getRepository(Agent::class)->find($allRequestData['sub_agent']);

        $existingFarmerCheck= $this->getDoctrine()->getRepository(CrmCustomer::class)->duplicateCustomerCheckByMobileAndType($allRequestData['mobile'], $allRequestData['farmer_type']);

        if($existingFarmerCheck){
            return new JsonResponse(['status'=>'409','message'=>'This Farmar Already Exist.']);
        }

        $entity=new CrmCustomer();
        if($allRequestData['agent']==''){
            if($allRequestData['sub_agent']!=''){
                $entity->setAgent($subAgent);
            }elseif ($allRequestData['other_agent']!=''){
                $entity->setAgent($otherAgent);
            }
        }else{
            $agent = $this->getDoctrine()->getRepository(Agent::class)->find($allRequestData['agent']);

            $entity->setAgent($agent);
        }

        $entity->setName($allRequestData['name']);
        $entity->setAddress($allRequestData['address']);
        $entity->setMobile($allRequestData['mobile']);
        $entity->setCustomerGroup($group);
        $entity->setLocation($location);

        if($otherAgent){
            $entity->setOtherAgent($otherAgent);
        }
        $em=$this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();
        $returnData= array(
            'id'=>$entity->getId(),
            'name'=>$entity->getName(),
            'subAgent'=>$allRequestData['sub_agent'],            
            'status'=>'200',
            'message'=>'Farmer has been successfully added.'
        );
//        $this->getDoctrine()->getRepository(CrmVisitDetails::class)->insertCrmVisitDetailForFarmer($entity, $id, $allRequestData);
//        $this->getDoctrine()->getRepository(FarmerTouchReport::class)->insertFarmerTouch($entity, $this->getUser(), $feed, $allRequestData);
        $this->getDoctrine()->getRepository(FarmerIntroduceDetails::class)->insertCrmFarmerIntroduceDetails($entity, $this->getUser(), $feed, $allRequestData);
        return new JsonResponse($returnData);

    }

    /**
     * @param Request $request
     * @Route("/{id}/other-create/ajax" ,name="new_other_agent_ajax", methods={"POST"}, options={"expose"=true})
     * @return JsonResponse
     */
    public function createOtherAgent(Request $request,$id){

        $entity=new Agent();
        $allRequestData = $request->request->all();
        $group = $this->getDoctrine()->getRepository(\App\Entity\Core\Setting::class)->findOneBy(array('slug'=>'other-agent'));
        $location = $this->getDoctrine()->getRepository(Location::class)->find($allRequestData['location']);
        if(isset($allRequestData['feedCompany'])&&$allRequestData['feedCompany']!=''){
            $feedCompany = $this->getDoctrine()->getRepository(Setting::class)->find($allRequestData['feedCompany']);
            $entity->setOtherAgentFeedCompany($feedCompany);
        }
        $entity->setName($allRequestData['name']);
        $entity->setAddress($allRequestData['address']);
        $entity->setMobile($allRequestData['mobile']);
        $entity->setAgentGroup($group);
        $entity->setUpozila($location);
        $entity->setDistrict($location->getParent());
        $entity->setCreated(new \DateTime());
        $em=$this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();
        $entity->setAgentId($entity->getId());
        $em->persist($entity);
        $em->flush();
        $returnData= array(
            'status'=>200,
          'id'=>$entity->getId(),
          'name'=>$entity->getName(),
        );
        $this->getDoctrine()->getRepository(CrmVisitDetails::class)->insertOtherAgent($entity,$id,$allRequestData);
        return new JsonResponse(array($returnData));

    }

    /**
     * @param Request $request
     * @Route("/{id}/sub-create/ajax" ,name="new_sub_agent_ajax", methods={"POST"}, options={"expose"=true})
     * @return JsonResponse
     */
    public function createSubAgent(Request $request,$id)
    {

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
        $entity->setCreated(new \DateTime('now'));
        if($allRequestData['agent']){
            $agent = $this->getDoctrine()->getRepository(Agent::class)->find($allRequestData['agent']);
            $entity->setParent($agent);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();

        $returnData= array(
            'status'=>200,
            'id' => $entity->getId(),
            'name' => $entity->getName(),
        );
        if ($allRequestData['purpose']){
            $this->getDoctrine()->getRepository(CrmVisitDetails::class)->insertSubAgent($entity, $id, $allRequestData);

        }
        return new JsonResponse(array($returnData));

    }

    /**
     * Deletes a CrmCustomer entity.
     * @Route("/{id}/delete", methods={"GET"}, name="customer_delete", options={"expose"=true})
     * @Security("is_granted('ROLE_CRM_POULTRY_ADMIN') or is_granted('ROLE_CRM_CATTLE_ADMIN') or is_granted('ROLE_CRM_AQUA_ADMIN') or is_granted('ROLE_CRM_SALES_MARKETING_ADMIN') or is_granted('ROLE_DEVELOPER')")
     * @param $id
     * @return Response
     */
    public function delete($id): Response
    {
        $customer = $this->getDoctrine()->getRepository(CrmCustomer::class)->find($id);
        $visit = $this->getDoctrine()->getRepository(CrmVisitDetails::class)->findOneBy(['crmCustomer' => $customer]);
        if ($visit){
            return new JsonResponse(['status'=>501,'message'=>'Failed']);
        }

        if($customer){
            $customer->setMobile(null);
            $customer->setDeletedBy($this->getUser());
            $customer->setDeletedAt(new \DateTime('now'));
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($customer);
        $em->flush();
        return new JsonResponse(['status'=>200,'message'=>'Success']);
    }


    /**
     * @param CrmCustomer $crmCustomer
     * @Route("/{id}/ajax" ,name="get_farmer_ajax", methods={"GET"}, options={"expose"=true})
     * @return JsonResponse
     */
    public function getCustomerById(CrmCustomer $crmCustomer){

        $returnData= array(
            'id'=>$crmCustomer->getId(),
            'name'=>$crmCustomer->getName(),
            'address'=>$crmCustomer->getAddress(),
            'phone'=>$crmCustomer->getMobile(),
        );
        return new JsonResponse(array($returnData));

    }

    /**
     * Deletes a Setting entity.
     * @param Agent $agent
     * @Route("/{id}/find/ajax", methods={"GET"}, name="get_core_agent_find_ajax", options={"expose"=true})
     * @return Response
     */
    public function getAgentByIdUsingAjax(Agent $agent): Response
    {
        $returnData= array(
            'id'=>$agent->getId(),
            'name'=>$agent->getName(),
            'address'=>$agent->getAddress(),
            'mobile'=>$agent->getMobile(),
            'agentId'=>$agent->getAgentId(),
        );
        return new JsonResponse(array($returnData));

    }


    /**
     * Displays a form to edit an existing CrmVisit entity.
     * @Route("species/name/by/parent/{id}", methods={"GET", "POST"}, name="species_name_by_parent_id_ajax", options={"expose"=true})
     * @param $id
     * @return Response
     */

    public function farmerIntroduceDetails($id): Response
    {
        $farmerType =$this->getDoctrine()->getRepository(Setting::class)->find($id);
        $speciesTypes =$this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType'=>'SPECIES_TYPE','status'=>1, 'parent'=>$id));

        return $this->render('@TerminalbdCrm/crmcustomer/farmer-introduce-details.html.twig', [
            'speciesTypes' => $speciesTypes,
            'farmerType' => $farmerType,
        ]);
    }

    /**
     * Displays a form to edit an existing CrmVisit entity.
     * @Route("/agent/change/chick/to/feed", methods={"GET"}, name="farmer_aget_change_chick_to_feed")
     * @Security("is_granted('ROLE_DEVELOPER')")
     * @return Response
     */

    public function farmerAgentChangeChickToFeedAgent( ParameterBagInterface $parameterBag): Response
    {

        $em=$this->getDoctrine()->getManager();
        $customers = $this->getDoctrine()->getRepository(CrmCustomer::class)->getCustomerByChickAgent();
    $returnArray=[];
        foreach ($customers as $customer) {

            $agentGroup = $this->getDoctrine()->getRepository(\App\Entity\Core\Setting::class)->findOneBy(['slug'=>'feed']);

            $findFeedAgentByChickAgent = $this->getDoctrine()->getRepository(Agent::class)->findOneBy(['upozila'=>$customer['customerUpozilaId'], 'name'=>$customer['agentName'], 'agentGroup'=>$agentGroup, 'status'=>1]);

//            $findFeedAgentByChickAgent = $this->getDoctrine()->getRepository(Agent::class)->findOneBy(['upozila'=>$customer['customerUpozilaId'], 'mobile'=>$customer['mobile'], 'agentGroup'=>$agentGroup, 'status'=>1]);

            if($findFeedAgentByChickAgent){
                $farmer = $this->getDoctrine()->getRepository(CrmCustomer::class)->find($customer['id']);

                $farmer->setAgent($findFeedAgentByChickAgent);

                $em->persist($farmer);


                $farmerIntroduce = $this->getDoctrine()->getRepository(FarmerIntroduceDetails::class)->findOneBy(['customer'=>$farmer]);

                $farmerIntroduce->setAgent($findFeedAgentByChickAgent);

                $em->persist($farmerIntroduce);
                $em->flush();


                $returnArray['changes'][]=['id'=>$findFeedAgentByChickAgent->getId(), 'agentCode'=>$findFeedAgentByChickAgent->getAgentId(), 'name'=>$findFeedAgentByChickAgent->getName()];
            }else{
                $returnArray['notChanges'][]=['customer_id'=>$customer['id'], 'customerName'=>$customer['name'], 'agentAutoId'=>$customer['agent_id'], 'agentCode'=>$customer['agentCode'], 'agentName'=>$customer['agentName']];
            }
        }

        if($returnArray && isset($returnArray['notChanges']) && sizeof($returnArray['notChanges'])>0){
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            foreach ($returnArray['notChanges'] as $key => $data) {
                if ($key === array_key_first($returnArray['notChanges'])){ //header
                    $sheet->setCellValue("A1", "Customer ID");
                    $sheet->setCellValue("B1", "Customer Name");
                    $sheet->setCellValue("C1", "Agent Auto Id");
                    $sheet->setCellValue("D1", "Agent Name");
                    $sheet->setCellValue("E1", "Agent Code");
                }

                $cellCoordinate = $key + 2;

                $sheet->setCellValue("A" . $cellCoordinate, $data['customer_id']);
                $sheet->setCellValue("B" . $cellCoordinate, $data['customerName']);
                $sheet->setCellValue("C" . $cellCoordinate, $data['agentAutoId']);
                $sheet->setCellValue("D" . $cellCoordinate, $data['agentName']);
                $sheet->setCellValue("E" . $cellCoordinate, $data['agentCode']);

            }

            // Create xlsx file
            $filePath = $parameterBag->get('projectRoot') . '/public/uploads/farmer_agent_update_problem_'. date('d-m-Y_H-s-i') .'_.xlsx';
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->setIncludeCharts(true);
            $writer->save($filePath);

            return $this->file($filePath)->deleteFileAfterSend();
        }
        
    }



}