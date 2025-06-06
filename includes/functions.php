<?php
require_once __DIR__ . '/config.php';

// Inscription d'un utilisateur
function registerUser($name, $email, $password) {
    global $pdo;
    
    // Vérifier si l'email existe déjà
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() > 0) {
        return [
            'success' => false,
            'message' => "Cet email est déjà utilisé."
        ];
    }
    
    // Hachage du mot de passe
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    
    // Insertion de l'utilisateur
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    
    try {
        $stmt->execute([$name, $email, $hashedPassword]);
        return [
            'success' => true,
            'message' => "Inscription réussie. Vous pouvez maintenant vous connecter.",
            'user_id' => $pdo->lastInsertId()
        ];
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => "Erreur lors de l'inscription: " . $e->getMessage()
        ];
    }
}

// Connexion d'un utilisateur
function loginUser($email, $password) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT id, name, email, password, is_admin FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() == 0) {
        return [
            'success' => false,
            'message' => "Email ou mot de passe incorrect."
        ];
    }
    
    $user = $stmt->fetch();
    
    if (password_verify($password, $user['password'])) {
        // Créer la session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['is_admin'] = $user['is_admin'];
        
        return [
            'success' => true,
            'message' => "Connexion réussie.",
            'user' => $user
        ];
    } else {
        return [
            'success' => false,
            'message' => "Email ou mot de passe incorrect."
        ];
    }
}

// Déconnexion d'un utilisateur
function logoutUser() {
    // Détruire toutes les variables de session
    $_SESSION = [];
    
    // Détruire la session
    session_destroy();
    
    return [
        'success' => true,
        'message' => "Déconnexion réussie."
    ];
}

// Fonctions pour les notes
function saveNote($userId, $title, $content) {
    global $pdo;
    
    $stmt = $pdo->prepare("INSERT INTO notes (user_id, title, content) VALUES (?, ?, ?)");
    
    try {
        $stmt->execute([$userId, $title, $content]);
        return [
            'success' => true,
            'message' => "Note enregistrée avec succès.",
            'note_id' => $pdo->lastInsertId()
        ];
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => "Erreur lors de l'enregistrement de la note: " . $e->getMessage()
        ];
    }
}

function updateNote($noteId, $userId, $title, $content) {
    global $pdo;
    
    $stmt = $pdo->prepare("UPDATE notes SET title = ?, content = ? WHERE id = ? AND user_id = ?");
    
    try {
        $stmt->execute([$title, $content, $noteId, $userId]);
        
        if ($stmt->rowCount() > 0) {
            return [
                'success' => true,
                'message' => "Note mise à jour avec succès."
            ];
        } else {
            return [
                'success' => false,
                'message' => "Impossible de mettre à jour la note. Note non trouvée ou vous n'êtes pas autorisé à la modifier."
            ];
        }
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => "Erreur lors de la mise à jour de la note: " . $e->getMessage()
        ];
    }
}

function deleteNote($noteId, $userId) {
    global $pdo;
    
    $stmt = $pdo->prepare("DELETE FROM notes WHERE id = ? AND user_id = ?");
    
    try {
        $stmt->execute([$noteId, $userId]);
        
        if ($stmt->rowCount() > 0) {
            return [
                'success' => true,
                'message' => "Note supprimée avec succès."
            ];
        } else {
            return [
                'success' => false,
                'message' => "Impossible de supprimer la note. Note non trouvée ou vous n'êtes pas autorisé à la supprimer."
            ];
        }
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => "Erreur lors de la suppression de la note: " . $e->getMessage()
        ];
    }
}

function getNoteById($noteId, $userId) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM notes WHERE id = ? AND user_id = ?");
    $stmt->execute([$noteId, $userId]);
    
    return $stmt->fetch();
}

function getUserNotes($userId) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM notes WHERE user_id = ? ORDER BY updated_at DESC");
    $stmt->execute([$userId]);
    
    return $stmt->fetchAll();
}

// Fonctions pour les conversations IA
function createAiConversation($userId) {
    global $pdo;
    
    $stmt = $pdo->prepare("INSERT INTO ai_conversations (user_id) VALUES (?)");
    
    try {
        $stmt->execute([$userId]);
        return [
            'success' => true,
            'conversation_id' => $pdo->lastInsertId()
        ];
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => "Erreur lors de la création de la conversation: " . $e->getMessage()
        ];
    }
}

