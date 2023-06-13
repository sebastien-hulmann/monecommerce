<?php
require_once("../inc/init.inc.php");
//----- TRAITEMENT PHP -----//
//----- VERIFICATION ADMIN -----//
if (!internauteEstConnecteEtEstAdmin()) {
    header("location:../connexion.php");
    exit();
}
//----- SUPPRESSION PRODUIT -----//
if (isset($_GET['action']) && $_GET['action'] == "suppression") {
    $resultat = executeRequete("SELECT * FROM produit WHERE id_produit=$_GET[id_produit]");
    $produit_a_supprimer = $resultat->fetch_assoc();

    // Vérifier si le produit est en commande
    $resultat_commande = executeRequete("SELECT COUNT(*) AS nb_commandes FROM details_commande WHERE id_produit=$_GET[id_produit]");
    $donnees_commande = $resultat_commande->fetch_assoc();
    $nb_commandes = $donnees_commande['nb_commandes'];

    if ($nb_commandes > 0) {
        // Le produit est en commande, ne pas supprimer l'image
        $contenu .= '<div class="erreur">La suppression du produit : ' . $_GET['id_produit'] .
            ' est impossible => commande en cours ...</div>';
        $_GET['action'] = 'affichage';
    } else {
        // Le produit n'est pas en commande, supprimer l'image
        $chemin_photo_a_supprimer = str_replace(RACINE_SITE, "../", "$produit_a_supprimer[photo]");
        if (!empty($produit_a_supprimer['photo']) && file_exists($chemin_photo_a_supprimer))
            unlink($chemin_photo_a_supprimer);
        // Supprimer le produit de la base de données
        executeRequete("DELETE FROM produit WHERE id_produit=$_GET[id_produit]");
        $contenu .= '<div class="validation">Suppression du produit : ' . $_GET['id_produit'] . '</div>';
        $_GET['action'] = 'affichage';
    }
}

