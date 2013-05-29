<?php

namespace Pim\Bundle\ProductBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Gedmo\Translatable\TranslatableListener;
use Symfony\Component\HttpKernel\HttpKernel;

/**
 * @author    Gildas Quemener <gildas.quemener@gmail.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class LocaleListener implements EventSubscriberInterface
{
    protected $securityContext;
    protected $listener;

    public function __construct(SecurityContextInterface $securityContext, TranslatableListener $listener)
    {
        $this->securityContext = $securityContext;
        $this->listener        = $listener;
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => 'onKernelRequest'
        );
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernel::MASTER_REQUEST !== $event->getRequestType() || null === $user = $this->getUser()) {
            return;
        }

        $this->listener->setTranslatableLocale((string) $user->getValue('cataloglocale')->getData());
    }

    private function getUser()
    {
        if (null === $token = $this->securityContext->getToken()) {
            return null;
        }

        if (!is_object($user = $token->getUser())) {
            return null;
        }

        return $user;
    }
}

