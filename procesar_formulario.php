<?php
include_once 'config/database.php';

$para_email = 'info@kindermontessori.edu';
$asunto_email = 'Nuevo Mensaje desde la Web Kinder Montessori';
$headers = "From: web@kindermontessori.edu\r\n";
$headers .= "Reply-To: web@kindermontessori.edu\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: contacto.html?status=error_method");
    exit();
}

$nombre = htmlspecialchars(trim($_POST['name'] ?? ''));
$email = htmlspecialchars(trim($_POST['email'] ?? ''));
$telefono = htmlspecialchars(trim($_POST['phone'] ?? ''));
$asunto_form = htmlspecialchars(trim($_POST['subject'] ?? ''));
$mensaje = htmlspecialchars(trim($_POST['message'] ?? ''));

$errors = [];

if (empty($nombre)) {
    $errors[] = "El nombre es obligatorio.";
}
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "El correo electrónico es inválido o está vacío.";
}
if (empty($asunto_form)) {
    $errors[] = "El asunto es obligatorio.";
}
if (empty($mensaje)) {
    $errors[] = "El mensaje es obligatorio.";
}

$recaptcha_response = $_POST['g-recaptcha-response'] ?? '';
$recaptcha_secret_key = 'YOUR_RECAPTCHA_SECRET_KEY';
$captcha_verified = true;

if (!empty($recaptcha_response) && $recaptcha_secret_key !== 'YOUR_RECAPTcha_SECRET_KEY') {
    $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
    $recaptcha_data = [
        'secret' => $recaptcha_secret_key,
        'response' => $recaptcha_response
    ];

    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($recaptcha_data)
        ]
    ];
    $context  = stream_context_create($options);
    $verify = file_get_contents($recaptcha_url, false, $context);
    $captcha_response = json_decode($verify);

    if ($captcha_response->success === false) {
        $errors[] = "Verificación CAPTCHA fallida. Por favor, inténtalo de nuevo.";
        $captcha_verified = false;
    }
} else if (empty($recaptcha_response) && $recaptcha_secret_key !== 'YOUR_RECAPTCHA_SECRET_KEY') {
    $errors[] = "Por favor, completa la verificación CAPTCHA.";
    $captcha_verified = false;
}

if (!empty($errors) || !$captcha_verified) {
    $error_message = urlencode(implode("<br>", $errors));
    header("Location: contacto.html?status=error&message=" . $error_message);
    exit();
}

$insert_success = false;
$stmt = $conn->prepare("INSERT INTO mensajes_contacto (nombre, email, telefono, asunto, mensaje) VALUES (?, ?, ?, ?, ?)");

if ($stmt === false) {
    error_log("Error al preparar la consulta de inserción de mensaje: " . $conn->error);
    $errors[] = "Error interno del servidor al guardar el mensaje.";
} else {
    $stmt->bind_param("sssss", $nombre, $email, $telefono, $asunto_form, $mensaje);
    if ($stmt->execute()) {
        $insert_success = true;
    } else {
        error_log("Error al ejecutar la inserción de mensaje: " . $stmt->error);
        $errors[] = "Hubo un problema al guardar tu mensaje en la base de datos.";
    }
    $stmt->close();
}

if (!$insert_success) {
    $error_message = urlencode(implode("<br>", $errors));
    header("Location: contacto.html?status=error&message=" . $error_message);
    $conn->close();
    exit();
}

$email_body = "
    <html>
    <head>
        <title>Nuevo Mensaje desde Kinder Montessori</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; background-color: #f9f9f9; }
            h2 { color: #413674; }
            p { margin-bottom: 10px; }
            strong { color: #F4BE18; }
        </style>
    </head>
    <body>
        <div class='container'>
            <h2>Mensaje de Contacto del Sitio Web</h2>
            <p><strong>Nombre:</strong> " . $nombre . "</p>
            <p><strong>Correo Electrónico:</strong> " . $email . "</p>
            <p><strong>Teléfono:</strong> " . ($telefono ?: 'No proporcionado') . "</p>
            <p><strong>Asunto:</strong> " . $asunto_form . "</p>
            <p><strong>Mensaje:</strong><br>" . nl2br($mensaje) . "</p>
            <hr>
            <p style='font-size: 0.8em; color: #777;'>Mensaje enviado desde el formulario de contacto de Kinder Montessori.</p>
        </div>
    </body>
    </html>
";

// $email_sent = false;
// if (mail($para_email, $asunto_email, $email_body, $headers)) {
//     $email_sent = true;
// } else {
//     error_log("Fallo al enviar el correo a " . $para_email . " desde el formulario. IP: " . $_SERVER['REMOTE_ADDR']);
// }

$conn->close();

header("Location: contacto.html?status=success");
exit();