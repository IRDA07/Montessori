<?php
include_once 'config/database.php';

$slug = $_GET['slug'] ?? '';

$post = null;
if (!empty($slug)) {
    $stmt = $conn->prepare("SELECT p.titulo, p.contenido, p.fecha_publicacion, a.nombre AS autor_nombre, p.imagen_destacada
                            FROM posts p
                            JOIN autores a ON p.autor_id = a.id
                            WHERE p.slug = ? AND p.activo = 1");
    
    if ($stmt === false) {
        die("Error al preparar la consulta: " . $conn->error);
    }

    $stmt->bind_param("s", $slug);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $post = $result->fetch_assoc();
    }
    $stmt->close();
}

$conn->close();

if (!$post) {
    header("Location: blog.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kinder Montessori | <?php echo htmlspecialchars($post['titulo']); ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
    <header class="main-header">
        <div class="header-content container">
            <div class="logo">
                <img src="assets/images/logo.png" alt="Logo Kinder Montessori">
                Kinder <span>Montessori</span>
            </div>
            <nav class="main-nav">
                <ul>
                    <li><a href="index.html">Inicio</a></li>
                    <li><a href="servicios.html">Servicios</a></li>
                    <li><a href="metodologia.html">Metodología</a></li>
                    <li><a href="nosotros.html">Nosotros</a></li>
                    <li><a href="contacto.html">Contacto</a></li>
                    <li><a href="blog.php">Blog</a></li>
                </ul>
            </nav>
            <button class="menu-toggle" aria-label="Toggle navigation">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </header>

    <section class="hero-page-header hero-blog-post">
        <div class="hero-content">
            <h1><?php echo htmlspecialchars($post['titulo']); ?></h1>
            <p class="post-meta-hero">
                <i class="fas fa-calendar-alt"></i> <?php echo date('d M Y', strtotime($post['fecha_publicacion'])); ?>
                <span class="separator">|</span>
                <i class="fas fa-user"></i> <?php echo htmlspecialchars($post['autor_nombre']); ?>
            </p>
        </div>
    </section>

    <section id="single-post-content" class="section-block bg-white-2">
        <div class="container post-detail-container">
            <?php if (!empty($posts)): ?>
                <?php foreach ($posts as $post): ?>
                    <div class="post-card">
                        <?php if (!empty($post['imagen_destacada'])): ?> 
                            <div class="post-img">
                                <img src="<?php echo ($post['imagen_destacada']); ?>" alt="<?php echo ($post['titulo']); ?>">
                            </div>
                        <?php endif; ?>
                        <div class="post-content">
                            <h3><?php echo htmlspecialchars($post['titulo']); ?></h3>
                            <p class="post-meta">
                                <i class="fas fa-calendar-alt"></i> <?php echo date('d M Y', strtotime($post['fecha_publicacion'])); ?>
                                <span class="separator">|</span>
                                <i class="fas fa-user"></i> <?php echo htmlspecialchars($post['autor_nombre']); ?>
                            </p>
                            <p><?php echo substr(strip_tags($post['contenido']), 0, 150); ?>...</p>
                            <a href="post.php?slug=<?php echo htmlspecialchars($post['slug']); ?>" class="btn btn-small">Leer más</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No hay publicaciones disponibles en este momento.</p>
            <?php endif; ?>
            <div class="post-detail-content">
                <?php echo $post['contenido']; ?>
            </div>
            <div class="back-to-blog">
                <a href="blog.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver al Blog</a>
            </div>
        </div>
    </section>

    <section class="cta-section">
        <div class="cta-content">
            <h2>¿Listo para que tu hijo viva la experiencia Montessori?</h2>
            <p>Agenda una visita o contáctanos para conocer más sobre nuestro proyecto educativo y cómo podemos acompañar a tu familia.</p>
            <a href="contacto.html" class="btn btn-cta-secondary">¡Inscríbete Hoy!</a>
        </div>
    </section>

    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>Kinder Montessori</h3>
                <p>Educación que inspira, crea y transforma.</p>
                <p>Desarrollando el potencial único de cada niño.</p>
                <div class="social-icons">
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                    <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>

            <div class="footer-section">
                <h3>Contacto</h3>
                <p><i class="fas fa-map-marker-alt"></i> Av. Educación 123, Col. Aprendizaje, Aguascalientes, Ags., México</p>
                <p><i class="fas fa-phone"></i> Tel: 555-123-4567</p>
                <p><i class="fas fa-envelope"></i> info@kindermontessori.edu</p>
            </div>

            <div class="footer-section">
                <h3>Horario</h3>
                <p><i class="far fa-clock"></i> Lunes a Viernes</p>
                <p>7:30 am - 6:30 pm</p>
                <p>Servicio extendido disponible</p>
                <a href="contacto.html#horarios">Ver horarios detallados</a>
            </div>
        </div>

        <div class="copyright">
            <p>&copy; 2023 Kinder Montessori. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="script.js"></script>
</body>
</html>