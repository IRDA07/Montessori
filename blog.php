<?php
include_once 'config/database.php';

function obtenerPosts($conn_obj) {
    $sql = "SELECT p.id, p.titulo, p.contenido, p.fecha_publicacion, a.nombre AS autor_nombre
            FROM posts p
            JOIN autores a ON p.autor_id = a.id
            WHERE p.activo = 1
            ORDER BY p.fecha_publicacion DESC";
    $result = $conn_obj->query($sql);

    $posts = [];
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $posts[] = $row;
        }
    }
    return $posts;
}

$posts = obtenerPosts($conn);

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kinder Montessori | Blog</title>
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
                    <li><a href="blog.php">Blog</a></li>
                    <li><a href="contacto.html">Contacto</a></li>
                </ul>
            </nav>
            <button class="menu-toggle" aria-label="Toggle navigation">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </header>

    <section class="hero-page-header hero-blog">
        <div class="hero-content">
            <h1>Nuestro Blog: Artículos y Noticias</h1>
            <p>Mantente informado sobre nuestra metodología, actividades y consejos para padres.</p>
        </div>
    </section>

    <section id="blog-posts" class="section-block bg-white-2">
        <div class="container">
            <h2 class="section-title">Últimas Publicaciones</h2>
            <div class="posts-grid">
                <?php if (!empty($posts)): ?>
                    <?php foreach ($posts as $post): ?>
                        <div class="post-card">
                            <?php if (!empty($post['imagen_destacada'])): ?>
                                <div class="post-img">
                                    <img src="<?php echo htmlspecialchars($post['imagen_destacada']); ?>" alt="<?php echo htmlspecialchars($post['titulo']); ?>">
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