function saveAiMessage($conversationId, $message, $isUser = true) {
    global $pdo;
    
    $stmt = $pdo->prepare("INSERT INTO ai_messages (conversation_id, is_user, message) VALUES (?, ?, ?)");
    
    try {
        $stmt->execute([$conversationId, $isUser, $message]);
        return [
            'success' => true,
            'message_id' => $pdo->lastInsertId()
        ];
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => "Erreur lors de l'enregistrement du message: " . $e->getMessage()
        ];
    }
}

function getConversationMessages($conversationId) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM ai_messages WHERE conversation_id = ? ORDER BY timestamp ASC");
    $stmt->execute([$conversationId]);
    
    return $stmt->fetchAll();
}

function getUserConversations($userId) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM ai_conversations WHERE user_id = ? ORDER BY conversation_date DESC");
    $stmt->execute([$userId]);
    
    return $stmt->fetchAll();
}

// Fonctions admin pour la gestion des contenus
function getAllBooks() {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM books ORDER BY added_at DESC");
    $stmt->execute();
    
    return $stmt->fetchAll();
}

function getBookById($bookId) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->execute([$bookId]);
    
    return $stmt->fetch();
}

function addBook($title, $author, $description, $filePath, $coverPath = null) {
    global $pdo;
    
    $stmt = $pdo->prepare("INSERT INTO books (title, author, description, file_path, cover_path) VALUES (?, ?, ?, ?, ?)");
    
    try {
        $stmt->execute([$title, $author, $description, $filePath, $coverPath]);
        return [
            'success' => true,
            'book_id' => $pdo->lastInsertId()
        ];
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => "Erreur lors de l'ajout du livre: " . $e->getMessage()
        ];
    }
}

function updateBook($bookId, $title, $author, $description, $filePath = null, $coverPath = null) {
    global $pdo;
    
    if ($filePath !== null && $coverPath !== null) {
        $stmt = $pdo->prepare("UPDATE books SET title = ?, author = ?, description = ?, file_path = ?, cover_path = ? WHERE id = ?");
        $params = [$title, $author, $description, $filePath, $coverPath, $bookId];
    } else if ($filePath !== null) {
        $stmt = $pdo->prepare("UPDATE books SET title = ?, author = ?, description = ?, file_path = ? WHERE id = ?");
        $params = [$title, $author, $description, $filePath, $bookId];
    } else if ($coverPath !== null) {
        $stmt = $pdo->prepare("UPDATE books SET title = ?, author = ?, description = ?, cover_path = ? WHERE id = ?");
        $params = [$title, $author, $description, $coverPath, $bookId];
    } else {
        $stmt = $pdo->prepare("UPDATE books SET title = ?, author = ?, description = ? WHERE id = ?");
        $params = [$title, $author, $description, $bookId];
    }
    
    try {
        $stmt->execute($params);
        return [
            'success' => true,
            'message' => "Livre mis à jour avec succès."
        ];
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => "Erreur lors de la mise à jour du livre: " . $e->getMessage()
        ];
    }
}

function deleteBook($bookId) {
    global $pdo;
    
    $stmt = $pdo->prepare("DELETE FROM books WHERE id = ?");
    
    try {
        $stmt->execute([$bookId]);
        return [
            'success' => true,
            'message' => "Livre supprimé avec succès."
        ];
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => "Erreur lors de la suppression du livre: " . $e->getMessage()
        ];
    }
}

// Fonctions pour les musiques
function getAllMusic() {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM music ORDER BY added_at DESC");
    $stmt->execute();
    
    return $stmt->fetchAll();
}

function getMusicById($musicId) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM music WHERE id = ?");
    $stmt->execute([$musicId]);
    
    return $stmt->fetch();
}

function addMusic($title, $artist, $filePath, $coverPath = null) {
    global $pdo;
    
    $stmt = $pdo->prepare("INSERT INTO music (title, artist, file_path, cover_path) VALUES (?, ?, ?, ?)");
    
    try {
        $stmt->execute([$title, $artist, $filePath, $coverPath]);
        return [
            'success' => true,
            'music_id' => $pdo->lastInsertId()
        ];
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => "Erreur lors de l'ajout de la musique: " . $e->getMessage()
        ];
    }
}

