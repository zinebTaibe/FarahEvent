<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Assainir et valider les données
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $rating = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1, 'max_range' => 5]
    ]);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

    // Vérifications supplémentaires
    $isValid = true;

    if (empty($name) || strlen($name) < 2 || strlen($name) > 100) {
        $isValid = false;
        echo "Nom invalide (2-100 caractères)<br>";
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-z]{2,}$/", $email)) {
    $isValid = false;
    $response['errors']['email'] = "Email invalide ou format non autorisé.";
}


    if (empty($rating)) {
        $isValid = false;
        echo "Note invalide (1-5 étoiles)<br>";
    }

    if (empty($message) || strlen($message) < 10 || strlen($message) > 500) {
        $isValid = false;
        echo "Message invalide (10-500 caractères)<br>";
    }

    if ($isValid) {
        try {
            require("conn.php");

            // Vérifier si le client existe
            $stmt = $pdo->prepare("SELECT id FROM client WHERE email = ? AND nom = ?");
            $stmt->execute([$email, $name]);
            $client = $stmt->fetch();

            if (!$client) {
                echo "Vous devez être un client enregistré pour déposer un avis.<br>";
                header("Location: ./Connexion/login.php");
                exit;
            }

            // Insérer l'avis
            $insertQuery = "INSERT INTO avis (note, commentaire, Client_id, created_at, statut) 
                            VALUES (:rating, :message, :id_client, NOW(), 'en_attente')";

            $stmt = $pdo->prepare($insertQuery);
            $success = $stmt->execute([
                ':rating' => $rating,
                ':message' => $message,
                ':id_client' => $client['id']
            ]);

            if ($success) {
                echo "Merci pour votre avis ! Il sera publié après modération.";
                header("Location:index.php");
                
            } else {
                echo "Erreur lors de l'enregistrement de votre avis.";
            }
        } catch (PDOException $e) {
            echo "Erreur technique : " . $e->getMessage();
        }
    }

    exit; // Important pour éviter que du HTML suive après ce script
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farah Event - Organisation de Mariages</title>
    <meta name="description" content="Agence d'organisation de mariages à Oujda, spécialisée dans les événements sur mesure et prestations haut de gamme.">
    <link rel="stylesheet" href="/FarahEvent/styles/index.css">
    <link rel="stylesheet" href="/FarahEvent/styles/fonts.css">
    <style>
        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: none;
        }

        .feedback-message {
            padding: 0.75rem;
            margin: 1rem 0;
            border-radius: 4px;
            display: none;
        }

        .feedback-message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .feedback-message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .loading-spinner {
            display: inline-block;
            margin-left: 0.5rem;
        }

        button:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .pending-badge {
            background: #fff3cd;
            color: #856404;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.8em;
            margin-left: 8px;
            display: inline-block;
        }

        .approved-badge {
            background: #d4edda;
            color: #155724;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.8em;
            margin-left: 8px;
            display: inline-block;
        }

        .rating-select {
            display: flex;
            gap: 5px;
        }

        .rating-select input[type="radio"] {
            display: none;
        }

        .rating-select label {
            font-size: 24px;
            color: #ddd;
            cursor: pointer;
            transition: color 0.2s;
        }

        .rating-select input[type="radio"]:checked ~ label {
            color: #ffc107;
        }

        .rating-select label:hover,
        .rating-select label:hover ~ label {
            color: #ffc107;
        }

        #submit-btn {
            position: relative;
        }

        .btn-text {
            transition: opacity 0.3s;
        }

        .loading-content {
            display: none;
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
        }

        .loading-content svg {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .form-group.invalid input,
        .form-group.invalid textarea {
            border-color: #dc3545;
        }

        .form-group.valid input,
        .form-group.valid textarea {
            border-color: #28a745;
        }
        .filled-stars {
    color: #ffc107; /* Couleur or pour les étoiles pleines */
}

.empty-stars {
    color: #e0e0e0; /* Couleur grise pour les étoiles vides */
    letter-spacing: 2px; /* Espacement pour mieux distinguer les étoiles */
}
    </style>
</head>
<body>
    <?php include 'nav.php'; ?>
    
    <header class="hero">
        <video autoplay muted loop playsinline>
            <source src="/FarahEvent/include/images/video.mp4" type="video/mp4">
            Votre navigateur ne supporte pas la vidéo.
        </video>
        <h1>Célébrez Votre Mariage</h1>
        <a href="/FarahEvent/Reservation/index.php">Réservez dès maintenant</a>
    </header>
    
    <main>
        <section class="bienvenue">
            <div>
                <h2>Bienvenue</h2>
                <h3>Découvrez l'expérience DreamEvents</h3>
                <p>À la recherche d'une équipe exceptionnelle pour créer le mariage de vos rêves ? Notre agence Dream Events, située au cœur de Oujda, vous propose un service sur mesure, professionnel et chaleureux. Laissez-nous vous accompagner dans la réalisation d'un événement inoubliable pour célébrer votre amour.</p>
                <a href="/FarahEvent/Propos/index.php">Lire plus</a>
            </div>
            <div class="video-container">
                <video id="weddingVideo" poster="/FarahEvent/include/images/img1.webp">
                    <source src="/FarahEvent/include/images/wedding1.mp4" type="video/mp4">
                    Votre navigateur ne supporte pas la lecture des vidéos.
                </video>
                <button class="play-btn" aria-label="Lire la vidéo">
                    <img src="/FarahEvent/include/icons/circle-play.svg" alt="Bouton lecture">
                </button>
            </div>
        </section>
   
        <section class="prestation">
            <div class="first-part">
                <h2>Nos Prestations</h2>
                <p>Offrant des espaces élégants, des décorations exquises, une beauté de mariée impeccable, une gastronomie exceptionnelle et des animations envoûtantes, notre service de mariage tout-en-un garantit que chaque détail de votre jour spécial est pris en charge avec perfection.</p>
            </div>
            <div class="cards">
                <div class="restauration">
                    <img src="/FarahEvent/include/images/restauration.webp" alt="Service de restauration pour mariage">
                    <a href="/FarahEvent/Prestation/Restauration/index.php">Restauration</a>
                </div>
                <div class="espace">
                    <img src="/FarahEvent/include/images/espace.webp" alt="Espaces et décorations de mariage">
                    <a href="/FarahEvent/Prestation/Espace&Décoration/index.php">Espace & Décoration</a>
                </div>
                <div class="beaute">
                    <img src="/FarahEvent/include/images/beaute.webp" alt="Service de beauté pour la mariée">
                    <a href="/FarahEvent/Prestation/Beauté_mariée/index.php">Beauté de la mariée</a>
                </div>
            </div>
        </section>

        <section class="temoignages">   
            <div class="first-part">
                <h2>Témoignage</h2>
                <p>Découvrez ce que nos clients pensent de nos services.</p>
            </div>
            <div class="testimonials-container">
                <?php
                require_once "conn.php";
                
                try {
                    // Afficher les témoignages validés
                    $query = "SELECT a.*, c.nom, c.email 
                              FROM avis a
                              JOIN client c ON a.Client_id = c.id
                              WHERE a.statut = 'validé'
                              ORDER BY a.created_at DESC
                              LIMIT 4";
                    
                    $stmt = $pdo->query($query);
                    $testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if (empty($testimonials)) {
                        echo '<div class="no-testimonials">
                                <p>Aucun témoignage à afficher pour le moment.</p>
                              </div>';
                    } else {
                        foreach ($testimonials as $testimonial) {
                            $initial = strtoupper(substr(trim($testimonial['nom']), 0, 1));
                            $date = date('d/m/Y', strtotime($testimonial['created_at']));
                            $stars = str_repeat('★', (int)$testimonial['note']);
                            $emptyStars = str_repeat('☆', 5 - (int)$testimonial['note']);
                ?>
                <div class="testimonial">
                    <div class="client-info">
                        <div class="client-avatar">
                            <span><?= htmlspecialchars($initial) ?></span>
                        </div>
                        <div class="client-details">
                            <h4><?= htmlspecialchars($testimonial['nom']) ?></h4>
                            <span class="date"><?= $date ?></span>
                        </div>
                    </div>
                    <div class="rating" aria-label="<?= $testimonial['note'] ?> étoiles">
                        <span class="filled-stars" ><?= $stars ?></span>
                        <span class="empty-stars"><?= $emptyStars ?></span>
                    </div>
                    <p class="testimonial-text"><?= nl2br(htmlspecialchars($testimonial['commentaire'])) ?></p>
                </div>
                <?php
                        }
                    }
                } catch (PDOException $e) {
                    echo '<div class="error-message">
                            <p>Erreur de chargement des témoignages</p>
                          </div>';
                }
                ?>
            </div>
        </section>

        <section class="avis-client">
            <div class="first-part">
                <h2>Votre Avis</h2>
                <p>Partagez votre expérience avec nous et aidez-nous à améliorer nos services pour les futurs mariés.</p>
            </div>
            <div class="form-container">
                <form id="testimonial-form" action="#" method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Nom complet</label>
                            <input type="text" id="name" name="name" required minlength="2" maxlength="100">
                            <div class="error-message" id="name-error"></div>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-z]{2,}">
                            <div class="error-message" id="email-error"></div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Votre note</label>
                        <div class="rating-select">
                            <input type="radio" id="star5" name="rating" value="5" checked>
                            <label for="star5" title="5 étoiles">★</label>
                            <input type="radio" id="star4" name="rating" value="4">
                            <label for="star4" title="4 étoiles">★</label>
                            <input type="radio" id="star3" name="rating" value="3">
                            <label for="star3" title="3 étoiles">★</label>
                            <input type="radio" id="star2" name="rating" value="2">
                            <label for="star2" title="2 étoiles">★</label>
                            <input type="radio" id="star1" name="rating" value="1">
                            <label for="star1" title="1 étoile">★</label>
                        </div>
                        <div class="error-message" id="rating-error"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Votre témoignage</label>
                        <textarea id="message" name="message" rows="4" required minlength="10" maxlength="500"></textarea>
                        <div class="error-message" id="message-error"></div>
                    </div>
                    
                    <div id="form-feedback" class="feedback-message"></div>
                    
                    <button type="submit" class="submit-btn" id="submit-btn">
                        <span class="btn-text">Envoyer votre avis</span>
                        <span class="loading-content">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"></path>
                            </svg>
                        </span>
                    </button>
                </form>
            </div>
        </section>
    </main>
    
    <?php include 'footer.html'; ?>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const video = document.getElementById("weddingVideo");
            const button = document.querySelector(".play-btn");
            
            button.addEventListener('click', function() {
                if (video.paused) {
                    video.play();
                    button.style.display = "none";
                }
            });
            
            video.addEventListener('ended', function() {
                button.style.display = "block";
            });

           });
        
    </script>
</body>
</html>
