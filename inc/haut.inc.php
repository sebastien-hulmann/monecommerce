<!Doctype html>
<html>

<head>
    <title>Mon Site</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?php echo RACINE_SITE; ?>inc/css/index.css">
    <script src="<?php echo RACINE_SITE ?>inc/js/main.js"></script>
</head>

<body>
    <header>
        <div class="">
            <nav class="topnav" id="myTopNav">
                <div>
                    <a class='title' href="<?php echo RACINE_SITE; ?>index.php" title="Mon Site">MonSite.com</a>
                </div>
                <a href="<?php echo RACINE_SITE; ?>inscription.php">Inscription</a>
                <a href="<?php echo RACINE_SITE; ?>connexion.php">Connexion</a>
                <a href="<?php echo RACINE_SITE; ?>boutique.php">Accès à la boutique</a>
                <a href="<?php echo RACINE_SITE; ?>panier.php">Voir votre panier</a>
                <a href="javascript:void(0);" class="icon" onclick="toggleNav()">
                    <i class="fa fa-bars"></i>
                </a>
            </nav>
        </div>
    </header>
    <section>
        <div class="conteneur">