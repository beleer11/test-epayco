# Prueba de una billetera electronica

_Prueba de desarrollo para Epayco._


### Instalación

1. Clonar el repositorio.

```sh 
git clone https://github.com/beleer11/test-epayco.git
```

2. Instalar dependencias.

```sh 
composer install
```
3. Crear BD, ejemplo.

```sh 
`CREATE DATABASE test_epayco COLLATE 'utf8mb4_general_ci';`
```

4. Duplique el archivo `.env.example` incluido en uno de nombre `.env` y dentro de este ingrese los valores de las variables de entorno necesarias, las básicas serían las siguientes:
- `DB_HOST="value"` Variable de entorno para el host de BD.
- `DB_PORT="value"` Variable de entorno para el puerto de BD.
- `DB_DATABASE="value"` Variable de entorno para el nombre de BD.
- `DB_USERNAME="value"` Variable de entorno para el usuario de BD.
- `DB_PASSWORD="value"` Variable de entorno para la contraseña de BD.

##### Notas:
```sh 
El sistema envia correos atravez de la libreria PHPMailer, debera configurar el correo gmail de preferencia para configurar las contraseñas de aplicaciones de gmail. Alli tendra que colocar con el nombre de PHPMailer. 
```
```sh 
Dentro del correo se encuentra la collecion de postman llamada: test-epayco.postman_collection
```

5. En la raíz del sitio ejecutar.
- `composer require phpmailer/phpmailer` Libreria de PhpMailer
- `php artisan migrate` Crea la estructura de BD. 
- `php artisan serve` Arranca el servidor web bajo la url [http://127.0.0.1:8000](http://127.0.0.1:8000).

##### Nota:
6. En la raíz del sitio usar este comando si se desea ejecutar las pruebas.
```sh 
vendor/bin/phpunit
```

## Descripción general de las URL's 

```sh 
Recuerde que las endpoint fueron almacenadas en el archivo routes/api.php
```
Método|URL|Descripción
 ------ | ------ | ------ 
GET|client/getClient/__{id}__|Consulta todos los clientes activos.
POST|client/clientRegistration|Registra clientes nuevos.
POST|wallet/createWallet|Crea nuevas billeteras virtuales.
POST|wallet/rechargeWallet|Recarga la billetera con dinero.
POST|wallet/checkBalance|Consulta el saldo de la billetera.
POST|transaction/payment|Ejecuta el pago.
POST|transaction/paymentConfirm|Confirma el pago realizado anteriormente.


##### Nota: 
- El parámetro __{id}__ Id de la notificación, debe ser numérico.

## Usuarios de prueba disponibles.

Email|Password|Rol|Permisos
 ------ | ------ | ------ | ------
mussebrahiam11@gmail.com|123456|Admin|Tiene toda la configuración de enviar correos.

##### Nota: 
Para configurar su propio usuario de correos debe modificar el archivo App/Services/EmailService.php.
## Autor️ 

* **Brahiam Musse** [mussebrahiam11@gmail.com](mailto:mussebrahiam11@gmail.com)


------------------------
