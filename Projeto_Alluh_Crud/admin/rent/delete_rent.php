<?php
include '../connect&login/connect.php';

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

if (!empty($id)) {
    // Execute a consulta para excluir o registro com base no 'id'.
    $sql = "DELETE FROM rent WHERE id = $id";
    $con->query($sql);

    // Redirecione de volta para a página de CRUD de aluguéis.
    header('Location: crud_rent.php');
    exit;
}
?>
