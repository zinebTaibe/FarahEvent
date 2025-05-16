<?php
header('Content-Type: application/json');

// Fonction pour envoyer une réponse JSON standardisée
function jsonResponse($success, $message = '', $data = []) {
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

try {
    // Vérification de l'ID
    if (!isset($_GET['id'])) {
        jsonResponse(false, 'Paramètre ID manquant');
    }

    $id = $_GET['id'];

    // Validation de l'ID
    if (!is_numeric($id) || $id <= 0) {
        jsonResponse(false, 'ID invalide');
    }

    // Connexion sécurisée à la base de données
    require_once '../../conn.php'; // Utilisez require_once pour éviter les inclusions multiples
    
    // Requête préparée pour éviter les injections SQL
    $query = "DELETE FROM avis WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
    // Exécution de la requête
    $result = $stmt->execute();
    
    // Vérification du résultat
    if ($result && $stmt->rowCount() > 0) {
        jsonResponse(true, 'Avis supprimé avec succès');
    } else {
        jsonResponse(false, 'Aucun avis trouvé avec cet ID');
    }

} catch (PDOException $e) {
    // Journalisation de l'erreur (à adapter selon votre environnement)
    error_log('Erreur de suppression: ' . $e->getMessage());
    jsonResponse(false, 'Erreur de base de données');
} catch (Exception $e) {
    error_log('Erreur générale: ' . $e->getMessage());
    jsonResponse(false, 'Erreur inattendue');
}
