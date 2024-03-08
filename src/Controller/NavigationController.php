<?php

    namespace App\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Session\SessionInterface;
    use Symfony\Component\Routing\Annotation\Route;

    class NavigationController extends AbstractController
    {
        /**
         * @Route("/navigate-back", name="navigate_back")
         */
        #[Route('/', name: 'app_navigate_back')]
        public function navigateBack(Request $request, SessionInterface $session)
        {
            // Retrieve the previous page URL from the session
            $previousPage = $session->get('previous_page');
            dump($previousPage);

            // Redirect the user back to the previous page
            return $this->redirect($previousPage);
        }
    }