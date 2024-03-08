<?php

    namespace App\EventListener;

    use Symfony\Component\EventDispatcher\EventSubscriberInterface;
    use Symfony\Component\HttpKernel\Event\ControllerEvent;
    use Symfony\Component\HttpKernel\KernelEvents;

    class PreviousPageListener implements EventSubscriberInterface
    {
        public function onKernelController(ControllerEvent $event)
        {
            $request = $event->getRequest();
            $currentUrl = $request->getUri();
            $session = $request->getSession();
            $session->set('previous_page', $currentUrl);
        }

        public static function getSubscribedEvents()
        {
            return [
                KernelEvents::CONTROLLER => 'onKernelController',
            ];
        }
    }