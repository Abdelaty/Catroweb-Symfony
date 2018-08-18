<?php
namespace Catrobat\AppBundle\Controller\Resetting;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ResettingController2 extends Controller
{

    /**
     * @Route("/resetting/request", name="resetting_request")
     * @Method({"GET"})
     */
    public function requestAction()
    {
        return $this->render('@FOSUser/Resetting/request.html.twig');
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function sendEmailAction(Request $request) {
        /**
         * @var $tokenGenerator \FOS\UserBundle\Util\TokenGeneratorInterface
         * @var $user UserInterface
         */

        $username = $this->container->get('request')->request->get('username');
        $user = $this->container->get('fos_user.user_manager')->findUserByUsernameOrEmail($username);

        if (null === $user) {
            return $this->container->get('templating')
                ->renderResponse('FOSUserBundle:Resetting:request.html.' . $this->getEngine(), array(
                'invalid_username' => $username
            ));
        }

        if ($user->isPasswordRequestNonExpired($this->container->getParameter('fos_user.resetting.token_ttl'))) {
            return $this->container->get('templating')
                ->renderResponse('FOSUserBundle:Resetting:passwordAlreadyRequested.html.' . $this->getEngine());
        }

        if ($user->isLimited()) {
            throw new HttpException(403, 'This Account cannot reset the password');
        }

        if (null === $user->getConfirmationToken()) {
            $tokenGenerator = $this->container->get('fos_user.util.token_generator');
            $user->setConfirmationToken($tokenGenerator->generateToken());
        }

        $this->container->get('session')->set(static::SESSION_EMAIL, $this->getObfuscatedEmail($user));
        $this->container->get('fos_user.mailer')->sendResettingEmailMessage($user);
        $user->setPasswordRequestedAt(new \DateTime());
        $this->container->get('fos_user.user_manager')->updateUser($user);

        return new RedirectResponse($this->container->get('router')->generate('fos_user_resetting_check_email'));
    }
}