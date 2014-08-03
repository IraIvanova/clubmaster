<?php

namespace Club\UserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/{_locale}/location")
 */
class LocationController extends Controller
{
    /**
     * @Template()
     * @Route("")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $locations = $em->getRepository('ClubUserBundle:Location')->getRoots();

        return array(
            'locations' => $locations
        );
    }

    /**
     * @Route("/{id}")
     */
    public function switchAction(\Club\UserBundle\Entity\Location $location)
    {
        $user = $this->getUser();

        $this->get('club_user.location')->setCurrent($location);

        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $user->setLocation($location);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
        }

        $url = ($this->get('session')->get('switch_location'))
            ? $this->get('session')->get('switch_location')
            : $this->generateUrl('localized', array( '_locale' => $this->getRequest()->getLocale() ));

        $this->get('session')->set('switch_location', null);

        return $this->redirect($url);
    }
}
