<?php
include('../dashboard.html');
require_once '../../conn.php';


try {
    $pdo = new PDO('mysql:host=localhost;dbname=FarahEvent;charset=utf8', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $totalUsers = $pdo->query("SELECT COUNT(*) FROM Client")->fetchColumn();
    $totalAdmins = $pdo->query("SELECT COUNT(*) FROM Admin")->fetchColumn();
    $totalStandardUsers = $totalUsers - $totalAdmins;
    $totalEvents = $pdo->query("SELECT COUNT(*) FROM Evenement")->fetchColumn();
    $activeEvents = $pdo->query("SELECT COUNT(*) FROM Evenement WHERE etat = 'confirmé'")->fetchColumn();
    $pendingEvents = $pdo->query("SELECT COUNT(*) FROM Evenement WHERE etat = 'planifié'")->fetchColumn();
    $totalParticipants = $pdo->query("SELECT COUNT(DISTINCT Client_id) FROM Evenement WHERE Client_id IS NOT NULL")->fetchColumn();
    $nouveauxUtilisateurs = $pdo->query("SELECT COUNT(*) FROM Client WHERE DATE(created_at) = CURDATE()")->fetchColumn();

} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - FarahEvent</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #5e60ce;
            --primary-light: #6930c3;
            --primary-dark: #4a257c;
            --success: #2ecc71;
            --success-light: #56d598;
            --warning: #f39c12;
            --warning-light: #fbb44c;
            --danger: #e74c3c;
            --danger-light: #f87171;
            --purple: #8075ff;
            --purple-light: #a78bfa;
            --teal: #4cc9bd;
            --teal-light: #5eead4;
            --indigo: #4f46e5;
            --indigo-light: #818cf8;
            --rose: #e11d48;
            --rose-light: #fb7185;
            --dark: #222831;
            --dark-light: #393E46;
            --text-main: #313866;
            --text-secondary: #64748b;
            --bg-light: #f1f6ff;
            --bg-grad: linear-gradient(135deg, #f1f6ff 0%, #f8f9fa 100%);
            --card-bg: #ffffff;
            --card-hover: #fefefe;
            --border-radius: 16px;
            --shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.02);
            --shadow-hover: 0 20px 25px -5px rgba(0, 0, 0, 0.08), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
            --transition-slow: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1);
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            --transition-fast: all 0.15s cubic-bezier(0.25, 0.8, 0.25, 1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg-grad);
            color: var(--text-main);
            line-height: 1.6;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }
        
        body::before {
            content: "";
            position: fixed;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(96, 165, 250, 0.03) 0%, rgba(99, 102, 241, 0.01) 50%, rgba(255, 255, 255, 0) 100%);
            z-index: -1;
        }
        
        .admin-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 2rem;
            animation: fadeIn 0.8s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(30px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
            100% { transform: translateY(0px); }
        }
        
        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid rgba(99, 102, 241, 0.1);
            position: relative;
        }
        
        .content-header::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 100px;
            height: 3px;
            background: linear-gradient(90deg, var(--primary), var(--primary-light));
            border-radius: 3px;
            box-shadow: 0 2px 6px rgba(104, 109, 224, 0.2);
        }
        
        .content-header h1 {
            font-size: 2.2rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary) 0%, var(--indigo) 50%, var(--primary-light) 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            text-shadow: 0px 2px 4px rgba(99, 102, 241, 0.1);
            letter-spacing: -1px;
            position: relative;
            display: inline-block;
        }
        
        .content-header h1::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 40%;
            height: 2px;
            background: linear-gradient(90deg, var(--primary), transparent);
            border-radius: 3px;
        }
        
        .refresh-btn {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: var(--border-radius);
            font-size: 0.95rem;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.7rem;
            box-shadow: 0 4px 10px rgba(99, 102, 241, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .refresh-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: var(--transition-slow);
        }
        
        .refresh-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 14px rgba(99, 102, 241, 0.35);
        }
        
        .refresh-btn:hover::before {
            left: 100%;
        }
        
        .refresh-btn:active {
            transform: translateY(1px);
            box-shadow: 0 4px 8px rgba(99, 102, 241, 0.25);
        }
        
        .refresh-btn i {
            transition: var(--transition);
        }
        
        .refresh-btn:hover i {
            transform: rotate(180deg);
        }
        
        .section-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 1.8rem;
            display: flex;
            align-items: center;
            gap: 0.7rem;
            position: relative;
            padding-left: 1rem;
            animation: slideInRight 0.5s ease-out;
        }
        
        .section-title::before {
            content: '';
            position: absolute;
            left: 0;
            height: 100%;
            width: 4px;
            background: linear-gradient(to bottom, var(--primary), var(--primary-light));
            border-radius: 4px;
        }
        
        .section-title i {
            color: var(--primary);
            font-size: 1.4rem;
        }
        
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 1.8rem;
            margin-bottom: 3rem;
        }
        
        .stat-card {
            background-color: var(--card-bg);
            border-radius: var(--border-radius);
            padding: 1.8rem;
            box-shadow: var(--shadow);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            border: 1px solid rgba(229, 231, 235, 0.5);
            opacity: 0;
            transform: translateY(20px);
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0.001) 100%);
            z-index: 0;
        }
        
        .stat-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--primary-light));
            opacity: 0;
            transition: var(--transition);
            transform: scaleX(0);
            transform-origin: left;
        }
        
        .stat-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: var(--shadow-hover);
            background-color: var(--card-hover);
            border-color: rgba(229, 231, 235, 0.8);
        }
        
        .stat-card:hover::after {
            opacity: 1;
            transform: scaleX(1);
        }
        
        .stat-icon {
            font-size: 1.6rem;
            width: 70px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 18px;
            margin-right: 1.2rem;
            color: white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
            position: relative;
            z-index: 1;
            transition: var(--transition);
        }
        
        .stat-card:hover .stat-icon {
            transform: scale(1.1) rotate(5deg);
        }
        
        .stat-icon::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: inherit;
            border-radius: inherit;
            filter: blur(8px);
            opacity: 0;
            z-index: -1;
            transition: var(--transition);
        }
        
        .stat-card:hover .stat-icon::after {
            opacity: 0.5;
            transform: scale(1.15) translateY(5px);
        }
        
        .primary-color { 
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
        }
        
        .success-color { 
            background: linear-gradient(135deg, var(--success) 0%, var(--success-light) 100%);
        }
        
        .warning-color { 
            background: linear-gradient(135deg, var(--warning) 0%, var(--warning-light) 100%);
        }
        
        .purple-color { 
            background: linear-gradient(135deg, var(--purple) 0%, var(--purple-light) 100%);
        }
        
        .teal-color { 
            background: linear-gradient(135deg, var(--teal) 0%, var(--teal-light) 100%);
        }
        
        .indigo-color {
            background: linear-gradient(135deg, var(--indigo) 0%, var(--indigo-light) 100%);
        }
        
        .rose-color {
            background: linear-gradient(135deg, var(--rose) 0%, var(--rose-light) 100%);
        }
        
        .stat-info {
            position: relative;
            z-index: 1;
        }
        
        .stat-info h3 {
            font-size: 2.1rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 0.4rem;
            transition: var(--transition);
            position: relative;
            display: inline-block;
        }
        
        .stat-card:hover .stat-info h3 {
            transform: scale(1.05);
        }
        
        .stat-info p {
            margin: 0;
            font-size: 0.9rem;
            color: var(--text-secondary);
            font-weight: 500;
            transition: var(--transition);
            position: relative;
            display: inline-block;
            padding-bottom: 3px;
        }
        
        .stat-info p::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 0;
            height: 1px;
            background: linear-gradient(90deg, var(--text-secondary), transparent);
            transition: var(--transition);
        }
        
        .stat-card:hover .stat-info p::after {
            width: 100%;
        }
        
        /* Dashboard section dividers */
        .dashboard-section {
            margin-bottom: 3.5rem;
            padding: 0.5rem;
            position: relative;
        }
        
        .dashboard-section::before {
            content: '';
            position: absolute;
            bottom: -1.5rem;
            left: 50%;
            transform: translateX(-50%);
            width: 150px;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--text-secondary) 50%, transparent);
            opacity: 0.2;
        }
        
        /* Fancy glow effect */
        .glow-effect {
            position: fixed;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.05) 0%, rgba(99, 102, 241, 0) 70%);
            pointer-events: none;
            z-index: -1;
            animation: float 6s ease-in-out infinite;
        }
        
        @media (max-width: 992px) {
            .admin-container {
                padding: 1.5rem;
            }
            
            .stats-cards {
                grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
                gap: 1.5rem;
            }
            
            .stat-card {
                padding: 1.5rem;
            }
            
            .stat-icon {
                width: 60px;
                height: 60px;
                font-size: 1.4rem;
            }
            
            .stat-info h3 {
                font-size: 1.8rem;
            }
        }
        
        @media (max-width: 768px) {
            .admin-container {
                padding: 1.2rem;
            }
            
            .content-header {
                flex-direction: column;
                gap: 1.2rem;
                align-items: flex-start;
            }
            
            .stats-cards {
                grid-template-columns: 1fr;
            }
            
            .section-title {
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="glow-effect" style="top: 20%; left: 10%;"></div>
        <div class="glow-effect" style="top: 60%; left: 80%;"></div>
        
        <header class="content-header">
            <h1>Tableau de bord</h1>
            <button class="refresh-btn" onclick="location.reload();">
                <i class="fas fa-sync-alt"></i> Actualiser
            </button>
        </header>

        <!-- Section utilisateurs -->
        <div class="dashboard-section">
            <h2 class="section-title"><i class="fas fa-users-cog"></i> Gestion des utilisateurs</h2>
            <div class="stats-cards">
                <div class="stat-card">
                    <div class="stat-icon primary-color"><i class="fas fa-users"></i></div>
                    <div class="stat-info">
                        <h3><?= $totalUsers ?></h3>
                        <p>Total Utilisateurs</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon success-color"><i class="fas fa-user-shield"></i></div>
                    <div class="stat-info">
                        <h3><?= $totalAdmins ?></h3>
                        <p>Administrateurs</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon warning-color"><i class="fas fa-user"></i></div>
                    <div class="stat-info">
                        <h3><?= $totalStandardUsers ?></h3>
                        <p>Utilisateurs Standard</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon purple-color"><i class="fas fa-user-plus"></i></div>
                    <div class="stat-info">
                        <h3><?= $nouveauxUtilisateurs ?></h3>
                        <p>Nouveaux Utilisateurs (aujourd'hui)</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section événements -->
        <div class="dashboard-section">
            <h2 class="section-title"><i class="fas fa-calendar-week"></i> Gestion des événements</h2>
            <div class="stats-cards">
                <div class="stat-card">
                    <div class="stat-icon indigo-color"><i class="fas fa-calendar-alt"></i></div>
                    <div class="stat-info">
                        <h3><?= $totalEvents ?></h3>
                        <p>Total Événements</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon success-color"><i class="fas fa-check-circle"></i></div>
                    <div class="stat-info">
                        <h3><?= $activeEvents ?></h3>
                        <p>Événements Confirmés</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon warning-color"><i class="fas fa-clock"></i></div>
                    <div class="stat-info">
                        <h3><?= $pendingEvents ?></h3>
                        <p>Événements Planifiés</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon rose-color"><i class="fas fa-user-friends"></i></div>
                    <div class="stat-info">
                        <h3><?= $totalParticipants ?></h3>
                        <p>Participants</p>
                    </div>
                </div>
            </div>
        </div>
    </div>>Participants</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Effet d'animation pour les cartes de statistiques
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.stat-card');
            cards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 100 * index);
            });
        });
    </script>
</body>
</html>
