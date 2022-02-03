<?php


namespace Terminalbd\CrmBundle\Controller;


use App\Entity\Admin\Location;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Terminalbd\CrmBundle\Entity\PoultryMeatEggPrice;
use Terminalbd\CrmBundle\Entity\Setting;

/**
 * Class MeatAndEggPriceController
 * @package Terminalbd\CrmBundle\Controller
 * @Security("is_granted('ROLE_CRM_SALES_MARKETING_USER') or is_granted('ROLE_DEVELOPER')")
 * @Route("/meat-egg-price", name="meat_egg_price")
 */
class MeatAndEggPriceController extends AbstractController
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/", name="_new")
     */
    public function new()
    {
        $regions = $this->getDoctrine()->getRepository(Location::class)->findBy(['level' => 3]);
        $breedTypes = $this->getDoctrine()->getRepository(Setting::class)->findBy(['settingType' => 'MEAT_EGG_TYPE']);
        $price = $this->getDoctrine()->getRepository(PoultryMeatEggPrice::class)->processPrice($regions,$breedTypes, $this->getUser());

        return $this->render('@TerminalbdCrm/meatAndEggPrice/new.html.twig',[
            'regions' => $regions,
            'breedTypes' => $breedTypes,
            'price' => $price,
            'employee' => $this->getUser(),
        ]);
    }


    /**
     * @Route("/{id}/update", name="_update", options={"expose"=true})
     * @param Request $request
     * @return JsonResponse
     */
    public function updateMeatAndEggPrice(Request $request)
    {
        $data = $request->request->all();

        $entity = $this->getDoctrine()->getRepository(PoultryMeatEggPrice::class)->findOneBy(['employee' => $this->getUser(),'region' => $data['regionId'], 'breedType' => $data['breedTypeId'], 'reportingDate' => new \DateTime("now")]);

        if($entity){
            $em = $this->getDoctrine()->getManager();
            $entity->setPrice($data['price']);
            $em->persist($entity);
            $em->flush();
            return new JsonResponse('Success');
        }
        return new JsonResponse('Failed');

    }
}