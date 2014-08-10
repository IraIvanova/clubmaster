<?php

namespace Club\FeedbackBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/feedback")
 */
class FeedbackController extends Controller
{
    /**
     * @Route("")
     * @Template()
     */
    public function indexAction()
    {
        $type = array(
            'idea' => $this->get('translator')->trans('Idea'),
            'bug' => $this->get('translator')->trans('Bug'),
            'other' => $this->get('translator')->trans('Other')
        );

        $feedback = new \Club\FeedbackBundle\Model\Feedback();
        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $user = $this->getUser();
            $feedback->name = $user->getProfile()->getName();

            if ($user->getProfile()->getProfileEmail())
                $feedback->email = $user->getProfile()->getProfileEmail()->getEmailAddress();
        }

        $attr = array(
            'class' => 'form-control'
        );
        $label_attr = array(
            'class' => 'col-sm-2'
        );

        $form = $this->createFormBuilder($feedback)
            ->add('name','text',array(
                'attr' => $attr,
                'label_attr' => $label_attr,
                'required' => false
            ))
            ->add('email','email',array(
                'attr' => $attr,
                'label_attr' => $label_attr,
                'required' => false
            ))
            ->add('type','choice',array(
                'attr' => $attr,
                'label_attr' => $label_attr,
                'choices' => $type
            ))
            ->add('subject','text',array(
                'attr' => $attr,
                'label_attr' => $label_attr,
                'required' => true
            ))
            ->add('message','textarea',array(
                'attr' => array(
                    'class' => $attr['class'],
                    'rows' => 10
                ),
                'label_attr' => $label_attr,
                'required' => true,
            ))
            ->getForm();

        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bind($this->getRequest());

            if ($form->isValid()) {
                $this->sendData($feedback);

                return $this->redirect($this->generateUrl('club_feedback_feedback_index'));
            }
        }

        return array(
            'form' => $form->createView()
        );
    }

    private function sendData(\Club\FeedbackBundle\Model\Feedback $feedback)
    {
        $host = 'loopback.clubmaster.dk';
        $fp = @fsockopen($host,80);

        $feedback->host = $this->generateUrl('homepage', array(), true);
        $str = '';
        foreach ($feedback as $key=>$value) {
            $str .= $key.'='.$value.'&';
        }

        if ($fp) {
            fputs($fp, "POST /feedback HTTP/1.1\r\n");
            fputs($fp, "HOST: loopback.clubmaster.dk\r\n");
            fputs($fp, "User-Agent: ClubMasterTool 1.0\r\n");
            fputs($fp, "Content-length: ".strlen($str)."\r\n");
            fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n\r\n");

            fputs($fp, $str."\r\n\r\n");

            $res = '';
            while (!feof($fp)) $res .= fgets($fp,4096);
            fclose($fp);

            if (strpos($res,'200 OK')) {
                $this->get('session')->getFlashBag()->add('notice',$this->get('translator')->trans('Your message has been sent.'));

                return;
            }
        }

        $this->get('session')->getFlashBag()->add('error',$this->get('translator')->trans('There was a problem delivering your message.'));
    }
}
