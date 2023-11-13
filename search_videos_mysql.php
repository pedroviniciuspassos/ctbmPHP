<?php
// Informações de conexão com o banco de dados MySQL
$host = 'localhost';
$usuario = 'root';
$senha = '';
$nomeBanco = 'videos_db';

try {
    $conn = new PDO("mysql:host=$host;dbname=$nomeBanco", $usuario, $senha);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verifica se a data foi enviada via POST
    $videosEncontrados = false; // Adiciona a variável para verificar se vídeos foram encontrados

    if (isset($_POST['date'])) {
        $selectedDate = $_POST['date'];

        $stmt = $conn->prepare('SELECT * FROM videos WHERE DATE(data_upload) = :selectedDate');
        $stmt->bindParam(':selectedDate', $selectedDate);
        $stmt->execute();
        $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($videos) > 0) {
            $videosEncontrados = true; // Define como true se vídeos foram encontrados

            foreach ($videos as $video) {
                echo '<div class="video-container">';
                echo '<h3>' . $video['nome'] . '</h3>';
                echo '<video class="video-player" controls id="videoPlayer' . $video['id'] . '">';
                echo '<source src="' . $video['caminho'] . '" type="video/mp4">';
                echo 'Seu navegador não suporta o elemento de vídeo.';
                echo '</video>';
                echo '<p>';
                echo '<a href="download_mysql.php?id=' . $video['id'] . '" class="download-button">Download</a>';
                echo '</p>';
                echo '</div>';
            }
        }
    }

    // Exibe a mensagem somente se uma pesquisa foi realizada e não foram encontrados vídeos
    if (!$videosEncontrados && isset($_POST['date'])) {
        echo 'Nenhum vídeo encontrado para a data selecionada.';
    }
} catch (PDOException $e) {
    echo 'Erro: ' . $e->getMessage();
}
?>
