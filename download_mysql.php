<?php
// Informações de conexão com o banco de dados MySQL
$host = 'localhost';
$usuario = 'root';
$senha = '';
$nomeBanco = 'videos_db';

if (isset($_GET['id'])) {
    try {
        $conn = new PDO("mysql:host=$host;dbname=$nomeBanco", $usuario, $senha);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $videoId = $_GET['id'];

        $stmt = $conn->prepare('SELECT caminho FROM videos WHERE id = :videoId');
        $stmt->bindParam(':videoId', $videoId);
        $stmt->execute();
        $video = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($video) {
            $filePath = $video['caminho'];

            // Envia o arquivo para download
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
            readfile($filePath);
        } else {
            echo 'Vídeo não encontrado.';
        }
    } catch (PDOException $e) {
        echo 'Erro: ' . $e->getMessage();
    }
} else {
    echo 'ID de vídeo inválido.';
}
?>
