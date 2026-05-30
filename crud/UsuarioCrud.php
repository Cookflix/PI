<?php
// crud/UsuarioCrud.php

class UsuarioCrud
{
  private PDO $pdo;

  public function __construct(PDO $pdo)
  {
    $this->pdo = $pdo;
  }

  public function cadastrar(Usuario $u)
  {
    // verificar email
    $sql = $this->pdo->prepare(
      "SELECT id FROM usuario WHERE email = :e"
    );

    $sql->bindValue(":e", $u->getEmail());
    $sql->execute();

    if ($sql->rowCount() > 0) {
      return false;
    }

    try {
      $this->pdo->beginTransaction();

      // cadastrar usuario
      $sql = $this->pdo->prepare(
        "INSERT INTO usuario (nome, email, senha, tipo) VALUES (:n, :e, :s, :t)"
      );

      $sql->bindValue(":n", $u->getNome());
      $sql->bindValue(":e", $u->getEmail());
      $sql->bindValue(":s", $u->getSenha());
      $sql->bindValue(":t", $u->getTipo());
      $sql->execute();

      $idUsuario = (int) $this->pdo->lastInsertId();

      if ($u->getTipo() === 'CULINARISTA') {
        $sql = $this->pdo->prepare(
          "INSERT INTO culinarista (id_culin) VALUES (:id)"
        );
        $sql->bindValue(":id", $idUsuario, PDO::PARAM_INT);
        $sql->execute();
      } else if ($u->getTipo() === 'CLIENTE') {
        $sql = $this->pdo->prepare(
          "INSERT INTO cliente (id_usuario, id_culin) VALUES (:id, NULL)"
        );
        $sql->bindValue(":id", $idUsuario, PDO::PARAM_INT);
        $sql->execute();
      }

      $this->pdo->commit();
      return true;
    } catch (Throwable $e) {
      if ($this->pdo->inTransaction()) {
        $this->pdo->rollBack();
      }

      throw $e;
    }
  }

  public function buscarPorEmail(string $email)
  {
    $sql = $this->pdo->prepare(
      "SELECT * FROM usuario WHERE email = :e"
    );

    $sql->bindValue(":e", $email);
    $sql->execute();

    return $sql->fetch(PDO::FETCH_ASSOC);
  }

  public function atualizarSenha(int $id, string $senhaCriptografada)
  {
    $sql = $this->pdo->prepare(
      "UPDATE usuario SET senha = :s WHERE id = :id"
    );

    $sql->bindValue(":s", $senhaCriptografada);
    $sql->bindValue(":id", $id, PDO::PARAM_INT);

    return $sql->execute();
  }

  public function listar()
  {
    $sql = $this->pdo->query(
      "SELECT * FROM usuario"
    );

    return $sql->fetchAll(PDO::FETCH_ASSOC);
  }
}
