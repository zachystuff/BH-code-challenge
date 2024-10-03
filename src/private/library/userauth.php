<?php

namespace AS\library;
include_once(__DIR__ . '/../library/maindb.php');

use AS\library\maindb;
use PDO;

class userauth {
    /**
     * Signup. Never store plaintext password in DB
     *
     * @param string $password
     * @return array
     */
    static function signup(string $email, string $password, string $username): array {
        if (empty($email) || empty($password) || empty($username) || self::creds_in_use($email, $username)) {
            return ['result' => 'error'];
        }
        $password_hash = md5($password);
        $db = new maindb;
        $query = $db->prepare('INSERT INTO users (email, password, username) VALUES (:email, :password_hash, :username) RETURNING id');
        $query->execute([':email' => $email, ':password_hash' => $password_hash, ':username' => $username]);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return ['result' => 'success', 'user_id' => $result['id']];
    }

    static function login(string $password, string $username): array {
        if (empty($username) || empty($password)) {
            return ['result' => 'error'];
        }
        $db = new maindb;
        $query = $db->prepare('SELECT password, id FROM users WHERE username = :username');
        $query->execute([':username' => $username]);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        if (!empty($result) && md5($password) === $result['password']) {
            return ['result' => 'success', 'user_id' => $result['id']];
        } else {
            return ['result' => 'error'];
        };
    }

    static function logout(): void {
        session_destroy();
    }

    static function creds_in_use(string $email, string $username): bool {
        $db = new maindb;
        $query = $db->prepare('SELECT 1 FROM users WHERE email = :email OR username = :username');
        $query->execute([':email' => $email, ':username' => $username]);
        $result = $query->fetchColumn();

        return !empty($result);
    }

    static function is_username_used(string $username): bool {
        $db = new maindb;
        $query = $db->prepare('SELECT 1 FROM users WHERE username = :username');
        $query->execute([':username' => $username]);
        $result = $query->fetchColumn();

        return !empty($result);
    }

    static function is_email_used(string $email): bool {
        $db = new maindb;
        $query = $db->prepare('SELECT 1 FROM users WHERE email = :email');
        $query->execute([':email' => $email]);
        $result = $query->fetchColumn();

        return !empty($result);
    }
}