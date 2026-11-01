# Guía de arquitectura — Pizza Goool

Esta guía explica dónde vive cada responsabilidad, cómo se relacionan los módulos y qué archivos revisar antes de modificar un flujo. El proyecto sigue la estructura convencional de Laravel; los comentarios del código se reservan para reglas de negocio, límites y decisiones que no se deducen sólo leyendo el nombre de un método.

## Mapa rápido

| Necesito cambiar… | Empiezo por… | También reviso… |
|---|---|---|
| Una página o acción web | `routes/web.php` | Controlador del rol, vista Blade y middleware |
| Una operación de la aplicación móvil | `routes/api.php` | Controlador API, Resource, middleware y `docs/API.md` |
| Los datos o relaciones | `app/Models/` | Migración nueva, controladores, Resources, seeders y pruebas |
| El proceso de compra web | `CatalogoController` y `PedidoController` del cliente | `cliente/checkout.blade.php`, `Pedido`, `Pago` y pruebas de integridad |
| El proceso de compra móvil | `Api/PedidoController` | `PedidoResource`, validación y documentación API |
| Un estado o transición de pedido | `Pedido::puedeCambiarEstado()` | Cajero, repartidor, API y pruebas |
| Una respuesta JSON | `app/Http/Resources/` o `ApiResponse` | Consumidor móvil y `docs/API.md` |
| Una pantalla | `resources/views/` | Ruta, controlador, layout y componentes utilizados |
| Datos de demostración | `database/seeders/DatabaseSeeder.php` | Restricciones de las migraciones y modelos |

Esta guía cubre **todos los módulos de la aplicación**: cliente, administración, caja, repartidor, autenticación, API móvil, carrito, persistencia, vistas, pruebas y documentación. No es una guía exclusiva del área de cliente.

## Qué revisar cuando se cambia algo

| Si cambias… | Archivos/áreas que normalmente cambian | Efecto o riesgo de no revisar el resto |
|---|---|---|
| Una ruta o URL | `routes/web.php` o `routes/api.php`, enlaces `route()`, middleware y prueba de endpoint | Puede romper enlaces, dejar una acción sin protección por rol/autenticación o exponer una respuesta distinta. |
| Un permiso o rol | `Usuario.rol`, `VerificarRol`/`ApiVerificarRol`, grupos de rutas y pantallas de administración de usuarios | Un rol puede perder acceso legítimo o ganar acceso accidental a páginas, pedidos o datos ajenos. |
| Un campo o relación de base de datos | Nueva migración, modelo `$fillable`/relaciones, controladores, Resource, vistas, seeder y pruebas | La migración sola no actualiza automáticamente formularios ni JSON. Una columna retirada puede romper lecturas, escrituras o instalaciones nuevas. |
| Una regla del pedido o su estado | `Pedido::puedeCambiarEstado()`, controladores de caja/repartidor/API, botones de las vistas y pruebas | Distintos canales podrían aceptar transiciones incompatibles o dejar pedidos bloqueados/sin opción para terminarse. |
| Precio, cantidad o disponibilidad | Catálogo web, checkout, API de pedidos, `DetallePedido.precio_unitario`, totales y pruebas | El cliente podría pagar un importe desactualizado o el historial podría mostrar el precio actual en vez del precio acordado. |
| Datos de entrega | Checkout web, creación de pedido API, `pedidos`, vista del repartidor, Resource y documentación API | La dirección o coordenadas podrían no llegar a quien entrega, guardarse en el lugar equivocado o cambiar pedidos históricos. |
| Una respuesta/campo de la API | Controlador, `Http/Resources/*`, validación, `docs/API.md` y cliente móvil | Cambiar o quitar una clave JSON puede romper versiones instaladas de la app aunque la web siga funcionando. |
| Una vista o estilo | Vista Blade, layout, componentes, controlador que la prepara y rutas | Una vista puede esperar variables que el controlador no entrega; modificar un layout puede afectar muchas pantallas del mismo rol. |
| Registro/autenticación | `routes/auth.php`, controladores `Auth/*`, `Usuario`, perfil de rol, middleware y pruebas Auth/API | Se pueden crear cuentas sin perfil, cambiar el destino posterior al login o invalidar sesiones/tokens de clientes existentes. |
| Categoría o producto | CRUD Admin, modelos, vistas de catálogo, carrito, detalles históricos, restricciones y API | El producto puede dejar de aparecer o un borrado puede afectar pedidos pasados; el precio del historial debe permanecer intacto. |
| Pago o configuración QR | Modelo `Pago`, `Configuracion`, controladores Admin/API, checkout, recursos y documentación | El estado/importe mostrado puede divergir del pedido o el QR visible para cliente y móvil puede quedar desactualizado. |
| Seeder o fábrica | `database/seeders/` y `database/factories/` | Las pruebas o los datos de demostración pueden dejar de satisfacer índices, relaciones o reglas de negocio reales. |
| Una prueba | Caso de prueba correspondiente y fixtures/helpers que usa | Una prueba que no refleja el contrato real puede pasar mientras una ruta, persistencia o permiso falla en producción. |

