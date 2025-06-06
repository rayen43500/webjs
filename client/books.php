<!-- Dans <head> -->
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Protection de la page
if (!isLoggedIn()) {
    redirect('../login.php');
}

$userId = $_SESSION['user_id'];
$userName = $_SESSION['user_name'];

// Récupérer tous les livres
$books = getAllBooks();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bibliothèque - Plateforme de Bien-être Mental et Culturel</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="dashboard">
        <aside class="w-64 bg-blue-500 shadow-md text-white">
            <div class="p-6 border-b">
                <h2 class="text-2xl font-bold">Menu</h2>
            </div>
            <nav class="p-4 space-y-2">
                <a href="dashboard.php" class="block py-2 px-4 rounded hover:bg-blue-600 text-white">
                    <i class="fas fa-home mr-2 text-white"></i> Accueil
                </a>
                <a href="ai-chat.php" class="block py-2 px-4 rounded hover:bg-blue-600 text-white">
                    <i class="fas fa-robot mr-2 text-white"></i> Consultation IA
                </a>
                 <a href="notes.php" class="block py-2 px-4 rounded hover:bg-blue-600 text-white">
                    <i class="fas fa-book mr-2 text-white"></i> Bloc-notes
                </a>
                <a href="books.php" class="block py-2 px-4 rounded hover:bg-blue-600 text-white">
                    <i class="fas fa-book mr-2 text-white"></i> Bibliothèque
                </a>
                <a href="music.php" class="block py-2 px-4 rounded hover:bg-blue-600 text-white">
                    <i class="fas fa-music mr-2 text-white"></i> Espace Musique
                </a>
                <a href="profile.php" class="block py-2 px-4 rounded hover:bg-blue-600 text-white">
                    <i class="fas fa-user mr-2 text-white"></i> Mon profil
                </a>
                <a href="../logout.php" class="block py-2 px-4 rounded text-red-600 hover:bg-red-100">
                    <i class="fas fa-sign-out-alt mr-2"></i><b> Déconnexion</b>
                </a>
            </nav>
        </aside>
       
        <div class="content">
            <div class="page-header">
                <h1>Bibliothèque numérique</h1>
                
                <div class="search-form">
                    <form id="searchForm" action="" method="GET">
                        <input type="text" name="search" placeholder="Rechercher un livre..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        <button type="submit" class="btn primary">Rechercher</button>
                    </form>
                </div>
            </div>
            
            <div class="library-container">
                <?php if (empty($books)): ?>
                <div class="empty-state">
                    <h2>Aucun livre disponible</h2>
                    <p>Notre bibliothèque est en cours de construction. Revenez bientôt pour découvrir notre collection de livres!</p>
                </div>
                <?php else: ?>
                <div class="book-grid">
                    <?php 
                    // Filtrer les livres si une recherche est effectuée
                    $searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
                    $filteredBooks = $books;
                    
                    if (!empty($searchQuery)) {
                        $filteredBooks = array_filter($books, function($book) use ($searchQuery) {
                            return (stripos($book['title'], $searchQuery) !== false) || 
                                   (stripos($book['author'], $searchQuery) !== false) ||
                                   (stripos($book['description'], $searchQuery) !== false);
                        });
                    }
                    
                    if (empty($filteredBooks)): ?>
                    <div class="empty-state">
                        <h2>Aucun résultat trouvé</h2>
                        <p>Aucun livre ne correspond à votre recherche "<?php echo htmlspecialchars($searchQuery); ?>". Essayez avec d'autres termes.</p>
                        <a href="books.php" class="btn secondary">Voir tous les livres</a>
                    </div>
                    <?php else: ?>
                    
                    <?php foreach ($filteredBooks as $book): ?>
                    <div class="book-card">
                        <div class="book-cover">
                            <?php if (!empty($book['cover_path'])): ?>
                            <img src="<?php echo htmlspecialchars($book['cover_path']); ?>" alt="Couverture de <?php echo htmlspecialchars($book['title']); ?>">
                            <?php else: ?>
                            <div class="book-cover-placeholder">
                                <span><?php echo htmlspecialchars(substr($book['title'], 0, 1)); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="book-info">
                            <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                            <p class="book-author"><?php echo htmlspecialchars($book['author']); ?></p>
                            <?php if (!empty($book['description'])): ?>
                            <p class="book-description"><?php echo nl2br(htmlspecialchars(substr($book['description'], 0, 100))); ?><?php echo (strlen($book['description']) > 100 ? '...' : ''); ?></p>
                            <?php endif; ?>
                            <div class="book-actions">
                                <a href="<?php echo htmlspecialchars($book['file_path']); ?>" class="btn primary" target="_blank">Lire</a>
                                <a href="<?php echo htmlspecialchars($book['file_path']); ?>" class="btn secondary" download>Télécharger</a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script src="../assets/js/main.js"></script>
</body>
</html>
