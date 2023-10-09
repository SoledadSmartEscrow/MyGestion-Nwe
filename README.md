Elementos necesarios para el manejo del API
Url servidor myGESTIÓN. 
Autenticación. Deberá programar su cliente API con autenticación BASICA.
Como alternativa a la autenticación, puede pasar su usuario y password como parámetros en la URL del servidor myGESTIÓN, siendo esto nada recomendable.
Es importante que programe su cliente API de tal forma que pueda guardar sesión con el servidor API. De no ser así, en cada una de las peticiones el servidor se verá obligado a autenticar la petición, crear usuario y sesión nueva cada vez, perdiendo eficiencia y dandose un retraso de 30 segundos entre peticiones.


CURLOPT_URL => 									//Dirección con parámetros a la que nos queremos conectar.
    "https://app05.mygestion.com/appMg/api/"	//URL servidor, común a todas las llamadas.
    ."ApiClientes/"								//Página del api, en este caso clientes.
    ."?"										//Empiezan los parámetros.
    ."user=usuario"								//La segunda parte de la igualdad corresponde al nombre con el que nos registramos en myGESTIÓN.
    ."&"
    ."password=password"	