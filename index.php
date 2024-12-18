<?php
ini_set('session.gc_maxlifetime', 365 * 24 * 60 * 60);

session_set_cookie_params([
    'lifetime' => 365 * 24 * 60 * 60, // 1 año en segundos
    'path' => '/',
    'domain' => '', // Puedes especificar tu dominio si es necesario
    'secure' => true, // True si usas HTTPS
    'httponly' => true,
    'samesite' => 'Lax' // Puedes usar 'Strict' o 'None' dependiendo de tus necesidades
]);

session_start(); 

// Definir la contraseña codificada
$contraseña_correcta = 'SET_YOUR_PASSWORD';

// Manejar el envío del formulario de inicio de sesión
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    $password_ingresada = $_POST['password'];

    if ($password_ingresada === $contraseña_correcta) {
        // Iniciar sesión
        $_SESSION['logged_in'] = true;
        header('Location: index.php');
        exit();
    } else {
        $error = 'Contraseña incorrecta. Inténtalo de nuevo.';
    }
}

// Si el usuario no está autenticado, mostrar el formulario de inicio de sesión
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    
?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Iniciar Sesión - Tesla Control</title>
        <!-- Enlace a Google Fonts para usar 'Roboto' -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap">
        <!-- Estilos básicos para el formulario -->
        <style>
            body {
                font-family: 'Roboto', Arial, sans-serif;
                background-color: #727273ad;
                color: #f5f5f5;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
            }

            .login-container {
                background-color: #2c2c2c;
                padding: 40px;
                border-radius: 10px;
                text-align: center;
            }

            .login-container h1 {
                margin-bottom: 20px;
            }

            .login-container input[type="password"] {
                padding: 10px;
                width: 100%;
                margin-bottom: 20px;
                border: none;
                border-radius: 5px;
                font-size: 16px;
            }

            .login-container input[type="submit"] {
                padding: 10px 20px;
                background-color: #3e6ae4;
                color: #ffffff;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                font-size: 16px;
            }

            .error {
                color: #ff4d4d;
                margin-bottom: 20px;
            }
        </style>
    </head>
    <body>
        <div class="login-container">
            <h1>Iniciar Sesión</h1>
            <?php if (isset($error)) { echo '<div class="error">'.$error.'</div>'; } ?>
            <form method="post" action="">
                <input type="password" name="password" placeholder="Password" required>
                <input type="submit" value="Login">
            </form>
        </div>
    </body>
    </html>
