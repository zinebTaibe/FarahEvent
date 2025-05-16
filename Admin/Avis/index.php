<?php
// Database connection and dashboard inclusion
require_once '../../conn.php';
if (!$pdo) {
    die("Erreur de connexion à la base de données");
}
include('../dashboard.html');

// Handle status update if POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $reviewId = $_POST['review_id'];
    $newStatus = $_POST['new_status'];
    
    try {
        $stmt = $pdo->prepare("UPDATE avis SET statut = :status WHERE id = :id");
        $stmt->execute([':status' => $newStatus, ':id' => $reviewId]);
        $updateSuccess = true;
    } catch (PDOException $e) {
        $updateError = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Gestion des Avis Clients</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Base styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        
        .admin-container {
            display: flex;
            min-height: 100vh;
        }
        
        .main-content {
            flex: 1;
            padding: 20px;
        }
        
        .header {
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #333;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        /* Table styles */
        .reviews-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
        }
        
        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .search-input {
            padding: 8px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 300px;
        }
        
        .table-responsive {
            overflow-x: auto;
        }
        
        .reviews-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .reviews-table th, .reviews-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .reviews-table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        
        /* Status styles */
        .status-select {
            padding: 5px;
            border-radius: 4px;
            border: 1px solid #ddd;
            cursor: pointer;
            min-width: 100px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.8em;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-pending {
            background-color: #FFF3CD;
            color: #856404;
        }
        
        .status-approved {
            background-color: #D4EDDA;
            color: #155724;
        }
        
        .status-rejected {
            background-color: #F8D7DA;
            color: #721C24;
        }
        
        /* Action buttons */
        .action-btn {
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 5px;
            font-size: 0.9em;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .view-btn {
            background-color: #17a2b8;
            color: white;
        }
        
        .delete-btn {
            background-color: #dc3545;
            color: white;
        }
        
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 20px;
            border-radius: 8px;
            width: 50%;
            max-width: 600px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
        
        .close-btn {
            background: none;
            border: none;
            font-size: 1.5em;
            cursor: pointer;
            color: #777;
        }
        
        .review-detail {
            margin-bottom: 15px;
        }
        
        .review-detail label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
            color: #555;
        }
        
        .full-review-content {
            white-space: pre-line;
            line-height: 1.6;
        }
        
        .modal-footer {
            border-top: 1px solid #eee;
            padding-top: 15px;
            margin-top: 15px;
            text-align: right;
        }
        
        .cancel-btn, .confirm-delete-btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .cancel-btn {
            background-color: #6c757d;
            color: white;
            margin-right: 10px;
        }
        
        .confirm-delete-btn {
            background-color: #dc3545;
            color: white;
        }
        
        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            gap: 5px;
        }
        
        .pagination a {
            padding: 8px 12px;
            text-decoration: none;
            border: 1px solid #ddd;
            color: #007bff;
            border-radius: 4px;
        }
        
        .pagination a.active {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }
        
        /* Notifications */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 4px;
            color: white;
            z-index: 1001;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            animation: slideIn 0.3s, fadeOut 0.5s 2.5s forwards;
        }
        
        .notification.success {
            background-color: #28a745;
        }
        
        .notification.error {
            background-color: #dc3545;
        }
        
        @keyframes slideIn {
            from { transform: translateX(100%); }
            to { transform: translateX(0); }
        }
        
        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="main-content">
            <div class="header">
                <h1><i class="fas fa-star"></i> Gérer les avis clients</h1>
                <?php if (isset($updateSuccess)): ?>
                    <div class="notification success">
                        <i class="fas fa-check-circle"></i> Statut mis à jour avec succès
                    </div>
                <?php elseif (isset($updateError)): ?>
                    <div class="notification error">
                        <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($updateError) ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="reviews-container">
                <div class="table-header">
                    <h2>Liste des avis clients</h2>
                    <div class="table-actions">
                        <input type="text" class="search-input" placeholder="Rechercher un avis..." aria-label="Recherche">
                    </div>
                </div>
                
                <div class="table-responsive">
                    <form method="post" id="statusForm">
                        <table class="reviews-table">
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Client</th>
                                    <th scope="col">Commentaire</th>
                                    <th scope="col">Note</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Statut</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            try {
                                $query = "SELECT a.*, c.nom as client_name FROM avis a LEFT JOIN client c ON a.Client_id = c.id ORDER BY a.created_at DESC";
                                $stmt = $pdo->prepare($query);
                                $stmt->execute();
                                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                
                                foreach ($data as $row) {
                                    $id = htmlspecialchars($row['id']);
                                    $comment = htmlspecialchars($row['commentaire']);
                                    $note = htmlspecialchars($row['note']);
                                    $clientName = htmlspecialchars($row['client_name'] ?? 'Client inconnu');
                                    $dateCreation = htmlspecialchars($row['created_at']);
                                    $status = htmlspecialchars($row['statut']);
                                    ?>
                                    <tr>
                                        <td class='review-id'><?= $id ?></td>
                                        <td><?= $clientName ?></td>
                                        <td class='review-content'><?= mb_strimwidth($comment, 0, 50, '...') ?></td>
                                        <td><span class='star-rating'><?= $note ?></span></td>
                                        <td><?= $dateCreation ?></td>
                                        <td>
                                            <select class="status-select" name="status[<?= $id ?>]" onchange="updateStatus(this, <?= $id ?>)">
                                                <option value="en_attente" <?= $status == 'en_attente' ? 'selected' : '' ?>>En attente</option>
                                                <option value="validé" <?= $status == 'validé' ? 'selected' : '' ?>>Validé</option>
                                                <option value="rejeté" <?= $status == 'rejeté' ? 'selected' : '' ?>>Rejeté</option>
                                            </select>
                                            <span class="status-badge status-<?= str_replace('é', 'e', strtolower($status)) ?>" style="display: none;">
                                                <?= ucfirst($status) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class='review-actions'>
                                                <button type="button" class='action-btn view-btn' title='Voir l avis complet' 
                                                    onclick='openViewModal(<?= $id ?>, "<?= $clientName ?>", "<?= addslashes($comment) ?>", <?= $note ?>, "<?= $dateCreation ?>", "<?= $status ?>")'>
                                                    <i class='fas fa-eye'></i> Voir
                                                </button>
                                                <button type="button" class='action-btn delete-btn' title='Supprimer l avis' 
                                                    onclick='openDeleteModal(<?= $id ?>, "<?= $clientName ?>")'>
                                                    <i class='fas fa-trash'></i> Supprimer
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php }
                            } catch (PDOException $e) {
                                echo "<tr><td colspan='7'>Erreur de base de données: " . $e->getMessage() . "</td></tr>";
                            }
                            ?>
                            </tbody>
                        </table>
                        <input type="hidden" name="update_status" value="1">
                    </form>
                </div>
            </div>

            <!-- Pagination -->
            <nav aria-label="Navigation des pages">
                <div class="pagination">
                    <a href="#" aria-label="Page précédente">&laquo;</a>
                    <a href="#" class="active" aria-current="page">1</a>
                    <a href="#">2</a>
                    <a href="#">3</a>
                    <a href="#" aria-label="Page suivante">&raquo;</a>
                </div>
            </nav>
        </div>
    </div>

    <!-- View Modal -->
    <div id="viewModal" class="modal" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Détails de l'avis</h3>
                <button class="close-btn" onclick="closeModal('viewModal')" aria-label="Fermer">&times;</button>
            </div>
            <div class="modal-body">
                <div class="review-detail">
                    <label for="view-review-id">ID de l'avis</label>
                    <p id="view-review-id"></p>
                </div>
                <div class="review-detail">
                    <label for="view-client-name">Client</label>
                    <p id="view-client-name"></p>
                </div>
                <div class="review-detail">
                    <label for="view-review-content">Commentaire complet</label>
                    <p class="full-review-content" id="view-review-content"></p>
                </div>
                <div class="review-detail">
                    <label for="view-review-rating">Note</label>
                    <p id="view-review-rating"></p>
                </div>
                <div class="review-detail">
                    <label for="view-review-date">Date</label>
                    <p id="view-review-date"></p>
                </div>
                <div class="review-detail">
                    <label for="view-review-status">Statut</label>
                    <p id="view-review-status" class="status-badge"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button class="cancel-btn" onclick="closeModal('viewModal')">Fermer</button>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal" role="dialog" aria-labelledby="deleteModalTitle" aria-hidden="true">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="deleteModalTitle">Confirmer la suppression</h3>
                <button class="close-btn" onclick="closeModal('deleteModal')" aria-label="Fermer">&times;</button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer l'avis <strong id="delete-review-id"></strong> de <strong id="delete-client-name"></strong> ?</p>
                <p>Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <button class="cancel-btn" onclick="closeModal('deleteModal')">Annuler</button>
                <button class="confirm-delete-btn" onclick="confirmDelete()">Confirmer la suppression</button>
            </div>
        </div>
    </div>

    <script>
        // Open View Modal and set the details
        function openViewModal(id, clientName, comment, rating, date, status) {
            document.getElementById('view-review-id').innerText = id;
            document.getElementById('view-client-name').innerText = clientName;
            document.getElementById('view-review-content').innerText = comment;
            document.getElementById('view-review-rating').innerText = rating;
            document.getElementById('view-review-date').innerText = date;
            
            const statusElement = document.getElementById('view-review-status');
            statusElement.innerText = status.charAt(0).toUpperCase() + status.slice(1);
            statusElement.className = 'status-badge status-' + status.toLowerCase().replace('é', 'e');
            
            document.getElementById('viewModal').style.display = 'block';
        }

        // Close the modal
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Show the delete confirmation modal
        function openDeleteModal(reviewId, clientName) {
            document.getElementById('delete-review-id').innerText = reviewId;
            document.getElementById('delete-client-name').innerText = clientName;
            document.getElementById('deleteModal').style.display = 'block';
        }

        // Update status via AJAX
        function updateStatus(selectElement, reviewId) {
            const newStatus = selectElement.value;
            const formData = new FormData();
            formData.append('review_id', reviewId);
            formData.append('new_status', newStatus);
            formData.append('update_status', '1');
            
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    // Show success notification
                    const notification = document.createElement('div');
                    notification.className = 'notification success';
                    notification.innerHTML = '<i class="fas fa-check-circle"></i> Statut mis à jour avec succès';
                    document.querySelector('.header').appendChild(notification);
                    
                    // Remove notification after 3 seconds
                    setTimeout(() => {
                        notification.remove();
                    }, 3000);
                    
                    // Update the status badge in the view modal if open
                    const viewModalStatus = document.getElementById('view-review-status');
                    if (viewModalStatus && viewModalStatus.textContent === selectElement.options[selectElement.selectedIndex].text) {
                        viewModalStatus.textContent = selectElement.options[selectElement.selectedIndex].text;
                        viewModalStatus.className = 'status-badge status-' + newStatus.toLowerCase().replace('é', 'e');
                    }
                } else {
                    throw new Error('Erreur lors de la mise à jour');
                }
            })
            .catch(error => {
                // Show error notification
                const notification = document.createElement('div');
                notification.className = 'notification error';
                notification.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + error.message;
                document.querySelector('.header').appendChild(notification);
                
                // Remove notification after 3 seconds
                setTimeout(() => {
                    notification.remove();
                }, 3000);
                
                // Revert to previous value
                selectElement.value = selectElement.getAttribute('data-previous-value');
            });
        }

        // Confirm the deletion of the review
        function confirmDelete() {
            const reviewId = document.getElementById('delete-review-id').textContent;
            
            fetch('delete.php?id=' + reviewId)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur réseau');
                    }
                    return response.text();
                })
                .then(data => {
                    console.log('Avis supprimé:', data);
                    closeModal('deleteModal');
                    location.reload();
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors de la suppression');
                });
        }

        // Search functionality
        document.querySelector('.search-input').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('.reviews-table tbody tr');

            rows.forEach(row => {
                const id = row.querySelector('.review-id')?.textContent.toLowerCase() || '';
                const clientName = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
                const comment = row.querySelector('.review-content')?.textContent.toLowerCase() || '';
                const date = row.querySelector('td:nth-child(5)')?.textContent.toLowerCase() || '';
                const status = row.querySelector('.status-select')?.value.toLowerCase() || '';

                if (id.includes(searchTerm) || clientName.includes(searchTerm) || 
                    comment.includes(searchTerm) || date.includes(searchTerm) || 
                    status.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Store previous status value on focus
        document.querySelectorAll('.status-select').forEach(select => {
            select.addEventListener('focus', function() {
                this.setAttribute('data-previous-value', this.value);
            });
        });
    </script>
</body>
</html>
