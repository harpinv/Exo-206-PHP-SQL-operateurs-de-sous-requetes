<?php

/**
 * Commencez par importer le fichier sql live.sql via PHPMyAdmin.
 * 1. Sélectionnez tous les utilisateurs.
 * 2. Sélectionnez tous les articles.
 * 3. Sélectionnez tous les utilisateurs qui parlent de poterie dans un article.
 * 4. Sélectionnez tous les utilisateurs ayant au moins écrit deux articles.
 * 5. Sélectionnez l'utilisateur Jane uniquement s'il elle a écris un article ( le résultat devrait être vide ! ).
 *
 * ( PS: Sélectionnez, mais affichez le résultat à chaque fois ! ).
 */

$server = 'localhost';
$user = 'root';
$pwd = '';
$db = 'personne';

try {
    $connect = new PDO("mysql:host=$server;dbname=$db;charset=utf8", $user, $pwd);

    $utilisateur = $connect->prepare("
            SELECT email, username, password FROM user
    ");

    $liste = $utilisateur->execute();

    if($liste) {
        foreach ($utilisateur->fetchAll() as $value) {
            echo "<div>Email: " . $value['email'] . ", Nom: " . $value['username'] . ", Mots de passe: " . $value['password'] . "</div>";
        }
    }

    $article = $connect->prepare("
            SELECT titre, contenu FROM article
    ");

    $liste = $article->execute();

    if($liste) {
        foreach ($article->fetchAll() as $value) {
            echo "<div>Titre: " . $value['titre'] . ", Contenu: " . $value['contenu'] . "</div>";
        }
    }

    $utili = $connect->prepare("
            SELECT username FROM user
                WHERE id = ANY (SELECT user_fk FROM article WHERE contenu LIKE '%poterie !')
    ");

    $liste = $utili->execute();

    if($liste) {
        foreach ($utili->fetchAll() as $value) {
            echo "<div>" . $value['username'] . " parle de poterie dans un article</div>";
        }
    }

    $ecrit = $connect->prepare("
            SELECT username FROM user
                WHERE EXISTS (SELECT * FROM article WHERE article.user_fk = user.id)
    ");

    $liste = $ecrit->execute();

    if($liste) {
        foreach ($ecrit->fetchAll() as $value) {
            echo "<div>" . $value['username'] . " a écrit deux article</div>";
        }
    }

    $ecriture = $connect->prepare("
            SELECT username FROM user 
                WHERE EXISTS (SELECT * FROM article WHERE article.user_fk = user.id) LIKE 'jane%'
    ");

    $liste = $ecriture->execute();

    if($liste) {
        foreach ($ecriture->fetchAll() as $value) {
            echo "<div>" . $value['username'] . " a écrit deux article</div>";
        }
    }
}
catch (PDOException $exception) {
    echo "Erreur de connexion: " . $exception->getMessage();
}