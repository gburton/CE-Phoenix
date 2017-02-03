privacy_title = Política de privacidad

privacy_body = <h3>Credenciales API<small>(disponibles sólo en los siguientes países :api_req_countries)</small></h3>

<p>La App de PayPal para osCommerce Online Merchant permite a los propietarios de una tienda configurar automáticamente la App con sus credenciales API sin necesidad de introducirlas manualmente. Esto se consigue permitiendo a osCommerce el acceso para solicitar las credenciales API de la cuenta de PayPal del propietario de la tienda.</p>

<p>Autorizar el acceso a osCommerce permite que la ssiguiente información sea recuperada desde la cuenta de PayPal del propietario de la tienda:</p>

<ul>
  <li>Usuario API</li>
  <li>Contraseña API</li>
  <li>Firma API</li>
  <li>ID de la cuenta</li>
</ul>

<p>No se accede a otra información (por ejemplo, usuario o contraseña de la cuenta, saldo, historial de transacciones...):</p>

<p>El Usuario API, Contraseña API, Firma API eID de la cuenta se usan para configurar automáticamente los móduos de PayPal incluidos en la App:</p>

<ul>
  <li>PayPal Payments Standard</li>
  <li>PayPal Express Checkout</li>
  <li>PayPal Payments Pro (Direct Payment)</li>
  <li>PayPal Payments Pro (Hosted Solution)</li>
  <li>Log In with PayPal</li>
</ul>

<p>El proceso se inicia usando los botones "Recuperar Credenciales Live" y "Recuperar Credenciales Sandbox" en las págunas de inicio y administración de credenciales de la App de PayPal. El propietario de la tienda es redirigido de forma segura al sitio web de PayPal donde se le solicita que conceda permisos de acceso a osCommerce para recuperar las credenciales API, tras lo que es redirigido de vuelta a su tienda para continuar con la configuración de la App. Esto se consigue a través de los siguientes pasos:</p>
<ol>
  <li>El propietario pulsa "Recuperar Credenciales Live" o "Recuperar Credenciales Sandbox" y es redirigido de forma segura a una página de inicialización en el sitio web de osCommerce que registra su petición e inmediatamente le redirige a una página de integración en el sitio web de PayPal. osCommerce registra la siguiente información en esta solicitud:
    <ul>
      <li>Un ID de sesión único generado.</li>
      <li>Un ID secreto para cotejar con el ID de sesión.</li>
      <li>La URL de la App de PayPal del propietario de la tienda(para redirigirle de vuelta).</li>
      <li>La dirección IP del propietario de la tienda.</li>
    </ul>
  </li>
  <li>PayPal solicita al propietario de la tienda que acceda a su cuenta de PayPal exiistente o cree una nueva.</li>
  <li>PayPal solicita al propietario de la tienda que conceda permisos a osCommerce para recuperar sus credenciales API.</li>
  <li>PayPal redirige al propietario de la tienda de vuelta a la página de inicialización en el sitio web de osCommerce.</li>
  <li>osCommerce recupera de forma segura y almacena la siguiente información de PayPal:
    <ul>
      <li>Usuario API</li>
      <li>Contraseña API</li>
      <li>Firma API</li>
      <li>ID de la cuenta</li>
    </ul>
  </li>
  <li>El propietario de la tienda es redirigido automáticamente de vuelta a su App de PayPal.</li>
  <li>La App de PayPal realiza una llamada HTTPS segura al sitio web de osCommerce website para recuperar las credenciales API.</li>
  <li>El sitio web de osCommerce autentifica la llamada segura, envía las credenciales y elimina localmente las credenciales API y la URL de la App de PayPal almacenadas en los pasos 1 y 5.</li>
  <li>La App de PayPal se auto configura con las credenciales API.</li>
</ol>

<div class="pp-panel pp-panel-warning">
  <p>Las credenciales APIrecuperadas de la cuenta de paypal del propietario de la tienda sólo son empleadas para configurar la App de Paypal. osCommerce almacena temporalmente las credenciales API tal y como se describe en esta política de provacidad, y descarta las credenciales API tan pronto el proceso finaliza. También se ejecuta un script para descartar toda información almacenada sobre procesos no finalizados.</p>
</div>

<div class="pp-panel pp-panel-info">
  <p>osCommerce ha trabajado estrechamente con PayPal para asegurarse que la App de PayPal sigue políticas de seguridad y pivacidad estrictas.</p>
</div>

<h3>Módulos de PayPal</h3>

<p>Los módulos de PayPal envían al propietario, a la tienda online y al cliente información relacionada con PayPal para procesar las transacciones API. Esto incluye los siguientes módulos:</p>

<ul>
  <li>PayPal Payments Standard</li>
  <li>PayPal Express Checkout</li>
  <li>PayPal Payments Pro (Direct Payment)</li>
  <li>PayPal Payments Pro (Hosted Solution)</li>
  <li>Log In with PayPal</li>
</ul>

<p>La siguiente información se incluye en las llamadas API enviadas a PayPal:</p>

<ul>
  <li>Información de la cuenta PayPal del vendedor / propietario de la tienda, incluyendo dirección de correo electrónico y credenciales API.</li>
  <li>Direcciones de envío y facturación del cliente..</li>
  <li>Información del producto, incluyendo nombre, precio y cantidad.</li>
  <li>Información de envío e impuestos aplicables al pedido.</li>
  <li>Total del pedido y moneda.</li>
  <li>Direccción URL de la tienda para procesar, verificar y finalizar las transacciones de PayPal, incluyendo completadas, canceladas y URLs IPN.</li>
  <li>Identificación de la solución E-Commerce.</li>
</ul>

<div class="pp-panel pp-panel-info">
  <p>Los parámetros de cada transacción enviados y recibidos a PayPal pueden ser inspeccionados en la página de registro de la App.</p>
</div>

<h3>Actualizaciones de la App</h3>

<p>La App de PayPal para osCommerce Online Merchant comprueba automáticamente el sitio web de osCommerce website en busca de actualizaciones disponibles. Esta comprobación se ejecuta cada 24 horas y, en caso de que exista una actualización se muestra una notificacion para dar permiso a la App para descargar y aplicar la actualización.</p>

<p>Se puede ejecutar una comprobación manual desde la página de información de la App.</p>

<h3>Google Hosted Libraries (jQuery y jQuery UI)</h3>

<p>Si jQuery or jQuery UI no están cargadas en la Herramienta de Administración,la App de PayPal las carga automáticamente las librerías de forma segura a través de Google Hosted Libraries.</p>
