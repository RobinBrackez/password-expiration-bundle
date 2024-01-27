<?php

namespace RobinBrackez\PasswordExpirationBundle\EventListener;

use RobinBrackez\PasswordExpirationBundle\Service\EasyAdminWrapper;
use RobinBrackez\PasswordExpirationBundle\User\PasswordExpirableInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class PasswordExpirationListener
{
    public function __construct(
        private readonly Security         $security,
        private readonly RequestStack     $requestStack,
        private readonly EasyAdminWrapper $easyAdminWrapper,
        private readonly RouterInterface           $router,
        private readonly string           $changePasswordRouteName,
        private readonly int              $passwordMaxDaysOld = 30,
    ) {
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        $user = $this->security->getUser();

        if (!$user) {
            return; // user is not logged in
        }

        if ($this->changePasswordIsCurrentRoute($request)) {
            return; // don't do anything when the user is on the change password page, to avoid endless redirect loop
        }

        if ($this->easyAdminWrapper->isEasyAdminBundleAvailable()
            && $this->easyAdminWrapper->changePasswordIsCurrentRoute($request->getQueryString())
        ) {
            return; // don't do anything when the user is on the change password page, to avoid endless redirect loop
        }

        if (!$request->isMethod('GET')) {
            return; // don't do anything when the user is not doing a GET request, otherwise you might block a POST request if the time would expire at that moment
        }

        if (!$user instanceof PasswordExpirableInterface) {
            throw new \LogicException(sprintf(
                'The User class %s needs to implement %s in order to use PasswordExpirationListener. If you don\'t want this behavior, remove the PasswordExpirationListener bundle',
                get_class($user),
                PasswordExpirableInterface::class
            ));
        }

        if (!$user->changedPasswordLongerAgoThan($this->passwordMaxDaysOld)) {
            return; // password is not expired yet
        }

        $session = $this->requestStack->getSession();
        $session->getFlashBag()->add('warning', sprintf("Your password is older then %d days. Please change it now.", $this->passwordMaxDaysOld));

        if ($this->easyAdminWrapper->isEasyAdminBundleAvailable()) {
            $url = $this->easyAdminWrapper->generateUrl();
        } else {
            $url = $this->router->generate($this->changePasswordRouteName);
        }
        $response = new RedirectResponse($url);
        $event->setResponse($response);
    }

    private function changePasswordIsCurrentRoute(Request $request): bool
    {
        return $request->attributes->get('_route') === $this->changePasswordRouteName;
    }
}