//----- ENREGISTREMENT PRODUIT -----//
if (!empty($_POST)) { // debug($_POST);
    $photo_bdd = "";
    if (isset($_GET['action']) && $_GET['action'] == 'modification') {
        $photo_bdd = $_POST['photo_actuelle'];
    }
    if (!empty($_FILES['photo']['name'])) { // debug($_FILES);
        $nom_photo = $_POST['reference'] . '_' . $_FILES['photo']['name'];
        $photo_bdd = RACINE_SITE . "public/img/$nom_photo";
        $photo_dossier = "../public/img/$nom_photo";
        copy($_FILES['photo']['tmp_name'], $photo_dossier);
    }
    foreach ($_POST as $indice => $valeur) {
        $_POST[$indice] = htmlEntities(addSlashes($valeur));
    }
    $id_produit = intval($_POST['id_produit']);
    executeRequete("REPLACE INTO produit(id_produit, reference, categorie, titre, description, couleur, taille, public, photo, prix, stock)
    values ('$id_produit','$_POST[reference]', '$_POST[categorie]', '$_POST[titre]', '$_POST[description]', '$_POST[couleur]',
     '$_POST[taille]', '$_POST[public]', '$photo_bdd', '$_POST[prix]', '$_POST[stock]')");
    $contenu .= '<div class="validation">Le produit a été ajouté</div>';
}
//------ LIENS PRODUITS ------//
$contenu .= '<a href="?action=affichage">Affichage des produits</a><br>';
$contenu .= '<a href="?action=ajout">Ajout d\'un produit</a><br><hr><br>';
//----- AFFICHAGE PRODUITS ----//
if (isset($_GET['action']) && $_GET['action'] == "affichage") {
    $resultat = executeRequete("SELECT * FROM produit");
    $contenu .= '<h2> Affichage des produits</h2>';
    $contenu .= 'Nombre de produit(s) dans la boutique : ' .
        $resultat->num_rows;
    $contenu .= '<table><tr>';

    while ($colonne = $resultat->fetch_field()) {
        $contenu .= '<th>' . $colonne->name . '</th>';
    }
    $contenu .= '<th>Modification</th>';
    $contenu .= '<th>Suppression</th>';
    $contenu .= '</tr>';
    while ($ligne = $resultat->fetch_assoc()) {
        $contenu .= '<tr>';
        foreach ($ligne as $indice => $information) {
            if ($indice == "photo") {
                $contenu .= '<td><img src="' . $information . '" ="70" height="70"></td>';
            } else {
                $contenu .= '<td>' . $information . '</td>';
            }
        }
        $contenu .= '<td><a href="?action=modification&id_produit=' . $ligne['id_produit'] .
            '"><img src="../inc/assets/icons/edit.png"></a></td>';
        $contenu .= '<td><a href="?action=suppression&id_produit=' . $ligne['id_produit'] .
            '" OnClick="return(confirm(\'En êtes-vous certain ?\'));"><img src="../inc/assets/icons/delete.png"></a></td>';
        $contenu .= '</tr>';
    }
    $contenu .= '</table><br><hr><br>';
}

//-----AFFICHAGE HTML -------//
require_once("../inc/haut.inc.php");
echo $contenu;
if (isset($_GET['action']) && ($_GET['action'] == 'ajout' || $_GET['action'] == 'modification')) {
    if (isset($_GET['id_produit'])) {
        $resultat = executeRequete("SELECT * FROM produit WHERE id_produit=$_GET[id_produit]");
        $produit_actuel = $resultat->fetch_assoc();
    }
    echo '
<h1> Formulaire Produits </h1><br>
<form method="post" enctype="multipart/form-data" action="">
<input type="hidden" id="id_produit" name="id_produit" value="';
    if (isset($produit_actuel['id_produit']))
        echo
            $produit_actuel['id_produit'];
    echo '">

    <label for="reference">Référence</label><br>
    <input type="text" id="reference" name="reference" placeholder="Référence du produit" value="';
    if (isset($produit_actuel['reference']))
        echo
            $produit_actuel['reference'];
    echo '"> <br><br>

    <label for="categorie">Catégorie</label><br>
    <input type="text" id="categorie" name="categorie" placeholder="Catégorie du produit"value="';
    if (isset($produit_actuel['categorie']))
        echo
            $produit_actuel['categorie'];
    echo '"> <br><br>

    <label for="titre">Titre</label><br>
    <input type="text" id="titre" name="titre" placeholder="Titre du produit"value="';
    if (isset($produit_actuel['titre']))
        echo
            $produit_actuel['titre'];
    echo '"> <br><br>

    <label for="description">Description</label><br>
    <textarea name="description" id="description" placeholder="Description du produit"> ';
    if (isset($produit_actuel['description']))
        echo
            $produit_actuel['description'];
    echo '</textarea> <br><br>

    <label for="couleur">Couleur</label><br>
    <input type="text" id="couleur" name="couleur" placeholder="Couleur du produit"value="';
    if (isset($produit_actuel['couleur']))
        echo
            $produit_actuel['couleur'];
    echo '"> <br><br>

    <label for="taille">Taille</label><br>
    <select name="taille">';
    $options = ["S", "M", "L", "XL"];
    foreach ($options as $taille) {
        echo '<option value="' . $taille . '"';
        if (isset($produit_actuel) && $produit_actuel['taille'] == $taille) {
            echo ' selected';
        }
        echo '>' . $taille . '</option>';
    }
    echo '
    </select><br><br>
    
    <label for="public">Public</label><br>
    <input type="radio" name="public" value="m" ';
    if (isset($produit_actuel) && $produit_actuel['public'] == 'm')
        echo ' checked ';
    elseif (!isset($produit_actuel) && !isset($_POST['public']))
        echo 'checked';
    echo '>Homme

    <input type="radio" name="public" value="f" ';
    if (isset($produit_actuel) && $produit_actuel['public'] == 'f')
        echo ' checked ';
    elseif (!isset($produit_actuel) && !isset($_POST['public']))
        echo 'checked';
    echo '>Femme 
    <br><br>

    <label for="photo">Photo</label><br>
    <input type="file" id="photo" name="photo" onchange="previewImage()"><br><br>';
    if (isset($produit_actuel)) {
        echo '<i>Vous pouvez uploader une nouvelle photo si vous souhaitez la changer</i><br>';
        echo '<img id="preview" src="' . $produit_actuel['photo'] . '"><br>';
        echo '<input type="hidden" name="photo_actuelle" value="' . $produit_actuel['photo'] . '"><br>';
    }
    echo '

    <label for="prix">Prix</label><br>
    <input type="text" id="prix" name="prix" placeholder="Prix du produit"value="';
    if (isset($produit_actuel['prix']))
        echo
            $produit_actuel['prix'];
    echo '"> <br><br>

    <label for="stock">Stock</label><br>
    <input type="text" id="stock" name="stock" placeholder="Stock du produit"value="';
    if (isset($produit_actuel['stock']))
        echo
            $produit_actuel['stock'];
    echo '"> <br><br>

    <input type="submit" value="';
    echo ucfirst($_GET['action']) . ' du produit ">
</form>
    ';
}
require_once("../inc/bas.inc.php"); ?>