<?php

namespace AppBundle\Controller;

use AppBundle\Entity\RotaSlotStaff;
use AppBundle\Util\RotaUtil;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/{rotaId}", name="homepage")
     */
    public function indexAction($rotaId, Request $request)
    {
        $rotaUtil = $this->container->get('app.util.rota');

        return $this->render('@App/default/index.html.twig', [
            'dayNumbers' => $rotaUtil->getDays($rotaId),
            'staffIds' => $rotaUtil->getActiveStaff($rotaId),
            'shiftList' => $rotaUtil->getShiftList($rotaId),
            'staffShiftsInDayNumber' => $rotaUtil->getStaffShiftsInDayNumber($rotaId),
        ]);
    }
}