### Cómo seguir un cambio de extremo a extremo

1. **Encuentra la entrada:** busca el nombre de ruta con `php artisan route:list` o busca la vista/API correspondiente.
2. **Sigue el flujo:** ruta → middleware → controlador → modelo/relaciones → vista Blade o Resource JSON.
3. **Revisa las otras superficies:** web, API móvil, paneles de admin/caja/repartidor y seeders si comparten el dato.
4. **Protege la persistencia:** si cambia el esquema, crea una migración nueva; si cambia una regla, aplica la misma invariante en cada camino que pueda escribir esos datos.
5. **Actualiza el contrato:** modifica documentación API cuando cambie la entrada/salida o el comportamiento móvil.
6. **Prueba el comportamiento observable:** prueba el camino válido y el rechazo importante (rol incorrecto, datos inválidos, estado no permitido o registro no relacionado).

## Estructura del repositorio

### Aplicación — `app/`

- `Models/`: entidades Eloquent y relaciones con la base. `Usuario` es la identidad autenticable; los perfiles personales están separados por rol.
- `Http/Controllers/`: coordinación de solicitudes HTTP. Los controladores se agrupan por área:
  - `Admin/`: dashboard, productos, categorías, usuarios, pedidos, reportes y configuración QR.
  - `Api/`: autenticación, catálogo público, pedidos y operaciones del repartidor.
  - `Auth/`: registro, inicio/cierre de sesión, verificación y recuperación de contraseña.
  - `Cliente/`: catálogo/carrito, checkout, historial y cancelación de pedidos.
  - `Cajero/`: consulta de pedidos, cambios de estado y asignación de repartidores.
  - `repartidor/`: entregas asignadas y actualización del resultado de entrega.
- `Http/Requests/`: validación reutilizable de solicitudes, como acceso y edición del perfil.
- `Http/Resources/`: contrato de serialización JSON. Mantener compatibilidad con los nombres de campos que consume el cliente móvil.
- `Http/Middleware/`: restricciones de acceso según rol para web y API.
- `Http/Traits/ApiResponse.php`: formato común de respuestas JSON y errores.
- `Livewire/`: componentes reactivos del carrito y su contador.
- `View/Components/`: componentes PHP que seleccionan layouts Blade.
- `Providers/`: registro de servicios y configuración durante el arranque.

#### Controladores por responsabilidad

