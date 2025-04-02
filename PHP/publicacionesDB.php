<?php
// publicacionesDB.php - Functions for post queries and pagination

/**
 * Obtiene los detalles de un hilo por su ID
 */
function obtenerDetallesHilo($db, $idHilo) {
    $sqlHilo = 'SELECT H.*, T.nomVideojoc, T.imagen, U.username as creador 
                FROM Hilo H 
                JOIN Tema T ON H.nomVideojoc = T.nomVideojoc 
                JOIN Users U ON H.iduser = U.iduser 
                WHERE H.idHilo = :idHilo';
    $preparadaHilo = $db->prepare($sqlHilo);
    $preparadaHilo->bindParam(':idHilo', $idHilo);
    $preparadaHilo->execute();
    return $preparadaHilo->fetch(PDO::FETCH_ASSOC);
}

/**
 * Insertar una nueva publicación en un hilo
 */
function insertarPublicacion($db, $contenido, $idHilo, $iduser) {
    $sqlInsert = "INSERT INTO Publicacio (Contingut, dataPub, idHilo, iduser) 
                 VALUES (:contenido, CURDATE(), :idHilo, :iduser)";
    $prepInsert = $db->prepare($sqlInsert);
    $prepInsert->bindParam(':contenido', $contenido);
    $prepInsert->bindParam(':idHilo', $idHilo);
    $prepInsert->bindParam(':iduser', $iduser);
    return $prepInsert->execute();
}

/**
 * Eliminar una publicación y opcionalmente el hilo completo si es la primera publicación
 */
