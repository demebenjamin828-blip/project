<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ELOHIM Appui | Portail d'Excellence</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-green: #064e3b;
            --accent-gold: #facc15;
            --glass: rgba(15, 23, 42, 0.75);
        }

        * { margin:0; padding:0; box-sizing:border-box; font-family: 'Plus Jakarta Sans', sans-serif; }

        /* --- CORRECTION MAJEURE DU SCROLL --- */
        html, body {
            height: 100%;
            margin: 0;
        }

        body {
            min-height: 100vh;
            background: radial-gradient(circle at top right, #065f46, #022c22) fixed;
            display: flex;
            justify-content: center;
            align-items: center; /* Centre si la page est grande */
            padding: 80px 20px; /* Force un espace en haut et en bas pour scroller */
            overflow-y: auto; /* Active le scroll vertical */
        }

        /* --- LE THROBBER HORIZONTAL (AMÉLIORÉ) --- */
        .throbber-container {
            width: 100%;
            height: 5px; /* Un peu plus épais */
            background: rgba(255, 255, 255, 0.05);
            position: absolute;
            top: 0;
            left: 0;
            overflow: hidden;
            border-radius: 32px 32px 0 0;
            z-index: 100;
        }

        .throbber-line {
            width: 40%;
            height: 100%;
            background: linear-gradient(90deg, transparent, var(--accent-gold), transparent);
            position: absolute;
            left: -50%;
            animation: moveThrobber 2.5s infinite linear;
            box-shadow: 0 0 15px var(--accent-gold);
        }

        @keyframes moveThrobber {
            0% { left: -50%; }
            100% { left: 110%; }
        }

        /* --- LA CARTE --- */
        .glass-card {
            background: var(--glass);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 32px;
            padding: 60px 40px;
            max-width: 850px;
            width: 100%;
            text-align:center;
            box-shadow: 0 40px 100px rgba(0,0,0,0.6);
            position: relative;
            margin: auto; /* Essentiel pour le scroll */
        }

        .brand .logo-icon { font-size: 3.5rem; color: var(--accent-gold); margin-bottom: 20px; }
        .brand h1 { color: white; font-size: 3.5rem; font-weight: 800; letter-spacing: -2px; margin-bottom: 10px; }
        .brand h1 span { color: var(--accent-gold); }
        .brand p { color: #94a3b8; font-size: 1.1rem; margin-bottom: 45px; }

        .selection-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .portal-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 20px;
            padding: 35px 25px;
            text-decoration: none;
            color: white;
            transition: 0.3s;
        }

        .portal-card:hover {
            transform: translateY(-8px);
            border-color: var(--accent-gold);
            background: rgba(255, 255, 255, 0.06);
        }

        .home-footer {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        .btn-secondary {
            text-decoration: none;
            color: var(--accent-gold);
            border: 1px solid var(--accent-gold);
            padding: 12px 35px;
            border-radius: 14px;
            font-weight: 700;
            display: inline-block;
            transition: 0.3s;
        }

        .btn-secondary:hover { background: var(--accent-gold); color: #022c22; }

        @media(max-width:600px){
            .brand h1 { font-size: 2.5rem; }
            body { padding: 40px 15px; }
        }
    </style>
</head>
<body>

    <div class="glass-card">
        <div class="throbber-container">
            <div class="throbber-line"></div>
        </div>

        <div class="brand">
            <i class="fa-solid fa-crown logo-icon"></i>
            <h1>ELOHIM <span>APPUI</span></h1>
            <p>L'excellence académique à portée de clic.</p>
        </div>

        <div class="selection-grid">
            <a href="connexion.php" class="portal-card">
                <i class="fa-solid fa-users-rectangle" style="font-size: 2rem; color: var(--accent-gold); margin-bottom: 15px; display: block;"></i>
                <h3>Espace Parent</h3>
                <p>Accédez aux résultats et bulletins de vos enfants.</p>
            </a>

            <a href="login_staff.php" class="portal-card">
                <i class="fa-solid fa-user-shield" style="font-size: 2rem; color: var(--accent-gold); margin-bottom: 15px; display: block;"></i>
                <h3>Espace Staff</h3>
                <p>Gestion administrative et publication des notes.</p>
            </a>
        </div>

        <div class="home-footer">
            <p style="color: #64748b; margin-bottom: 15px;">Professionnel de l'éducation ?</p>
            <a href="inscription_staff.php" class="btn-secondary">REJOINDRE L'ÉQUIPE</a>
        </div>
    </div>

</body>
</html>