<?php

namespace App\Controller;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class InvoicesIssuedController extends AbstractController
{
    #[Route('/invoices/issued', name: 'app_invoices_issued')]
    
    public function getFacturas(): Response
    {
        $user = 'SOLEDADROMAR@outlook.es'; // Usuario
        $password = 'mygestion'; // Contraseña
        $recurso = 'ApiSeriesFacturasCliente'; // Recurso

        // Configura el cliente Guzzle
        $client = new Client();
        $jar = new CookieJar();

        $options = [
            'headers' => [],
            'cookies' => $jar,
            'verify' => false, // Desactiva la verificación SSL (ten precaución en producción)
        ];

        // Realiza la solicitud HTTP
        $url = 'https://app05.mygestion.com/appMg/api/' . $recurso . '?' . http_build_query(['user' => $user, 'password' => $password]);
        $response = $client->request('GET', $url, $options);
        $content = $response->getBody()->getContents();

        // Convierte el contenido XML a JSON
        $xml = simplexml_load_string($content);
        $json = json_encode($xml, JSON_PRETTY_PRINT);

        return $this->json(['response' => $json]);
    }
}
