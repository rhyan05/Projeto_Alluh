<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('location: ../connect&login/login.php');
    exit;
}

include '../connect&login/connect.php';

// Verifique se os parâmetros necessários estão definidos
if (isset($_GET['image_id']) && isset($_GET['user_id']) && isset($_GET['casa_id'])) {
    $imageId = $_GET['image_id'];
    $userId = $_GET['user_id'];
    $casaId = $_GET['casa_id'];

    // Verifique se a imagem pertence ao usuário e à casa especificada
    $checkImageSql = "SELECT nome_imagem FROM imagens WHERE id = ? AND usuario_id = ? AND casa_id = ?";
    $stmt = $con->prepare($checkImageSql);

    if ($stmt) {
        $stmt->bind_param("iii", $imageId, $userId, $casaId);
        $stmt->execute();
        $imageResult = $stmt->get_result();

        if ($imageResult->num_rows > 0) {
            $imageRow = $imageResult->fetch_assoc();
            $imageName = $imageRow['nome_imagem'];

            // Caminho da imagem no servidor
            $imagePath = '../admin/imagens/' . $userId . '/' . $imageName;

            // Verifique se o arquivo realmente existe antes de excluí-lo
            if (file_exists($imagePath)) {
                if (unlink($imagePath)) {
                    // Exclua a entrada do banco de dados
                    $deleteImageSql = "DELETE FROM imagens WHERE id = ? AND usuario_id = ? AND casa_id = ?";
                    $stmt = $con->prepare($deleteImageSql);

                    if ($stmt) {
                        $stmt->bind_param("iii", $imageId, $userId, $casaId);
                        if ($stmt->execute()) {
                            echo 'success'; // Retorna 'success' em caso de sucesso
                        } else {
                            echo 'Erro ao excluir a imagem do banco de dados.';
                        }
                    } else {
                        echo 'Erro ao preparar a declaração de exclusão.';
                    }
                } else {
                    echo 'Erro ao excluir a imagem do servidor.';
                }
            } else {
                echo 'Imagem não encontrada no servidor.';
            }
        } else {
            echo 'Imagem não encontrada no banco de dados ou não pertence ao usuário ou casa especificados.';
        }
    } else {
        echo 'Erro ao preparar a declaração de consulta.';
    }
} else {
    header('location: home_vendedor.php');
    exit;
}
?>
