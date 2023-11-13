<?php
// Informações de conexão com o banco de dados MySQL
$host = 'localhost';
$usuario = 'root';
$senha = '';
$nomeBanco = 'videos_db';

try {
    $conn = new PDO("mysql:host=$host;dbname=$nomeBanco", $usuario, $senha);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Criar tabela para armazenar informações dos vídeos, se ainda não existir
    $conn->exec('
        CREATE TABLE IF NOT EXISTS videos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(255) NOT NULL,
            caminho VARCHAR(255) NOT NULL,
            data_upload TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ');

    // Pasta onde os vídeos estão localizados
    $pastaVideos = 'videos';

    // Listar arquivos na pasta
    $arquivos = scandir($pastaVideos);

    // Filtrar apenas os arquivos novos (não existentes no banco de dados)
    $novosArquivos = array_diff($arquivos, obterNomesArquivosNoBanco($conn));

    // Inserir informações dos vídeos na tabela para arquivos novos
    foreach ($novosArquivos as $arquivo) {
        if ($arquivo !== '.' && $arquivo !== '..') {
            $caminhoCompleto = $pastaVideos . '/' . $arquivo;
            if (is_file($caminhoCompleto)) {
                $stmt = $conn->prepare('INSERT INTO videos (nome, caminho) VALUES (:nome, :caminho)');
                $stmt->bindParam(':nome', $arquivo);
                $stmt->bindParam(':caminho', $caminhoCompleto);
                $stmt->execute();
            }
        }
    }

    echo 'Upload de vídeos concluído com sucesso!';
} catch (PDOException $e) {
    echo 'Erro: ' . $e->getMessage();
}

function obterNomesArquivosNoBanco($conn) {
    $stmt = $conn->query('SELECT nome FROM videos');
    $nomesArquivos = $stmt->fetchAll(PDO::FETCH_COLUMN);
    return $nomesArquivos;
}
?>