function updateMusic($musicId, $title, $artist, $filePath = null, $coverPath = null) {
    global $pdo;
    
    if ($filePath !== null && $coverPath !== null) {
        $stmt = $pdo->prepare("UPDATE music SET title = ?, artist = ?, file_path = ?, cover_path = ? WHERE id = ?");
        $params = [$title, $artist, $filePath, $coverPath, $musicId];
    } else if ($filePath !== null) {
        $stmt = $pdo->prepare("UPDATE music SET title = ?, artist = ?, file_path = ? WHERE id = ?");
        $params = [$title, $artist, $filePath, $musicId];
    } else if ($coverPath !== null) {
        $stmt = $pdo->prepare("UPDATE music SET title = ?, artist = ?, cover_path = ? WHERE id = ?");
        $params = [$title, $artist, $coverPath, $musicId];
    } else {
        $stmt = $pdo->prepare("UPDATE music SET title = ?, artist = ? WHERE id = ?");
        $params = [$title, $artist, $musicId];
    }
    
    try {
        $stmt->execute($params);
        return [
            'success' => true,
            'message' => "Musique mise à jour avec succès."
        ];
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => "Erreur lors de la mise à jour de la musique: " . $e->getMessage()
        ];
    }
}

function deleteMusic($musicId) {
    global $pdo;
    
    $stmt = $pdo->prepare("DELETE FROM music WHERE id = ?");
    
    try {
        $stmt->execute([$musicId]);
        return [
            'success' => true,
            'message' => "Musique supprimée avec succès."
        ];
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => "Erreur lors de la suppression de la musique: " . $e->getMessage()
        ];
    }
}

// Fonctions pour les vidéos
function getAllVideos() {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM videos ORDER BY added_at DESC");
    $stmt->execute();
    
    return $stmt->fetchAll();
}

function getVideoById($videoId) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM videos WHERE id = ?");
    $stmt->execute([$videoId]);
    
    return $stmt->fetch();
}

function addVideo($title, $description, $filePath) {
    global $pdo;
    
    $stmt = $pdo->prepare("INSERT INTO videos (title, description, file_path) VALUES (?, ?, ?)");
    
    try {
        $stmt->execute([$title, $description, $filePath]);
        return [
            'success' => true,
            'video_id' => $pdo->lastInsertId()
        ];
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => "Erreur lors de l'ajout de la vidéo: " . $e->getMessage()
        ];
    }
}

function updateVideo($videoId, $title, $description, $filePath = null) {
    global $pdo;
    
    if ($filePath !== null) {
        $stmt = $pdo->prepare("UPDATE videos SET title = ?, description = ?, file_path = ? WHERE id = ?");
        $params = [$title, $description, $filePath, $videoId];
    } else {
        $stmt = $pdo->prepare("UPDATE videos SET title = ?, description = ? WHERE id = ?");
        $params = [$title, $description, $videoId];
    }
    
    try {
        $stmt->execute($params);
        return [
            'success' => true,
            'message' => "Vidéo mise à jour avec succès."
        ];
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => "Erreur lors de la mise à jour de la vidéo: " . $e->getMessage()
        ];
    }
}

function deleteVideo($videoId) {
    global $pdo;
    
    $stmt = $pdo->prepare("DELETE FROM videos WHERE id = ?");
    
    try {
        $stmt->execute([$videoId]);
        return [
            'success' => true,
            'message' => "Vidéo supprimée avec succès."
        ];
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => "Erreur lors de la suppression de la vidéo: " . $e->getMessage()
        ];
    }
}

// Fonction pour récupérer des statistiques pour le tableau de bord admin
function getAdminStats() {
    global $pdo;
    
    $stats = [];
    
    // Nombre total d'utilisateurs
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM users WHERE is_admin = 0");
    $stmt->execute();
    $stats['total_users'] = $stmt->fetch()['total'];
    
    // Nombre total de livres
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM books");
    $stmt->execute();
    $stats['total_books'] = $stmt->fetch()['total'];
    
    // Nombre total de musiques
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM music");
    $stmt->execute();
    $stats['total_music'] = $stmt->fetch()['total'];
    
    // Nombre total de vidéos
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM videos");
    $stmt->execute();
    $stats['total_videos'] = $stmt->fetch()['total'];
    
    // Nombre total de conversations IA
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM ai_conversations");
    $stmt->execute();
    $stats['total_conversations'] = $stmt->fetch()['total'];
    
    return $stats;
}
?>
