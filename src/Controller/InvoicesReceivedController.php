<?php

namespace App\Controller;

use SimpleXMLElement;
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
            //$xmlResponse3 = simplexml_load_string($invoiceDetails3);

            // Verificar si la respuesta es válida antes de intentar acceder a sus propiedades-TODO: ACTIVAR CUANDO FUNCIONE LA API
            if ($xmlResponse3 = simplexml_load_string($invoiceDetails3)) {
                $facturaResponse[$facturaId] = (array)$xmlResponse3->factura;
            }

            // Almacenar los datos en arreglos asociativos
            $detalleResponse[$facturaId] = (array)$xmlResponse2->detalle;
            //$facturaResponse[$facturaId] = (array)$xmlResponse3->factura;  TODO: ACTIVAR CUANDO FUNCIONE LA API
        }
        //Array para mostrar la informacion unificada
        $responseData = [];
        // Relaciono el factura_Id de $detalle con el Id de $factura para obtener los productos vendidos que estan en detalle con el resto de la informacion que esta en factura
        foreach ($detalleResponse as $facturaId => $detalleData) {
            //$facturaData = $facturaResponse[$facturaId];TODO: ACTIVAR CUANDO FUNCIONE LA API
            $facturaData = isset($facturaResponse[$facturaId]) ? $facturaResponse[$facturaId] : [];

            $responseData[] = [
                "ID Detalle" => $detalleData['id'],
                "ID Factura" => $facturaId,
                "Artículo" => $detalleData['articulo'],
                "Descripción" => $detalleData['descripcion'],
                "Cantidad" => $detalleData['cantidad'],
                "Coste Divisa" => $detalleData['coste_divisa'],
                "Base Imponible" => $detalleData['base_imponible'],
                "% de IVA" => $detalleData['tipo_iva'],
                "IVA" => $detalleData['iva'],
                "Total Detalle" => $detalleData['total_detalle'],
                "% de Descuento" => $detalleData['porcen_dto'],
                "Descuento Lineal" => $detalleData['dto_lineal'],
                "Año" => isset($facturaData['anio']) ? $facturaData['anio'] : "",
                "Tipo" => isset($facturaData['tipo_factura']) ? $facturaData['tipo_factura'] : "",
                "Serie" => isset($facturaData['serie']) ? $facturaData['serie'] : "",
                "Número de Factura" => isset($facturaData['factura']) ? $facturaData['factura'] : "",
                "Fecha de Emisión" => isset($facturaData['fecha_factura']) ? $facturaData['fecha_factura'] : "",
                "Fecha de Vencimiento" => isset($facturaData['fecha_vto']) ? $facturaData['fecha_vto'] : "",
                "Factura Pagada" => isset($facturaData['pagada']) ? $facturaData['pagada'] : "",
                "Razon Social Cliente" => isset($facturaData['nombre_cliente']) ? $facturaData['nombre_cliente'] : "",
                "Almacén" => isset($facturaData['almacen']) ? $facturaData['almacen'] : "",
                "Concepto" => isset($facturaData['concepto']) ? $facturaData['concepto'] : "",
                "Forma de Pago" => isset($facturaData['forma_pago']) ? $facturaData['forma_pago'] : "",
                "Divisa" => isset($facturaData['divisa']) ? $facturaData['divisa'] : "",
                "Serie Albarán" => isset($facturaData['albaran_serie']) ? $facturaData['albaran_serie'] : "",
                "Número Albarán" => isset($facturaData['albaran_num']) ? $facturaData['albaran_num'] : "",
                "Bultos" => isset($facturaData['bultos']) ? $facturaData['bultos'] : "",
                "Tasa Conversión" => isset($facturaData['tasa_conversion']) ? $facturaData['tasa_conversion'] : "",
                "Valor en euros" => isset($facturaData['valor_en_euros']) ? $facturaData['valor_en_euros'] : "",
                "Importe Detalles" => isset($facturaData['importe_detalles']) ? $facturaData['importe_detalles'] : "",
                "Base imponible" => isset($facturaData['base_imponible']) ? $facturaData['base_imponible'] : "",
                "IVA" => isset($facturaData['iva']) ? $facturaData['iva'] : "",
                "Recargo Equivalente" => isset($facturaData['recargo_equiv']) ? $facturaData['recargo_equiv'] : "",
                "Total factura" => isset($facturaData['total_factura']) ? $facturaData['total_factura'] : "",
                "% Descuento por Pronto Pago" => isset($facturaData['porcen_dto_pp']) ? $facturaData['porcen_dto_pp'] : "",
                "% Descuento Especial" => isset($facturaData['porcen_dto_especial']) ? $facturaData['porcen_dto_especial'] : "",
                "% Recargo Financiero" => isset($facturaData['porcen_rec_financiero']) ? $facturaData['porcen_rec_financiero'] : "",
                "Importe Recargo Financiero" => isset($facturaData['rec_financiero']) ? $facturaData['rec_financiero'] : "",
                "Entrega a Cuenta" => isset($facturaData['entrega_a_cuenta']) ? $facturaData['entrega_a_cuenta'] : "",
                "% IRPF" => isset($facturaData['porcen_irpf']) ? $facturaData['porcen_irpf'] : "",
                "Importe IRPF" => isset($facturaData['irpf']) ? $facturaData['irpf'] : "",
            ];
        }

        return new JsonResponse(["Facturas Emitidas" => $responseData]);
    }
}
