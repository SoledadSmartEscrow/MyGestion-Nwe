<?php

namespace App\Controller;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class InvoicesIssuedController extends AbstractController
{
    #[Route('/invoices/issued', name: 'app_invoices_issued')]

    public function getInvoicesIssued(): Response
    {
        $user = 'SOLEDADROMAR@outlook.es'; // Usuario
        $password = 'mygestion'; // Contraseña
        $recurso = 'ApiDetallesFacturasCliente'; //Para obtener todos los factura_id
        $recurso2 = 'ApiFacturasCliente'; //Para obtener el detalle de cada factura segun su factura_id

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

        $facturaIds = [];
        foreach ($xmlResponse->detalle as $detalle) {
            $facturaIds[] = (int)$detalle->factura_id;
        }
        $facturaDetails = [];

        // Realizo la solicitud de los detalles por cada factura_id
        foreach ($facturaIds as $facturaId) {
            $url2 = 'https://app05.mygestion.com/appMg/api/' . $recurso2 . '/' . $facturaId . '?' . http_build_query(['user' => $user, 'password' => $password]);
            $response2 = $client->request('GET', $url2, $options);
            $facturaDetails[] = $response2->getBody()->getContents();
        }

        // Procesa los detalles de las facturas, transforma a JSON, y devuelve la respuesta
        $InvoicesIssued = [];
        $processedFacturaIds = [];

        foreach ($facturaDetails as $facturaDetail) {
            $xml = simplexml_load_string($facturaDetail);
            $facturaId = (string)$xml->factura->id;

            // Verifica si ya hemos procesado este factura_id porque trae tantos factura_id como cantidad de productos que esten en la factura
            if (!in_array($facturaId, $processedFacturaIds)) {
                $invoice = [
                    "factura" => [
                        "id" => (string)$xml->factura->id,
                        "Ejercicio" => (string)$xml->factura->asiento_ejercicio,
                        "Serie" => (string)$xml->factura->serie,
                        "Tipo" => (string)$xml->factura->tipo_factura,
                        "Numero de Factura" => (string)$xml->factura->factura,
                        "Fecha de Emision" => (string)$xml->factura->fecha_factura,
                        "Fecha de Vencimiento" => (string)$xml->factura->fecha_vto,
                        "Razon Social Cliente" => (string)$xml->factura->nombre_cliente,
                        "Forma de Pago" => (string)$xml->factura->forma_pago,
                        "Pagada" => (string)$xml->factura->pagada,
                        "Fecha de Pago" => (string)$xml->factura->fecha_pago,
                        "Moneda" => (string)$xml->factura->divisa,
                        "Total antes de los descuentos" => (string)$xml->factura->importe_detalles,
                        "% Descuento Pronto Pago" => (string)$xml->factura->porcen_dto_pp,
                        "% Descuento Especial" => (string)$xml->factura->porcen_dto_especial,
                        "Recargo Equivalente" => (string)$xml->factura->recargo_equiv,
                        "% Recargo Financiero" => (string)$xml->factura->porcen_rec_financiero,
                        "Importe Total Recargo Financiero" => (string)$xml->factura->rec_financiero,
                        "Importe Entrega a cuenta" => (string)$xml->factura->entrega_a_cuenta,
                        "% IRPF" => (string)$xml->factura->porcen_irpf,
                        "Importe Total IRPF" => (string)$xml->factura->irpf,
                        "Base Imponible" => (string)$xml->factura->base_imponible,
                        "Total IVA" => (string)$xml->factura->iva,
                        "Importe Total Factura" => (string)$xml->factura->total_factura,

                    ]
                ];
                $InvoicesIssued[] = $invoice;
                $processedFacturaIds[] = $facturaId;
            }
        }
        $responseData = ["Facturas Emitidas" => $InvoicesIssued];

        return new JsonResponse($responseData);
    }
}
