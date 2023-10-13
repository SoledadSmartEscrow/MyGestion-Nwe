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
        $recurso = 'ApiDetallesFacturasCliente'; // Para obtener todos los factura_id
        $recurso2 = 'ApiFacturasCliente'; // Para obtener el detalle de cada factura según su factura_id

        // Configura el cliente Guzzle
        $client = new Client();
        $jar = new CookieJar();

        $options = [
            'headers' => [],
            'cookies' => $jar,
            'verify' => false, // Desactiva la verificación SSL (ten precaución en producción)
        ];

        // Realiza la solicitud HTTP para obtener todos los ID
        $url = 'https://app05.mygestion.com/appMg/api/' . $recurso . '?' . http_build_query(['user' => $user, 'password' => $password]);
        $response = $client->request('GET', $url, $options);
        $invoices = $response->getBody()->getContents();
        $xmlResponse = simplexml_load_string($invoices);

        //Guardo los resultados en dos arreglos distintos
        $detalleResponse = [];
        $facturaResponse = [];


        foreach ($xmlResponse->detalle as $detalle) {
            $id = (string)$detalle->id;
            $facturaId = (string)$detalle->factura_id;

            // Realizo una nueva llamada y le paso los ID
            $url2 = 'https://app05.mygestion.com/appMg/api/' . $recurso . '/' . $id . '?' . http_build_query(['user' => $user, 'password' => $password]);
            $response2 = $client->request('GET', $url2, $options);
            $invoiceDetails = $response2->getBody()->getContents();
            $xmlResponse2 = simplexml_load_string($invoiceDetails);

            // Realizo una nueva llamada y le paso los factura_Id
            $url3 = 'https://app05.mygestion.com/appMg/api/' . $recurso2 . '/' . $facturaId . '?' . http_build_query(['user' => $user, 'password' => $password]);
            $response3 = $client->request('GET', $url3, $options);
            $invoiceDetails3 = $response3->getBody()->getContents();
            $xmlResponse3 = simplexml_load_string($invoiceDetails3);

            // Almacenar los datos en arreglos asociativos
            $detalleResponse[$facturaId] = (array)$xmlResponse2->detalle;
            $facturaResponse[$facturaId] = (array)$xmlResponse3->factura;
        }
        //Array para mostrar la informacion unificada
        $responseData = [];
        // Relaciono el factura_Id de $detalle con el Id de $factura para obtener los productos vendidos que estan en detalle con el resto de la informacion que esta en factura
        foreach ($detalleResponse as $facturaId => $detalleData) {
            $facturaData = $facturaResponse[$facturaId];

            $responseData[] = [
                "ID Detalle" => $detalleData['id'],
                "ID Factura" => $facturaId,  
                "Artículo" => $detalleData['articulo'],
                "Descripción" => $detalleData['descripcion'],
                "Cantidad" => $detalleData['cantidad'],
                "Precio Venta" => $detalleData['precio_venta'],
                "% de IVA" => $detalleData['tipo_iva'],
                "% de Descuento" => $detalleData['porcen_dto'],
                "Descuento Lineal" => $detalleData['dto_lineal'], 
                "Año" => $facturaData['anio'],
                "Tipo" => $facturaData['tipo_factura'],
                "Serie" => $facturaData['serie'],
                "Número de Factura" => $facturaData['factura'],
                "Fecha de Emisión" => $facturaData['fecha_factura'],
                "Fecha de Vencimiento" => $facturaData['fecha_vto'],
                "factura Pagada" => $facturaData['pagada'],
                "Razon Social Cliente" => $facturaData['nombre_cliente'],
                "Almacén" => $facturaData['almacen'],
                "Concepto" => $facturaData['concepto'],
                "Forma de Pago" => $facturaData['forma_pago'],
                "Divisa" => $facturaData['divisa'],
                "Serie Albarán" => $facturaData['albaran_serie'],
                "Número Albarán" => $facturaData['albaran_num'],
                "Bultos" => $facturaData['bultos'],
                "Tasa Conversión" => $facturaData['tasa_conversion'],
                "Valor en euros" => $facturaData['valor_en_euros'],
                "Importe Detalles" => $facturaData['importe_detalles'],
                "Base imponible" => $facturaData['base_imponible'],
                "IVA" => $facturaData['iva'],
                "Recargo Equivalente" => $facturaData['recargo_equiv'],
                "Total factura" => $facturaData['total_factura'],
                "% Descuento por Pronto Pago" => $facturaData['porcen_dto_pp'],
                "% Descuento Especial" => $facturaData['porcen_dto_especial'],
                "% Recargo Financiero" => $facturaData['porcen_rec_financiero'],
                "Importe Recargo Financiero" => $facturaData['rec_financiero'],
                "Entrega a Cuenta" => $facturaData['entrega_a_cuenta'],                
                "% IRPF" => $facturaData['porcen_irpf'],
                "Importe IRPF" => $facturaData['irpf'], 
            ];
        }

        return new JsonResponse(["Facturas Emitidas" => $responseData]);
    }
}
