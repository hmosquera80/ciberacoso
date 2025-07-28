<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¡Denuncia Enviada! - Ciberacoso</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex; /* Para centrar el contenido verticalmente */
            justify-content: center; /* Para centrar el contenido horizontalmente */
            align-items: center; /* Para centrar el contenido verticalmente */
        }
        .form-container { /* Renombrado de .container a .form-container para reusar el estilo */
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            margin: 20px auto;
            max-width: 800px;
            overflow: hidden;
            text-align: center; /* Centrar texto dentro del contenedor */
        }
        .form-header {
            background: linear-gradient(45deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .form-body {
            padding: 40px;
        }
        .section-title { /* Si se usa en la página de éxito, aunque no es común */
            color: #667eea;
            border-bottom: 2px solid #f093fb;
            padding-bottom: 10px;
            margin-bottom: 25px;
            font-size: 1.3em;
            font-weight: 600;
        }
        .btn-submit { /* Reutilizamos el estilo del botón de envío */
            background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 15px 40px;
            border-radius: 25px;
            color: white;
            font-weight: 600;
            transition: transform 0.3s ease;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            color: white;
        }
        .alert-success-custom { /* Un estilo más específico para el mensaje de éxito */
            background: linear-gradient(45deg, #28a74520, #21883820);
            border: 1px solid #28a74550;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }
        h1.success-title {
            color: #ffffffff;
            font-size: 2.5em;
            margin-bottom: 20px;
        }
        p {
            font-size: 1.1em;
            line-height: 1.6;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <div class="form-header">
                <h1 class="success-title">🎉 ¡Gracias por tu valentía!🎉</h1> <h3>"Tu voz es importante para nosotros"</h3>
            </div>
            
            <div class="form-body">
                @if (session('success'))
                    <div class="alert alert-success alert-success-custom">
                        <p class="mb-0">{{ session('success') }}</p>
                    </div>
                @endif
                
                <p>Tu denuncia ha sido enviada con éxito.<p> 
                <p>Valoramos mucho que nos hayas contado lo que te pasa.</p>
                <p>Un equipo especializado revisará tu caso con la mayor confidencialidad posible.</p>
                <p>Recuerda que no estás solo/a. Estamos aquí para ayudarte.</p>
                
                <div class="text-center mt-4">
                    <a href="{{ route('report.create') }}" class="btn btn-submit btn-lg">
                    Hacer otra denuncia
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>