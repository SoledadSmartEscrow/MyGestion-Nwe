<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InvoicesReceivedController extends AbstractController
{
    #[Route('/invoices/received', name: 'app_invoices_received')]
    public function index(): Response
    {
        return $this->render('invoices_received/index.html.twig', [
            'controller_name' => 'InvoicesReceivedController',
        ]);
    }
}