| Archivo | Responsabilidad |
|---|---|
| `Controllers/InicioController.php` | Preparar la página inicial. |
| `Controllers/ProfileController.php` | Editar datos de cuenta y aplicar la protección de borrado de cuenta. |
| `Controllers/Admin/DashboardController.php` | Resumen del panel administrativo. |
| `Controllers/Admin/CategoriaController.php` | CRUD de categorías. |
| `Controllers/Admin/ConfiguracionController.php` | Consultar configuración administrativa y guardar el QR de pago. |
| `Controllers/Admin/PedidoController.php` | Consultar pedidos en administración. |
| `Controllers/Admin/ProductoController.php` | CRUD de productos y protección del historial al borrar. |
| `Controllers/Admin/ReporteController.php` | Consultas de reportes y exportación PDF. |
| `Controllers/Admin/UsuarioController.php` | CRUD de cuentas y perfiles de acuerdo al rol. |
| `Controllers/Cliente/CatalogoController.php` | Catálogo, filtros, carrito de sesión y preparación del checkout. |
| `Controllers/Cliente/PedidoController.php` | Crear pedidos web, consultar historial y cancelar pedidos permitidos. |
| `Controllers/Cajero/DashboardController.php` | Resumen del panel de caja. |
| `Controllers/Cajero/PedidoController.php` | Lista/detalle, transiciones y asignación de repartidores. |
| `Controllers/repartidor/DashboardController.php` | Resumen de entregas asignadas. |
| `Controllers/repartidor/PedidoController.php` | Consultar pedidos propios, marcar entrega o registrar fallo. |
| `Controllers/Api/AuthController.php` | Registro, login, logout, token y perfil JSON del usuario. |
| `Controllers/Api/CategoriaController.php` | Listado JSON de categorías. |
| `Controllers/Api/ConfiguracionController.php` | Configuración pública consumida por la aplicación móvil. |
| `Controllers/Api/PedidoController.php` | Crear, listar, consultar y cancelar pedidos del usuario autenticado. |
| `Controllers/Api/ProductoController.php` | Catálogo y detalle de productos JSON. |
| `Controllers/Api/RepartidorController.php` | Pedidos del repartidor autenticado y entrega desde móvil. |
| `Controllers/Auth/AuthenticatedSessionController.php` | Formulario, inicio y cierre de sesión web. |
| `Controllers/Auth/RegisteredUserController.php` | Registro de cuentas web. |
| `Controllers/Auth/PasswordResetLinkController.php`, `NewPasswordController.php` | Solicitar y completar recuperación de contraseña. |
| `Controllers/Auth/PasswordController.php`, `ConfirmablePasswordController.php` | Cambio de contraseña y confirmación de operaciones sensibles. |
| `Controllers/Auth/EmailVerificationPromptController.php`, `VerifyEmailController.php`, `EmailVerificationNotificationController.php` | Pantalla, confirmación y reenvío de verificación de correo. |
| `Controllers/Controller.php` | Clase base compartida por controladores HTTP. |

#### Modelos y relaciones

| Modelo | Función |
|---|---|
| `Models/Usuario.php` | Autenticación, rol, perfiles 1:1, pedidos propios/asignados y datos derivados para mostrar. |
| `Models/Cliente.php` | Perfil de cliente vinculado a `Usuario`; datos de contacto. |
| `Models/Empleado.php` | Perfil laboral vinculado a `Usuario` para administración/caja. |
| `Models/Repartidor.php` | Datos personales y operativos de la cuenta repartidora. |
| `Models/Pedido.php` | Compra, estado, relaciones y validación de transiciones. |
| `Models/DetallePedido.php` | Producto, cantidad y precio histórico de una línea. |
| `Models/Pago.php` | Importe y estado del único pago asociado al pedido. |
| `Models/Producto.php` | Producto del catálogo y sus relaciones de categoría/detalles. |
| `Models/Categoria.php` | Categoría y productos relacionados. |
| `Models/Configuracion.php` | Lectura y guardado de valores clave-valor. |

### Entrada HTTP — `routes/` y `bootstrap/app.php`

- `routes/web.php`: rutas de navegación y acciones web, agrupadas por rol.
- `routes/api.php`: endpoints JSON públicos y endpoints protegidos por Sanctum.
- `routes/auth.php`: rutas convencionales de registro, sesión, contraseña y verificación.
- `routes/console.php`: comandos Artisan definidos por la aplicación.
- `bootstrap/app.php`: conecta los archivos de rutas, registra alias de middleware (`role`, `api.role`) y configura la representación JSON de errores.

El middleware `role` protege páginas web y redirige visitantes no autenticados al login; `api.role` entrega errores JSON 401/403. Los grupos de rutas son la primera referencia para saber quién puede ejecutar cada acción. La autorización del grupo no reemplaza la comprobación de propiedad de un pedido cuando corresponde.

### Interfaz — `resources/views/`

- `layouts/`: estructura compartida para cliente, invitado, administración, caja, repartidor y Breeze.
- `cliente/`: inicio, catálogo, checkout, perfil, historial y seguimiento.
- `admin/`: CRUD de categorías, productos y usuarios; pedidos, configuración y reportes.
- `cajero/`: panel, lista de pedidos y detalle.
- `repartidor/`: panel, entregas y detalle con información de ubicación del pedido.
- `auth/` y `profile/`: pantallas convencionales de autenticación y cuenta.
- `components/`: controles Blade reutilizables.
- `livewire/`: plantillas de los componentes Livewire del carrito.

Las vistas presentan datos; las reglas de negocio y persistencia pertenecen al controlador/modelo correspondiente. Si una página deja de reflejar cambios, seguir la ruta nombrada hasta el controlador que la prepara y la vista que renderiza.

#### Vistas por directorio

