<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InvoicesIssuedController extends AbstractController
{
    #[Route('/invoices/issued', name: 'app_invoices_issued')]
    public function index(): Response
    {
        return $this->render('invoices_issued/index.html.twig', [
            'controller_name' => 'InvoicesIssuedController',
        ]);
    }
}