<?php
    exit();
    }
    // Si el usuario está autenticado, mostrar el contenido original
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tesla Control</title>
    <!-- Enlace a la librería de iconos Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Enlace a Google Fonts para usar 'Montserrat' y 'Roboto' -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat:400,700|Roboto:400,700&display=swap">
    <style>
        /* Estilos generales */
        body {
            margin: 0;
            font-family: 'Roboto', Arial, sans-serif;
            /*background-color: #7b7a7d;*/
            background-color: #727273ad;
            color: #000000;
            overflow: hidden;
        }

        .container {
            position: relative;
            width: calc(100% - 150px); /* Restamos 150px para el padding lateral */
            height: 100vh;
            margin: 0 auto; /* Centramos el contenedor */
            padding: 0 20px; /* Padding lateral de 20px */
        }

        /* Estilos para el panel izquierdo (información de clima) */
        .left-panel {
            position: absolute;
            left: 0; /* Ajustado debido al padding */
            top: 50%;
            transform: translateY(-50%);
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .weather-info {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .weather-info i {
            margin-right: 10px;
            font-size: 28px;
            /*color: #747376;*/
            color: #f5f5f5;
            width: 37px;
            text-align: center;
        }

        .weather-text {
            display: flex;
            flex-direction: column;
        }

        .weather-title {
            font-size: 18px; /* Títulos más pequeños */
            font-weight: bold;
            /*color: #747376;*/ /* Color gris */
            color: #f5f5f5;
            margin-bottom: 5px;
        }

        .weather-value {
            font-size: 22px; /* Tamaño del valor */
            /*color: #959698;*/
            color: #f5f5f5;
        }

        /* Estilos para el panel central (imagen del coche) */
        .center-panel {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        .car-title {
            font-family: 'Montserrat', 'Roboto', Arial, sans-serif;
            font-size: 48px;
            font-weight: bold;
            margin-bottom: 20px;
            /*color: #000000;*/
            color: #ffffff;
        }

        .car-image {
            width: 1237px;
            height: auto;
        }

        /* Estilos para el panel derecho (botones) */
        .button-container {
            position: absolute;
            right: 0; /* Ajustado debido al padding */
            top: 50%;
            transform: translateY(-50%);
            display: flex;
            flex-direction: column;
            z-index: 100;
        }

        .button {
            background-color: #d5d5d7; /* Fondo sin pulsar */
            color: #747376; /* Texto sin pulsar */
            border: none;
            padding: 15px 20px;
            margin-bottom: 15px;
            text-align: left;
            font-size: 18px;
            border-radius: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: background-color 0.3s, color 0.3s;
            position: relative;
            overflow: hidden;
        }

        .button:hover .button-icon,
        .button.loading .button-icon {
            background-color: #ffffff; /* El círculo cambia a blanco */
        }

        .button:hover .button-icon i,
        .button.loading .button-icon i {
            color: #747376; /* El icono mantiene su color */
        }

        .button:hover,
        .button.loading {
            background-color: #3e6ae4; /* Fondo al pulsar */
            color: #ffffff; /* Texto al pulsar */
        }

        .button-content {
            display: flex;
            align-items: center;
        }

        .button-icon {
            width: 40px;
            height: 40px;
            background-color: #d5d5d7; /* Mismo fondo que el botón sin pulsar */
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            transition: background-color 0.3s;
        }

        .button-icon i {
            font-size: 20px;
            color: #747376; /* Color del icono sin pulsar */
            transition: color 0.3s;
        }

        /* Indicador de carga */
        .loading-indicator {
            margin-left: 10px;
            display: none;
            align-items: center;
        }

        .button.loading .loading-indicator {
            display: flex;
        }

        .loading-indicator .dot {
            width: 6px;
            height: 6px;
            background-color: #ffffff; /* Puntos blancos */
            border-radius: 50%;
            margin-left: 2px;
            animation: loading 1s infinite;
            opacity: 0.2;
        }

        .loading-indicator .dot:nth-child(1) {
            animation-delay: 0s;
        }

        .loading-indicator .dot:nth-child(2) {
            animation-delay: 0.2s;
        }

        .loading-indicator .dot:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes loading {
            0%, 100% { opacity: 0.2; }
            20% { opacity: 1; }
        }

        /* Estilos para la barrera */
        .barrera .static {
            position: absolute;
            bottom: -16px;
            z-index: 4;
            width: 100%;
        }

        .barrera .dynamic {
            position: absolute;
            bottom: -16px;
            z-index: 3;
            width: 56%;
            left: 234px; /* Posición cerrada inicial */
            transition: left 30s linear; /* Animación de movimiento */
        }
        /* Clases para la animación */
        .barrera-abierta {
            left: -351px !important; /* Posición abierta */
        }

        .barrera-animar {
            transition: left 30s linear;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Panel izquierdo: Información del clima -->
        <div class="left-panel">
            <div class="weather-info">
                <i class="fas fa-thermometer-half"></i>
                <div class="weather-text">
                    <div class="weather-title">Temperatura exterior</div>
                    <div class="weather-value" id="temp-exterior">--°C</div>
                </div>
            </div>
            <div class="weather-info">
                <i class="fas fa-home"></i>
                <div class="weather-text">
                    <div class="weather-title">Temperatura interior</div>
                    <div class="weather-value" id="temp-interior">--°C</div>
                </div>
            </div>
            <div class="weather-info">
                <i class="fas fa-wind"></i>
                <div class="weather-text">
                    <div class="weather-title">Viento</div>
                    <div class="weather-value" id="viento">-- km/h</div>
                </div>
            </div>
        </div>

        <!-- Panel central: Título y imagen del coche -->
        <div class="center-panel">
            <h1 class="car-title">Model Y - House Dashboard</h1>
            <img src="tesla-garage.png" alt="Tesla en el garaje" class="car-image">
        </div>

        <!-- Panel derecho: Botones -->
        <div class="button-container">
            <button class="button" onclick="handleButtonClick(this, 'salir')">
                <span class="button-content">
                    <span class="button-icon">
                        <i class="fas fa-sign-out-alt"></i>
                    </span>
                    Salgo de casa
                </span>
                <span class="loading-indicator">
                    <span class="dot"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                </span>
            </button>
            <button class="button" onclick="handleButtonClick(this, 'llegar')">
                <span class="button-content">
                    <span class="button-icon">
                        <i class="fas fa-sign-in-alt"></i>
                    </span>
                    Llego a casa
                </span>
                <span class="loading-indicator">
                    <span class="dot"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                </span>
            </button>
            <button class="button" onclick="handleButtonClick(this, 'abrir-entrada')">
                <span class="button-content">
                    <span class="button-icon">
                        <i class="fas fa-door-open"></i>
                    </span>
                    Abre entrada
                </span>
                <span class="loading-indicator">
                    <span class="dot"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                </span>
            </button>
            <button class="button" onclick="handleButtonClick(this, 'abrir-garaje')">
                <span class="button-content">
                    <span class="button-icon">
                        <i class="fas fa-warehouse"></i>
                    </span>
                    Abre garaje
                </span>
                <span class="loading-indicator">
                    <span class="dot"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                </span>
            </button>
            <!-- Botón "He llegado" -->
            <button class="button" onclick="handleButtonClick(this, 'cierra-todo')">
                <span class="button-content">
                    <span class="button-icon">
                        <i class="fas fa-check-circle"></i>
                    </span>
                    Cierra todo
                </span>
                <span class="loading-indicator">
                    <span class="dot"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                </span>
            </button>
        </div>
    </div>

    <div class="barrera">
        <img class="static" src="https://private.gosmart.pt:2053/tesla/img/barrera_estatica_dark.webp">
        <img class="dynamic" src="https://private.gosmart.pt:2053/tesla/img/barrera_dark.webp">
    </div>

    <!-- Scripts para obtener datos del clima -->
    <script>
        // Aquí puedes usar una API pública para obtener los datos meteorológicos
        // Por ejemplo, OpenWeatherMap API
        const apiKey = 'e5915b1f440c310ad7939efe613dba1a';
        const city = 'Palmela,PT';

        fetch(`https://api.openweathermap.org/data/2.5/weather?q=${city}&units=metric&lang=es&appid=${apiKey}`)
            .then(response => response.json())
            .then(data => {

                const tempExterior = data.main.temp;
                const viento = data.wind.speed;


                document.getElementById('temp-exterior').textContent = tempExterior.toFixed(1) + '°C';
                document.getElementById('viento').textContent = viento + ' km/h';
            })
            .catch(error => {
                console.error('Error al obtener los datos meteorológicos:', error);
            });

        // Simulación de temperatura interior
        const tempInterior = 22; // Puedes reemplazar este valor con datos reales si los tienes
        document.getElementById('temp-interior').textContent = tempInterior + '°C';

        // Variable para controlar el estado de la barrera
        let barreraAbierta = false;
        let barreraTimeout;

        // Función para manejar el clic en los botones
        function handleButtonClick(button, action) {
            // Añadir clase de carga
            button.classList.add('loading');

            // Enviar solicitud a integration.php
            fetch('integration.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ action: action })
            })
            .then(response => response.json())
            .then(data => {
                // Remover la clase de carga
                button.classList.remove('loading');

                if (data.success) {
                    // Acción completada exitosamente
                    console.log(`Acción '${action}' ejecutada con éxito.`);

                    // Si la acción requiere abrir la barrera
                    if (action === 'salir' || action === 'llegar' || action === 'abrir-entrada') {
                        abrirBarrera();
                    }

                    // Cerrar barrera
                    if (action === 'cierra-todo') {
                        cerrarBarrera();
                    }

                } else {
                    // Hubo un error al ejecutar la acción
                    console.error(`Error al ejecutar la acción '${action}':`, data.message);
                    alert(`Error: ${data.message}`);
                }
            })
            .catch(error => {
                console.error('Error al comunicarse con integration.php:', error);
                button.classList.remove('loading');
                alert('Ocurrió un error al procesar la solicitud.');
            });
        }

        function abrirBarrera() {
            if (!barreraAbierta) {
                barreraAbierta = true;
                const barrera = document.querySelector('.barrera .dynamic');

                // Añadir clase para animar
                barrera.classList.add('barrera-animar');
                barrera.offsetHeight; // Forzar reflow
                barrera.classList.add('barrera-abierta');

                // Programar el cierre automático después de 2 minutos (120,000 ms)
                if (barreraTimeout) {
                    clearTimeout(barreraTimeout);
                }
                barreraTimeout = setTimeout(cerrarBarrera, 120000);
            }
        }

        function cerrarBarrera() {
            if (barreraAbierta) {
                const barrera = document.querySelector('.barrera .dynamic');

                // Añadir clase para animar
                barrera.classList.add('barrera-animar');
                barrera.classList.remove('barrera-abierta');

                // Esperar a que termine la animación antes de cambiar el estado
                setTimeout(() => {
                    barreraAbierta = false;
                }, 30000); // Debe coincidir con la duración de la animación (30s)
            }
        }
    </script>
</body>
</html>
