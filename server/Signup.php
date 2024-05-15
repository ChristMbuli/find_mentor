<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inclure les fichiers de connexion à la base de données et la fonction de génération d'ID
$dbPath = __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'db_connect.php';
$generatePath = __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'generate_id.php';

// Inclure les fichiers
include $dbPath;
include $generatePath;

if (isset($_POST['register'])) {

    if (!empty($_POST['fname']) && !empty($_POST['email']) && !empty($_POST['mdp']) && !empty($_POST['roles'])) {

        // Stocker les données entrées dans les variables
        $name = htmlspecialchars($_POST['fname']);
        $email = htmlspecialchars($_POST['email']);
        $role = htmlspecialchars($_POST['roles']);
        $mdp = password_hash($_POST['mdp'], PASSWORD_DEFAULT);

        $date = date("Y-m-d H:i:s");
        $userId = generateUniqueId();
        $online = 0;

        // Vérifier si l'utilisateur existe déjà dans la base de données
        $stmt_check = $conn->prepare('SELECT COUNT(*) FROM users WHERE fname = ? AND email = ?');
        $stmt_check->execute([$name, $email]);
        $count = $stmt_check->fetchColumn();

        if ($count > 0) {
            $msgError = "Ses informations existe déjà !";
        } else {

            // Préparer et exécuter la requête SQL pour insérer les données dans la base de données
            $stmt_insert = $conn->prepare('INSERT INTO users (id, fname, email, password, roles, online_statuts, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)');

            $stmt_insert->bindValue(1, $userId, PDO::PARAM_STR);
            $stmt_insert->bindValue(2, $name, PDO::PARAM_STR);
            $stmt_insert->bindValue(3, $email, PDO::PARAM_STR);
            $stmt_insert->bindValue(4, $mdp, PDO::PARAM_STR);
            $stmt_insert->bindValue(5, $role, PDO::PARAM_STR);
            $stmt_insert->bindValue(6, $online, PDO::PARAM_INT);
            $stmt_insert->bindValue(7, $date, PDO::PARAM_STR);

            // Exécuter la requête préparée
            if ($stmt_insert->execute()) {
                // Récupérer l'ID de l'utilisateur nouvellement inséré
                $lastInsertId = $conn->lastInsertId();

                // Vérifier le rôle de l'utilisateur et insérer dans la table appropriée
                if ($role === 'student') {
                    // Si le rôle est étudiant, insérer dans la table student
                    $stmt_insert_student = $conn->prepare('INSERT INTO students (user_id) VALUES (?)');
                    $stmt_insert_student->execute([$userId]);
                } elseif ($role === 'mentor') {
                    // Si le rôle est mentor, insérer dans la table mentors
                    $stmt_insert_mentor = $conn->prepare('INSERT INTO mentors (user_id) VALUES (?)');
                    $stmt_insert_mentor->execute([$userId]);
                } else {
                    // Si le rôle n'est ni étudiant ni mentor, afficher un message d'erreur
                    $msgError = "Veuillez sélectionner un rôle valide.";
                }

                // Rediriger vers la page de connexion après l'inscription réussie
                header('Location: signin');
            } else {
                $msgError = "Une erreur s'est produite lors de l'inscription";
            }

            // Fermer la requête préparée
            $stmt_insert->closeCursor();

        }
    } else {
        $msgError = "Completez tous les champs...";
    }
}