- `views/`: `welcome` y `dashboard`.
- `views/cliente/`: `catalogo`, `checkout`, `perfil`, `mis-pedidos`, `seguimiento`.
- `views/admin/`: `dashboard`, `configuracion`; vistas `index/create/edit` para categorías, productos y usuarios; lista de pedidos y vistas de reportes HTML/PDF.
- `views/cajero/`: `dashboard` y vistas de lista/detalle de pedidos.
- `views/repartidor/`: `dashboard` y vistas de lista/detalle de pedidos.
- `views/layouts/`: layouts de aplicación, invitado, cliente, admin, cajero y repartidor, además de navegación.
- `views/auth/`: login, registro, verificación, confirmación y recuperación de contraseña.
- `views/profile/`: edición de cuenta y parciales para información, contraseña y borrado.
- `views/components/`: logo, botones, campos, navegación, dropdown, modal, errores y patrón visual.
- `views/livewire/`: plantillas del carrito y del contador.

### Persistencia — `database/`

- `migrations/`: historial incremental del esquema. Una migración ya aplicada es inmutable; los cambios nuevos se agregan en otra migración.
- `seeders/DatabaseSeeder.php`: datos de demostración para roles, perfiles, catálogo, pedidos, detalles y pagos.
- `factories/`: fábricas de datos usadas por pruebas.

La migración `2026_11_01_000000_reforzar_integridad_pedidos_y_perfiles` agrega índices únicos y evita que ciertos borrados destruyan historial relacionado. La migración `2026_11_01_161830_remove_ubicacion_de_perfil_de_clientes` archiva datos de ubicación del perfil antes de retirar esos campos de `clientes`; las direcciones y coordenadas de entrega pertenecen al pedido.

### Contratos y validación

- `app/Http/Requests/`: valida datos de entrada antes de ejecutar la operación.
- `app/Http/Resources/`: define qué campos se exponen y cómo se anidan en la API.
- `docs/API.md`: ejemplos de solicitudes, respuestas y códigos HTTP para el cliente móvil.
- `docs/API-PizzaGoool.postman_collection.json`: colección Postman para explorar endpoints.

Al cambiar un endpoint, revisar conjuntamente ruta, autenticación/rol, validación, persistencia, Resource y documentación. La API y la web pueden crear pedidos por caminos diferentes; ambas deben respetar los mismos invariantes.

## Modelo de datos y relaciones

```text
usuarios 1 ── 0..1 clientes
usuarios 1 ── 0..1 empleados
usuarios 1 ── 0..1 repartidores
usuarios 1 ── N pedidos (cliente propietario)
usuarios 1 ── N pedidos (repartidor asignado, nullable)
categorias 1 ── N productos
pedidos 1 ── N detalle_pedidos
productos 1 ── N detalle_pedidos
pedidos 1 ── 0..1 pagos
```

- `usuarios.id` es la identidad común. `rol` determina si la cuenta actúa como `admin`, `cajero`, `cliente` o `repartidor`.
- Los perfiles en `clientes`, `empleados` y `repartidores` usan `usuario_id`; los índices únicos garantizan como máximo un perfil de cada tipo por cuenta. El perfil de cliente conserva nombres, apellidos y teléfono; no almacena una dirección predeterminada.
- `pedidos.usuario_id` referencia a `usuarios.id` y **no es único**: un cliente puede realizar varios pedidos. `pedidos.repartidor_id` también referencia a una cuenta de usuario y puede quedar vacío mientras no se asigne repartidor.
- `detalle_pedidos` conecta pedido y producto y conserva `precio_unitario` confirmado para no recalcular pedidos históricos con precios actuales.
- `pagos.pedido_id` es único: un pedido tiene como máximo un registro de pago.
- La cardinalidad del diagrama se lee desde `pedidos` hacia `pagos`: **un pedido puede tener cero o un pago** (`1 ── 0..1`). Cada fila de pago pertenece a exactamente un pedido. Los flujos de creación actuales crean el pago junto al pedido, pero la base no obliga a que todo pedido tenga ya un pago. Si una herramienta ER dibuja `1 ── N`, está usando una vista/exportación anterior o no está interpretando el índice único `pagos_pedido_id_unique`; refrescar el esquema del diagrama. La migración de integridad ya aplicada creó ese índice.
- `direccion`, `referencia`, `latitud` y `longitud` de entrega se guardan en `pedidos`, porque describen una compra concreta.
- `clientes_ubicaciones_archivadas` conserva los valores retirados del perfil por la migración de limpieza. No es fuente de direcciones para nuevas compras.

