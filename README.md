# Integrated Popup Plugin

Un plugin de WordPress para gestionar y mostrar popups de manera integrada con CodeIgniter.

## Descripción

Este plugin permite crear y gestionar popups personalizables en WordPress, con la capacidad de sincronizar la configuración con un backend de CodeIgniter. Ofrece una interfaz administrativa intuitiva y opciones de personalización extensivas.

## Características

- Creación y gestión de múltiples popups
- Personalización completa del diseño
  - Color de fondo
  - Color del texto
  - Dimensiones personalizables
  - Posicionamiento flexible
- Condiciones de visualización
  - Mostrar una sola vez por usuario
  - Retraso en la aparición
  - Selección de páginas específicas
- Integración con CodeIgniter
  - Sincronización automática
  - API REST
  - Gestión de configuraciones

## Requisitos

- WordPress 5.0 o superior
- PHP 7.4 o superior
- MySQL 5.6 o superior
- Servidor CodeIgniter configurado (para la integración)

## Instalación

1. Descarga el plugin
2. Sube la carpeta `integrated-popup` al directorio `/wp-content/plugins/`
3. Activa el plugin desde el panel de WordPress
4. Configura la API Key y URL del servidor CodeIgniter en Ajustes > Integrated Popup

## Configuración

### Configuración Básica

1. Ve a "Popups" en el menú de WordPress
2. Haz clic en "Añadir Nuevo"
3. Configura el contenido y estilo del popup
4. Guarda los cambios

### Configuración de la API

1. Ve a "Popups > Configuración"
2. Ingresa la API Key proporcionada por tu servidor CodeIgniter
3. Configura la URL base de la API
4. Prueba la conexión con el botón "Probar Conexión"

## Uso

### Crear un Nuevo Popup

1. Ve a "Popups > Añadir Nuevo"
2. Completa los siguientes campos:
   - Título del popup
   - Contenido
   - Estilos (colores, dimensiones)
   - Condiciones de visualización
3. Haz clic en "Guardar"

### Gestionar Popups Existentes

1. Ve a "Popups"
2. Verás una lista de todos los popups
3. Puedes:
   - Editar popups existentes
   - Activar/desactivar popups
   - Eliminar popups
   - Ver estadísticas de visualización

## Integración con CodeIgniter

El plugin se integra con un backend de CodeIgniter a través de una API REST. Asegúrate de:

1. Tener configurada correctamente la API Key
2. Configurar la URL base de la API
3. Verificar la conexión en la página de configuración

## Estructura de Archivos

```
integrated-popup/
├── assets/
│   ├── css/
│   │   ├── admin.css
│   │   └── popup.css
│   └── js/
│       └── popup.js
├── includes/
│   ├── class-admin.php
│   ├── class-admin-menu.php
│   ├── class-ajax.php
│   ├── class-api-test.php
│   ├── class-autoloader.php
│   ├── class-core.php
│   ├── class-database.php
│   └── class-list-table.php
├── templates/
│   └── admin/
│       ├── main-page.php
│       ├── new-popup.php
│       └── settings.php
└── integrated-popup.php
```

## Soporte

Para soporte técnico o reportar problemas:

1. Abre un issue en el repositorio
2. Visita nuestra documentación en línea

## Contribuir

¡Las contribuciones son bienvenidas!

## Licencia

Este plugin está licenciado bajo la GPL v2 o posterior.

## Changelog

### 1.0.0
- Lanzamiento inicial
- Funcionalidad básica de popups
- Integración con CodeIgniter
- Panel de administración

### 1.0.1
- Corrección de errores menores
- Mejoras en la interfaz de usuario
- Optimización de rendimiento


## FAQ

### ¿Cómo obtengo la API Key?

La API Key debe ser proporcionada por tu servidor CodeIgniter. Contacta con el administrador del sistema para obtenerla.

### ¿Puedo usar el plugin sin CodeIgniter?

Sí, el plugin funciona de manera independiente, pero algunas características de sincronización no estarán disponibles.

### ¿Cómo personalizo el estilo del popup?

Puedes personalizar todos los aspectos visuales desde el panel de administración, incluyendo colores, tamaños y posición.