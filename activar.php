<?php
include 'components/connect.php';

session_start();

if(isset($_GET['activation_hash'])) {
    $activationHash = $_GET['activation_hash'];
    echo "Activation Hash recibido: " . $activationHash; 
    
    $select_user = $conn->prepare("SELECT * FROM `users` WHERE activation_hash = ? AND is_active = 0");
    $select_user->execute([$activationHash]);
    $user = $select_user->fetch(PDO::FETCH_ASSOC);
    
    if($select_user->rowCount() > 0) {
        $update_user = $conn->prepare("UPDATE `users` SET is_active = 1 WHERE activation_hash = ?");
        $update_user->execute([$activationHash]);
        
        header('location: /home.php');
        exit;
    } else {
        header('location: ../public/register_public.php');
        exit;
        echo "Código de activación inválido";
    }
} else {
    echo "Código de activación no proporcionado";
}
?>
