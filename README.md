# Guía de Uso de la API de MyGestion  :eyes:

## Elementos necesarios para el manejo del API
1-URL servidor: común a todas las llamadas =>  "https://app05.mygestion.com/appMg/api/"<br>

2-Deberá programar su cliente API con autenticación *BASICA*. Como alternativa a la autenticación,  puede pasar su usuario y password como parámetros en la URL del servidor myGESTIÓN<br>
Es importante que programe su cliente API de tal forma que pueda guardar sesión con el servidor API. De no ser así, en cada una de las peticiones el servidor se verá obligado  <br>
a autenticar la petición, crear usuario y sesión nueva cada vez, perdiendo eficiencia y dandose un retraso de 30 segundos entre peticiones.<br>
 
3-Referencia a la tabla a la que queremos acceder: Hay muchas disponibles, en este caso utilizaremos para FACTURAS RECIBIDAS el siguiente orden:<br>
 * 'ApiDetallesFacturasProveedor' = Para obtener todos los ID y los factura_Id<br>
 * 'ApiDetallesFacturasProveedor' = Para pasarle cada ID obtenido en la 1º llamada  y nos devuelva los productos de la factura<br>
 * 'ApiFacturasProveedor' = Para pasarle cada factura_Id obtenido en la 1º llamada  y relacionarlo con los ID de la 2º llamada para que nos devuelva los totales de la <br>
 factura que corresponden a ese producto.<br>

 4-Referencia a la tabla a la que queremos acceder: Hay muchas disponibles, en este caso utilizaremos para FACTURAS EMITIDAS el siguiente orden:<br>
 * 'ApiDetallesFacturasCliente' = Para obtener todos los ID y los factura_Id<br>
 * 'ApiDetallesFacturasCliente' = Para pasarle cada ID obtenido en la 1º llamada  y nos devuelva los productos de la factura<br>
 * 'ApiFacturasCliente' = Para pasarle cada factura_Id obtenido en la 1º llamada  y relacionarlo con los ID de la 2º llamada para que nos devuelva los totales de la <br>
 factura que corresponden a ese producto.<br>

5- Link a la documentación de la [API](https://www.facturandoenlanube.com/documentacion-api)<br>

### Nota Importante
Al 13/10/2023 la llamada 'ApiFacturasProveedor' esta devolviendo vacia por lo que esta ajustado en el codigo para manejar esto hasta tanto se revierta.<br>
Se envio un mail consultando este problema a MYGESTION el dia 12/10/2023






