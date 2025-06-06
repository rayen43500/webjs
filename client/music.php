<!-- Dans <head> -->
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Musique - Plateforme de Bien-être Mental et Culturel</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
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

        <!-- Contenu principal -->
        <main class="flex-1 p-10">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold">Espace Musique</h1>
                <form id="searchForm" action="" method="GET" class="flex">
                    <input type="text" name="search" placeholder="Rechercher une musique..."
                           value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
                           class="px-4 py-2 border rounded-l border-gray-300 w-full">
                           <button type="submit" class="bg-blue-500 text-white p-3 rounded-full hover:bg-blue-600">
                               <i class="fas fa-search"></i>
                           </button>

                </form>
            </div>

            <!-- Lecteur audio -->
            <div class="bg-white p-6 rounded shadow mb-6 " >
                <div id="nowPlaying" class="mb-2 text-gray-900 font-semibold">Sélectionnez une musique pour commencer</div>
                <audio id="audioPlayer" controls class="w-full"></audio>
            </div>

            <!-- Liste des musiques -->
            <div class="bg-white p-6 rounded shadow mb-6">
                <?php if (empty($music)): ?>
                    <div class="text-gray-600">
                        <h2 class="text-xl font-semibold mb-2">Aucune musique disponible</h2>
                    </div>
                <?php else: ?>
                    <?php
                    $searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
                    $filteredMusic = $music;

                    if (!empty($searchQuery)) {
                        $filteredMusic = array_filter($music, function($track) use ($searchQuery) {
                            return (stripos($track['title'], $searchQuery) !== false) || 
                                   (stripos($track['artist'], $searchQuery) !== false);
                        });
                    }
                    ?>

                    <?php if (empty($filteredMusic)): ?>
                        <div class="text-gray-600">
                            <p>Aucune musique ne correspond à "<?php echo htmlspecialchars($searchQuery); ?>".</p>
                            <a href="music.php" class="text-blue-500 hover:underline">Voir toutes les musiques</a>
                        </div>
                    <?php else: ?>
                        <table class="w-full text-left table-auto">
                            <thead>
                                <tr class="border-t hover:bg-gray-50 transition-colors duration-100">
                                    <th class="p-2">Titre</th>
                                    <th class="p-2">Artiste</th>
                                    <th class="p-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($filteredMusic as $track): ?>
                                <tr class="border-t">
                                    <td class="p-2"><?php echo htmlspecialchars($track['title']); ?></td>
                                    <td class="p-2"><?php echo htmlspecialchars($track['artist']); ?></td>
                                    <td class="p-2">
                                        <a href="#" class="text-blue-500 hover:underline music-item"
                                           data-src="<?php echo htmlspecialchars($track['file_path']); ?>"
                                           data-title="<?php echo htmlspecialchars($track['title']); ?>"
                                           data-artist="<?php echo htmlspecialchars($track['artist']); ?>">
                                           Écouter
                                        </a> |
                                        <a href="<?php echo htmlspecialchars($track['file_path']); ?>" download
                                           class="text-green-600 hover:underline">Télécharger</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <!-- Bien-être musical -->
            <div class="bg-white p-6 rounded shadow">
                <h2 class="text-xl font-semibold mb-4">Musique et bien-être mental</h2>
                <ul class="list-disc list-inside text-gray-700 space-y-2">
                    <li><strong>Réduction du stress</strong> : Diminue le cortisol.</li>
                    <li><strong>Amélioration de l'humeur</strong> : Libère de la dopamine.</li>
                    <li><strong>Concentration</strong> : Favorise la productivité.</li>
                    <li><strong>Sommeil</strong> : Facilite l’endormissement.</li>
                </ul>
                <p class="mt-4 text-gray-600">Prenez le temps d’écouter ce qui vous apaise.</p>
            </div>
        </main>
    </div>

    <script>
        // JS simple pour changer la musique
        document.querySelectorAll('.music-item').forEach(item => {
            item.addEventListener('click', e => {
                e.preventDefault();
                const audio = document.getElementById('audioPlayer');
                const nowPlaying = document.getElementById('nowPlaying');
                audio.src = item.dataset.src;
                nowPlaying.textContent = `🎵 ${item.dataset.title} - ${item.dataset.artist}`;
                audio.play();
            });
        });
    </script>
</body>
</html>

