<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';

// Protection de la page - rediriger vers login si non connecté
if (!isLoggedIn()) {
    redirect('../../login.php');
}
?>