function eliminarPublicacion($db, $idPublicacion, $idHilo, $iduser) {
    // Verificar si es el primer post y el usuario actual es el creador del hilo
    $sqlVerificarOP = "SELECT P.idPublicacio, P.iduser, H.iduser as hilo_creador, H.nomVideojoc
                      FROM Publicacio P
                      JOIN Hilo H ON P.idHilo = H.idHilo
                      WHERE P.idHilo = :idHilo
                      ORDER BY P.dataPub ASC, P.idPublicacio ASC
                      LIMIT 1";
    $prepVerificarOP = $db->prepare($sqlVerificarOP);
    $prepVerificarOP->bindParam(':idHilo', $idHilo);
    $prepVerificarOP->execute();
    $primerPost = $prepVerificarOP->fetch(PDO::FETCH_ASSOC);
    
    // Verificar si el usuario es dueño de la publicación
    $sqlVerificar = "SELECT iduser FROM Publicacio WHERE idPublicacio = :idPublicacion";
    $prepVerificar = $db->prepare($sqlVerificar);
    $prepVerificar->bindParam(':idPublicacion', $idPublicacion);
    $prepVerificar->execute();
    $publicacionData = $prepVerificar->fetch(PDO::FETCH_ASSOC);
    
    if (!$publicacionData || $publicacionData['iduser'] != $iduser) {
        return false;
    }
    
    // Si es el primer post y el usuario es el creador del hilo
    if ($primerPost && $primerPost['idPublicacio'] == $idPublicacion && $primerPost['hilo_creador'] == $iduser) {
        try {
            $db->beginTransaction();
            
            // Eliminar todas las publicaciones del hilo
            $sqlEliminarPubs = "DELETE FROM Publicacio WHERE idHilo = :idHilo";
            $prepEliminarPubs = $db->prepare($sqlEliminarPubs);
            $prepEliminarPubs->bindParam(':idHilo', $idHilo);
            $prepEliminarPubs->execute();
            
            // Eliminar el hilo
            $sqlEliminarHilo = "DELETE FROM Hilo WHERE idHilo = :idHilo";
            $prepEliminarHilo = $db->prepare($sqlEliminarHilo);
            $prepEliminarHilo->bindParam(':idHilo', $idHilo);
            $prepEliminarHilo->execute();
            
            $db->commit();
            return ['success' => true, 'deleted_thread' => true, 'tema' => $primerPost['nomVideojoc']];
        } catch (Exception $e) {
            $db->rollBack();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    } else {
        // Si no es el primer post, solo eliminar la publicación
        $sqlEliminar = "DELETE FROM Publicacio WHERE idPublicacio = :idPublicacion";
        $prepEliminar = $db->prepare($sqlEliminar);
        $prepEliminar->bindParam(':idPublicacion', $idPublicacion);
        
        if ($prepEliminar->execute()) {
            return ['success' => true, 'deleted_thread' => false];
        } else {
            return ['success' => false];
        }
    }
}

/**
 * Obtener el total de publicaciones de un hilo
 */
function contarPublicaciones($db, $idHilo) {
    $sqlTotal = "SELECT COUNT(*) FROM Publicacio WHERE idHilo = :idHilo";
    $prepTotal = $db->prepare($sqlTotal);
    $prepTotal->bindParam(':idHilo', $idHilo);
    $prepTotal->execute();
    return $prepTotal->fetchColumn();
}

/**
 * Obtener publicaciones paginadas de un hilo
 */
function obtenerPublicacionesPaginadas($db, $idHilo, $offset, $limit) {
    $sqlPublicaciones = "SELECT P.*, U.username, U.profile_image 
                         FROM Publicacio P
                         JOIN Users U ON P.iduser = U.iduser
                         WHERE P.idHilo = :idHilo
                         ORDER BY P.dataPub ASC, P.idPublicacio ASC
                         LIMIT :offset, :limit";
    $prepPublicaciones = $db->prepare($sqlPublicaciones);
    $prepPublicaciones->bindParam(':idHilo', $idHilo);
    $prepPublicaciones->bindParam(':offset', $offset, PDO::PARAM_INT);
    $prepPublicaciones->bindParam(':limit', $limit, PDO::PARAM_INT);
    $prepPublicaciones->execute();
    return $prepPublicaciones->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Obtener el ID de la primera publicación del hilo (para marcar como OP)
 */
function obtenerPrimerPostId($db, $idHilo) {
    $sqlPrimerPost = "SELECT idPublicacio FROM Publicacio 
                      WHERE idHilo = :idHilo 
                      ORDER BY dataPub ASC, idPublicacio ASC 
                      LIMIT 1";
    $prepPrimerPost = $db->prepare($sqlPrimerPost);
    $prepPrimerPost->bindParam(':idHilo', $idHilo);
    $prepPrimerPost->execute();
    return $prepPrimerPost->fetchColumn();
}

/**
 * Obtener hilos relacionados con el mismo tema
 */
function obtenerHilosRelacionados($db, $nomVideojoc, $idHilo) {
    $sqlRelacionados = "SELECT H.idHilo, H.Titol
                        FROM Hilo H
                        WHERE H.nomVideojoc = :nomVideojoc
                        AND H.idHilo != :idHilo
                        ORDER BY RAND()
                        LIMIT 5";
    $prepRelacionados = $db->prepare($sqlRelacionados);
    $prepRelacionados->bindParam(':nomVideojoc', $nomVideojoc);
    $prepRelacionados->bindParam(':idHilo', $idHilo);
    $prepRelacionados->execute();
    return $prepRelacionados->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Obtener usuarios activos en el hilo
 */
function obtenerUsuariosActivos($db, $idHilo) {
    $sqlUsuarios = "SELECT DISTINCT U.iduser, U.username, U.profile_image
                    FROM Publicacio P
                    JOIN Users U ON P.iduser = U.iduser
                    WHERE P.idHilo = :idHilo
                    LIMIT 5";
    $prepUsuarios = $db->prepare($sqlUsuarios);
    $prepUsuarios->bindParam(':idHilo', $idHilo);
    $prepUsuarios->execute();
    return $prepUsuarios->fetchAll(PDO::FETCH_ASSOC);
}
?>