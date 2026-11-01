# API REST - Pizza Goool

Documentación de la API REST para la aplicación móvil de Pizza Goool.

**Base URL:** `http://127.0.0.1:8000/api`  
**Autenticación:** Bearer Token (Sanctum)  
**Formato:** JSON

---

## Índice

1. [Autenticación](#autenticación)
2. [Catálogo](#catálogo)
3. [Pedidos (Cliente)](#pedidos-cliente)
4. [Pedidos (Repartidor)](#pedidos-repartidor)
5. [Formato de respuestas](#formato-de-respuestas)
6. [Códigos HTTP](#códigos-http)

---

## Autenticación

### POST /register
Registrar un nuevo cliente.

**Body (JSON):**
```json
{
    "nombre": "Juan Pérez",
    "email": "juan@example.com",
    "password": "12345678",
    "password_confirmation": "12345678",
    "telefono": "71234567",
    "device_name": "app-movil"
}
```

`nombre` se acepta por compatibilidad con esta colección; también puede enviarse como `nombres`. `apellidos` es opcional.

**Respuesta exitosa (201):**
```json
{
    "success": true,
    "message": "Usuario registrado correctamente.",
    "data": {
        "token": "1|abc123...",
        "usuario": {
            "id": 1,
            "nombres": "Juan",
            "apellidos": "Pérez",
            "nombre_completo": "Juan Pérez",
            "email": "juan@example.com",
            "rol": "cliente",
            "telefono": "71234567"
        }
    }
}
```

### POST /login
Iniciar sesión y obtener un token.

**Body (JSON):**
```json
{
    "email": "cliente@goool.com",
    "password": "12345678",
    "device_name": "app-movil"
}
```

**Respuesta exitosa (200):**
```json
{
    "success": true,
    "message": "Login exitoso.",
    "data": {
        "token": "1|abc123...",
        "usuario": {
            "id": 4,
            "nombres": "Maria",
            "apellidos": "Lopez",
            "nombre_completo": "Maria Lopez",
            "email": "cliente@goool.com",
            "rol": "cliente",
            "telefono": "71234570"
        }
    }
}
```

### POST /logout
Cerrar sesión.

**Headers:**
```text
Authorization: Bearer {token}
```

**Respuesta exitosa (200):**
```json
{
    "success": true,
    "message": "Sesión cerrada correctamente."
}
```

### GET /user
Obtener datos del usuario autenticado.

**Headers:**
```text
Authorization: Bearer {token}
```

**Respuesta exitosa (200):**
```json
{
    "success": true,
    "message": "Datos del usuario.",
    "data": {
        "usuario": {
            "id": 4,
        "nombres": "Maria",
        "apellidos": "Lopez",
        "nombre_completo": "Maria Lopez",
        "email": "cliente@goool.com",
        "rol": "cliente",
            "telefono": "71234570",
            "created_at": "2026-09-29T15:04:33.000000Z"
        }
    }
}
```

---

## Catálogo

### GET /categorias
Lista todas las categorías (Acceso público).

**Respuesta exitosa (200):**
```json
{
    "success": true,
    "message": "Lista de categorías.",
    "data": [
        { "id": 1, "nombre": "Pizzas", "descripcion": "..." },
        { "id": 2, "nombre": "Bebidas", "descripcion": "..." }
    ]
}
```

### GET /productos
Lista los productos disponibles (Acceso público).

**Query Params (Opcionales):**
* `categoria_id` -> Filtrar por el ID de la categoría.
* `buscar` -> Buscar por coincidencia en el nombre.

**Respuesta exitosa (200):**
```json
{
    "success": true,
    "message": "Lista de productos.",
    "data": [
        {
            "id": 1,
            "nombre": "Pizza Napolitana",
            "descripcion": "...",
            "precio": 45.00,
            "imagen": "http://127.0.0.1:8000/storage/productos/...",
            "disponible": true,
            "categoria": {
                "id": 1,
                "nombre": "Pizzas",
                "descripcion": "..."
            }
        }
    ]
}
```

### GET /productos/{id}
Detalle de un producto específico (Acceso público).

---

## Pedidos (Cliente)
*Requiere token de Sanctum con rol `cliente`.*

### POST /pedidos
Crear un nuevo pedido.

**Body (JSON):**
```json
{
    "items": [
        { "producto_id": 1, "cantidad": 2 },
        { "producto_id": 4, "cantidad": 1 }
    ],
    "metodo_pago": "efectivo",
    "tipo_entrega": "contraentrega",
    "direccion": "Av. Principal #123",
    "referencia": "Piso 2, portón verde",
    "latitud": -16.5,
    "longitud": -68.15
}
```

**Respuesta exitosa (201):**
```json
{
    "success": true,
    "message": "Pedido creado correctamente.",
    "data": {
        "id": 1,
        "codigo": "#000001",
        "total": 100.00,
        "estado": "pendiente",
        "metodo_pago": "efectivo",
        "tipo_entrega": "contraentrega",
        "direccion": "Av. Principal #123",
        "referencia": "Piso 2, portón verde",
        "latitud": -16.5,
        "longitud": -68.15,
        "fecha": "29/09/2026 15:30",
        "fecha_iso": "2026-09-29T15:30:00.000000Z",
        "usuario": {
            "id": 4,
            "nombres": "Maria",
            "apellidos": "Lopez",
            "nombre_completo": "Maria Lopez",
            "telefono": "71234570",
            "email": "cliente@goool.com"
        },
        "detalle": [
            {
                "id": 1,
                "producto_id": 1,
                "producto": "Pizza Napolitana",
                "cantidad": 2,
                "precio_unitario": 45.00,
                "subtotal": 90.00
            }
        ],
        "repartidor": null,
        "pago": {
            "id": 1,
            "monto": 100.00,
            "metodo": "efectivo",
            "estado_pago": "pendiente",
            "codigo_qr": null
        }
    }
}
```

### GET /pedidos
Listar los pedidos del cliente autenticado.

### GET /pedidos/{id}
Ver el detalle de un pedido específico.

La relación de pago es de uno a uno. La respuesta mantiene `pago` como objeto o `null`; los detalles conservan el precio unitario confirmado al crear el pedido.

### DELETE /pedidos/{id}
Cancelar un pedido (Solo si se encuentra en estado `pendiente`).

**Respuesta exitosa (200):**
```json
{
    "success": true,
    "message": "Pedido cancelado correctamente."
}
```

---

## Pedidos (Repartidor)
*Requiere token de Sanctum con rol `repartidor`.*

### GET /repartidor/pedidos
Listar los pedidos asignados al repartidor autenticado.

### GET /repartidor/pedidos/{id}
Ver el detalle de un pedido asignado.

### PATCH /repartidor/pedidos/{id}/entregar
Marcar un pedido como entregado.

**Respuesta exitosa (200):**
```json
{
    "success": true,
    "message": "Pedido marcado como entregado correctamente.",
    "data": {
        "id": 1,
        "estado": "entregado"
    }
}
```

---

## Formato de respuestas

### Éxito
```json
{
    "success": true,
    "message": "Mensaje descriptivo.",
    "data": { }
}
```

### Error
```json
{
    "success": false,
    "message": "Mensaje de error.",
    "errors": { }
}
```

---

## Códigos HTTP

| Código | Significado |
| :--- | :--- |
| **200** | OK |
| **201** | Creado |
| **400** | Solicitud incorrecta |
| **401** | No autenticado |
| **403** | No autorizado |
| **404** | No encontrado |
| **422** | Error de validación |
| **500** | Error interno del servidor |
