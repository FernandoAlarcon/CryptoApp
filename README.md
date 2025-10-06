CryptoInvestment - Dashboard de Criptomonedas

## Descripción del Proyecto

CryptoInvestment es una aplicación web SPA (Single Page Application) desarrollada para un grupo de inversores en criptomonedas que necesitan seguir el rendimiento de un conjunto personalizado de criptomonedas. La aplicación proporciona precios actualizados, cambios porcentuales y volumen del mercado en tiempo real, eliminando la necesidad de usar hojas de cálculo y sitios web dispersos.

----

## Características Principales

Interfaz SPA: Navegación dinámica sin recargas de página

Tiempo Real: Actualización automática cada 30 segundos

Responsive Design: Compatible con todos los dispositivos

Selección Personalizada: Buscar y agregar criptomonedas a seguimiento

Gráficos Interactivos: Visualización de precios con Chart.js

Persistencia de Datos: Almacenamiento en sesión del servidor

----

## Tecnologías Utilizadas

Backend
PHP 7.4+ con CodeIgniter 4

API CoinMarketCap (Plan Gratuito)

Frontend
HTML5, CSS3, JavaScript Vanilla

Bootstrap 5 para diseño responsive

Chart.js para visualización de gráficos

Font Awesome para iconografía

--------------------

## Instalación y Configuración
Prerrequisitos
PHP 7.4 o superior

Composer

API Key de CoinMarketCap


-------------

## Pasos de Instalación
Clonar el repositorio

git clone https://github.com/FernandoAlarcon/CryptoApp.git
cd CryptoApp

Instalar dependencias
composer install

Configurar variables de entorno
cp env .env


Editar el archivo .env y agregar:

app.baseURL = 'http://localhost:8080/'
COINMARKETCAP_API_KEY = "tu-api-key-de-coinmarketcap"


Obtener API Key de CoinMarketCap

Registrarse en CoinMarketCap API

Seleccionar plan "Basic" (gratuito)

Copiar la API Key al archivo .env

--

## Ejecutar la aplicación

php spark serve


Acceder a la aplicación

Abrir en el navegador: http://localhost:8080

----

## Uso de la Aplicación

Funcionalidades

- Dashboard Principal

- Visualización de criptomonedas en seguimiento

- Gráfico comparativo de precios

- Resumen de estadísticas en tiempo real

- Búsqueda y Selección

- Modal de búsqueda con autocompletado

- Resultados en tiempo real

- Agregar/eliminar criptomonedas con un click

- Datos en Tiempo Real

- Precios actualizados

- Cambios porcentuales (24h)

- Volumen de mercado

- Capitalización de mercado

-----------------------

## Estructura del Proyecto

CryptoApp/
├── app/
│   ├── Controllers/
│   │   ├── CryptoController.php
│   │   └── Dashboard.php
│   ├── Libraries/
│   │   └── CoinMarketCapAPI.php
│   └── Views/
│       └── dashboard.php
├── public/
│   ├── assets/
│   │   └── js/
│   │       └── app.js
│   └── index.php
├── .env
└── composer.json

---------------------

## API Endpoints


GET /api/cryptos - Lista las top 20 criptomonedas

GET /api/tracked - Obtiene criptomonedas en seguimiento

POST /api/track - Agrega criptomoneda a seguimiento

DELETE /api/untrack/:id - Elimina criptomoneda de seguimiento
 

## CoinMarketCap API

/v1/cryptocurrency/listings/latest - Precios actuales

/v1/cryptocurrency/quotes/latest - Datos específicos

------

## Requisitos Cumplidos

- Requisitos Funcionales

SPA con cambios dinámicos sin recarga

Seguimiento de criptomonedas personalizadas

Precios actualizados en tiempo real

Cambios porcentuales y volumen de mercado

Gráficos históricos e interactivos

Búsqueda y selección de criptomonedas

- Requisitos No Funcionales

Interfaz completamente responsive

Actualización automática de datos

Persistencia de datos en sesión

Compatibilidad multi-dispositivo

Tiempo real con monedas

-------------------------------------------

## Características de Diseño


Interfaz Moderna: Diseño oscuro con gradientes profesionales

Glassmorphism: Efectos visuales modernos

Animaciones Suaves: Transiciones y hover effects

Responsive: Adaptable a móviles, tablets y desktop

UX Mejorada: Estados de carga, notificaciones, empty states

---------------------------

## Flujo de Trabajo con Git

main (producción)
└── develop (desarrollo) 

---------------------------

## Pruebas Realizadas

✅ Adaptabilidad en diferentes resoluciones

✅ Actualización dinámica de datos

✅ Funcionamiento en tiempo real

✅ Responsive design

✅ Compatibilidad entre navegadores





Desarrollo
Desarrollador: Fernando Alarcón
Repositorio: https://github.com/FernandoAlarcon/CryptoApp
Tecnología: PHP CodeIgniter 4 + JavaScript Vanilla





