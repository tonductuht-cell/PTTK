<?php
require_once __DIR__ . '/DAO.php';
require_once __DIR__ . '/../model/User8.php';

class UserDAO8 extends DAO {
    public function checkLogin(string $username, string $password): ?User8 {
        $stmt = $this->getConnection()->prepare(
            "SELECT * FROM users WHERE username = ? AND password = ? LIMIT 1"
        );
        $stmt->execute([$username, $password]);
        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }

        return new User8(
            $row['id'],
            $row['username'],
            null,
            $row['role'],
            $row['name'],
            $row['phone'],
            $row['email'],
            $row['address']
        );
    }

    public function getUserById(string $id): ?User8 {
        $stmt = $this->getConnection()->prepare(
            "SELECT * FROM users WHERE id = ? LIMIT 1"
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }

        return new User8(
            $row['id'],
            $row['username'],
            null,
            $row['role'],
            $row['name'],
            $row['phone'],
            $row['email'],
            $row['address']
        );
    }
}