Las relaciones Eloquent se definen en los modelos de `app/Models/`. Para mantener los datos íntegros, revisar las claves foráneas e índices de las migraciones además de las validaciones HTTP.

## Flujos principales

### Compra web

1. `CatalogoController` muestra productos disponibles y guarda el carrito en sesión.
2. Al abrir checkout, vuelve a leer los productos actuales y sincroniza los precios del carrito.
3. `Cliente\PedidoController::procesar` valida entrega/pago y vuelve a comprobar disponibilidad, cantidades y precios dentro de una transacción.
4. Si el precio cambió, actualiza el carrito y pide confirmación antes de crear el pedido.
5. En una operación atómica crea `Pedido`, sus `DetallePedido` con precio histórico y su `Pago`; después limpia el carrito.
6. La ruta de seguimiento comprueba que el pedido pertenezca al usuario autenticado.

### Compra por API

`Api\PedidoController::store` valida `items`, vuelve a consultar y bloquear los productos, calcula el total desde precios guardados, y crea pedido, detalles y pago en una transacción. `PedidoResource` transforma el resultado al contrato JSON. Los endpoints de listado, detalle y cancelación restringen la consulta al usuario del token.

### Preparación y entrega

- Caja consulta y filtra pedidos; las transiciones válidas viven en `Pedido::puedeCambiarEstado()`.
- La asignación comprueba que el usuario tenga rol y perfil de repartidor y que el pedido sea delivery en estado compatible.
- El repartidor sólo puede consultar o cambiar pedidos asignados a su propia cuenta.
- Si se cambian estados, revisar a la vez el flujo web de caja, repartidor, API y pruebas para evitar reglas divergentes.

### Registro y perfiles

El registro web/API crea una cuenta `Usuario` y su fila de perfil dentro de una transacción. El endpoint API además devuelve un token Sanctum. Los campos personales se leen del perfil según rol; editar el perfil del cliente no guarda dirección ni ubicación. La dirección se solicita para cada entrega durante el checkout.

## Mapa por archivo de entrada

| Área | Rutas | Controladores principales | Vistas principales |
|---|---|---|---|
| Catálogo y compra web | `routes/web.php` | `Cliente/CatalogoController`, `Cliente/PedidoController` | `cliente/catalogo`, `cliente/checkout`, `cliente/seguimiento`, `cliente/mis-pedidos` |
| Cuenta cliente | `routes/web.php`, `routes/auth.php` | `ProfileController`, `Auth/*Controller` | `cliente/perfil`, `profile/*`, `auth/*` |
| Administración | `routes/web.php` | `Admin/*Controller` | `admin/dashboard`, `admin/productos/*`, `admin/categorias/*`, `admin/usuarios/*`, `admin/pedidos/*`, `admin/reportes/*`, `admin/configuracion` |
| Caja | `routes/web.php` | `Cajero/DashboardController`, `Cajero/PedidoController` | `cajero/dashboard`, `cajero/pedidos/*` |
| Repartidor web | `routes/web.php` | `repartidor/DashboardController`, `repartidor/PedidoController` | `repartidor/dashboard`, `repartidor/pedidos/*` |
| API móvil | `routes/api.php` | `Api/AuthController`, `Api/CategoriaController`, `Api/ProductoController`, `Api/PedidoController`, `Api/RepartidorController`, `Api/ConfiguracionController` | Sin Blade; respuestas con `Http/Resources/*` |

Los nombres de vistas se resuelven desde `resources/views/` con notación de puntos; por ejemplo, `cliente.checkout` corresponde a `resources/views/cliente/checkout.blade.php`.

### Mapa de pantallas y sus responsables

Las vistas contienen la presentación y los formularios; el controlador indicado prepara datos y recibe las acciones. Antes de cambiar el nombre de una variable Blade, revisar todas las acciones que renderizan esa plantilla.

