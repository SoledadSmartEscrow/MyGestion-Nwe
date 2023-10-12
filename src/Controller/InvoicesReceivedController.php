<?php

namespace App\Controller;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class InvoicesReceivedController extends AbstractController
{
    #[Route('/invoices/received', name: 'app_invoices_received')]
    public function getInvoicesReceived(): Response
    {
        $user = 'SOLEDADROMAR@outlook.es'; // Usuario
        $password = 'mygestion'; // Contraseña
        $recurso = 'ApiDetallesFacturasProveedor'; // Para obtener todos los factura_id
        $recurso2 = 'ApiFacturasProveedor'; // Para obtener el detalle de cada factura según su factura_id

        // Configura el cliente Guzzle
        $client = new Client();
        $jar = new CookieJar();

        $options = [
            'headers' => [],
            'cookies' => $jar,
            'verify' => false, // Desactiva la verificación SSL (ten precaución en producción)
        ];

        // Realiza la solicitud HTTP para obtener todos los factura_id
        $url = 'https://app05.mygestion.com/appMg/api/' . $recurso . '?' . http_build_query(['user' => $user, 'password' => $password]);
        $response = $client->request('GET', $url, $options);
        $invoices = $response->getBody()->getContents();
        $xmlResponse = simplexml_load_string($invoices);

        $invoicesReceived = [];

        // Realizo una nueva llamada y le paso los ID
        foreach ($xmlResponse->detalle as $detalle) {
            $id = (string)$detalle->id;
            $url2 = 'https://app05.mygestion.com/appMg/api/' . $recurso . '/' . $id . '?' . http_build_query(['user' => $user, 'password' => $password]);
            $response2 = $client->request('GET', $url2, $options);
            $invoiceDetails = $response2->getBody()->getContents();
            $xmlResponse2 = simplexml_load_string($invoiceDetails);

            foreach ($xmlResponse2->detalle as $detalle2) {
                $id = (int)$detalle2->id;
                $invoice = [
                    "factura" => [
                        "id" => $id,
                        "factura Id" => (int)$detalle2->factura_id,
                        "Articulo" => (string)$detalle2->articulo,
                        "Descripcion" => (string)$detalle2->descripcion,
                        "Cantidad" => (int)$detalle2->cantidad,
                        "Coste Divisa" => number_format((float)$detalle2->coste_divisa, 2),
                        "Base Imponible" => number_format((float)$detalle2->base_imponible, 2),
                        "Tipo IVA %" => number_format((float)$detalle2->tipo_iva, 2),
                        "IVA" => number_format((float)$detalle2->iva, 2),
                        "Total Detalle" => number_format((float)$detalle2->total_detalle, 2),
                        "% Descuento" => number_format((float)$detalle2->porcen_dto, 2),
                        "% Descuento Lineal" => number_format((float)$detalle2->dto_lineal, 2),
                    ],
                ];
                $invoicesReceived[] = $invoice;
            }
        }
        $responseData = ["Facturas Recibidas" => $invoicesReceived];

        return new JsonResponse($responseData);
    
    }
}