| Rol/área | Vista(s) | Responsable habitual |
|---|---|---|
| Portada pública | `welcome.blade.php` | `InicioController::index` |
| Cliente | `cliente/catalogo.blade.php`, `cliente/checkout.blade.php` | `Cliente\CatalogoController`; la creación se procesa en `Cliente\PedidoController` |
| Cliente | `cliente/perfil.blade.php` | Ruta `cliente.perfil` y `ProfileController` para la edición |
| Cliente | `cliente/mis-pedidos.blade.php`, `cliente/seguimiento.blade.php` | `Cliente\PedidoController` y ruta de seguimiento |
| Administración | `admin/dashboard.blade.php` | `Admin\DashboardController` |
| Administración | `admin/categorias/{index,create,edit}.blade.php` | `Admin\CategoriaController` |
| Administración | `admin/productos/{index,create,edit}.blade.php` | `Admin\ProductoController` |
| Administración | `admin/usuarios/{index,create,edit}.blade.php` | `Admin\UsuarioController` |
| Administración | `admin/pedidos/index.blade.php` | `Admin\PedidoController` |
| Administración | `admin/reportes/{index,pdf}.blade.php` | `Admin\ReporteController`; ambos formatos deben conservar filtros y métricas equivalentes |
| Administración | `admin/configuracion.blade.php` | `Admin\ConfiguracionController` |
| Caja | `cajero/dashboard.blade.php`, `cajero/pedidos/index.blade.php`, `cajero/pedidos/show.blade.php` | `Cajero\DashboardController`, `Cajero\PedidoController` |
| Repartidor | `repartidor/dashboard.blade.php`, `repartidor/pedidos/index.blade.php`, `repartidor/pedidos/show.blade.php` | `repartidor\DashboardController`, `repartidor\PedidoController` |
| Autenticación | `auth/login.blade.php`, `auth/register.blade.php` | `AuthenticatedSessionController`, `RegisteredUserController` |
| Autenticación | `auth/forgot-password.blade.php`, `auth/reset-password.blade.php` | `PasswordResetLinkController`, `NewPasswordController` |
| Autenticación | `auth/confirm-password.blade.php`, `auth/verify-email.blade.php` | `ConfirmablePasswordController` y controladores de verificación |
| Perfil Breeze | `profile/edit.blade.php` y `profile/partials/*` | `ProfileController`, `ProfileUpdateRequest` y `PasswordController` |
| Diseños compartidos | `layouts/*` | Las páginas hijas seleccionan su layout con `@extends`; un cambio aquí puede afectar a todo ese rol |
| Componentes Blade | `components/*` | Se invocan desde otras vistas; conservar sus props, slots y atributos para no romper múltiples formularios |
| Livewire | `livewire/carrito-compras.blade.php`, `livewire/contador-carrito.blade.php` | `App\Livewire\CarritoCompras`, `App\Livewire\ContadorCarrito` |
| Plantillas base | `dashboard.blade.php` | Layout Breeze; confirmar su ruta/autorización antes de convertirla en dashboard de un rol |

Cada plantilla Blade lleva un comentario inicial con su propósito y el controlador, rol o contrato que debe mantener. En plantillas compartidas, validar que las props/slots usados por todas sus llamadas sigan coincidiendo.

## Pruebas y cambios seguros

- `tests/Feature/DatabaseIntegrityTest.php`: contratos de registro/pedidos, checkout, restricciones, estados y seeder.
- `tests/Feature/Auth/`: flujos de autenticación.
- `tests/Feature/ProfileTest.php`: edición y eliminación de cuenta.
- `tests/Feature/ExampleTest.php`, `tests/Unit/ExampleTest.php`: ejemplos generados por el esqueleto de Laravel/Pest.
- `tests/TestCase.php`: clase base para las pruebas.
- `tests/Pest.php`: configura `RefreshDatabase` para las pruebas Feature.
- Ejecutar la prueba enfocada con `php artisan test --compact tests\Feature\DatabaseIntegrityTest.php`; ejecutar el conjunto con `php artisan test`.
- Revisar rutas con `php artisan route:list --except-vendor`.
- No usar `migrate:fresh` en una base con datos reales. Confirmar destino de conexión y respaldo antes de aplicar migraciones.

**Pendiente conocido:** parte de las pruebas Breeze heredadas todavía importa `App\Models\User`, aunque la aplicación usa `App\Models\Usuario`. No confundir esos errores preexistentes con fallos de una funcionalidad nueva; modernizar esa suite es un trabajo separado.

## Convenciones para mantener esta guía útil

1. Seguir un cambio desde la ruta hasta su controlador, modelo, vista/Resource y pruebas relacionadas.
2. Añadir una migración nueva en vez de reescribir una migración compartida o aplicada.
3. Actualizar este mapa cuando se agregue un módulo, rol, flujo o relación nueva.
4. Preferir nombres claros; comentar el motivo de una regla, límite o decisión, no repetir la instrucción que ya expresa el código.
5. No incluir secretos ni valores de `.env` en documentación, ejemplos, capturas o commits.
