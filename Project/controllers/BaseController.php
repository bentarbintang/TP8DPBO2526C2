<?php

// Kelas dasar controller: helper umum (flash message, redirect, sanitasi input).
// Controller tiap entitas mewarisi kelas ini; tidak ada query SQL di sini.
class BaseController
{
  // Mengambil nilai input POST sebagai string yang sudah di-trim
  protected function value($post, $key)
  {
    if (isset($post[$key]) && is_scalar($post[$key])) {
      return trim((string) $post[$key]);
    }
    return '';
  }

  // ID valid = bilangan bulat positif
  protected function validId($id)
  {
    return is_scalar($id) && ctype_digit((string) $id) && (int) $id > 0;
  }

  // Menyimpan pesan sementara (ditampilkan sekali setelah redirect)
  protected function setFlash($type, $message)
  {
    $_SESSION['flash'] = array('type' => $type, 'message' => $message);
  }

  protected function getFlash()
  {
    if (isset($_SESSION['flash'])) {
      $flash = $_SESSION['flash'];
      unset($_SESSION['flash']);
      return $flash;
    }
    return null;
  }

  protected function redirect($page)
  {
    header("Location: " . $page);
    exit;
  }

  // Mengubah kode error MySQL menjadi pesan yang mudah dipahami
  // 1062 = data duplikat (UNIQUE), 1451 = masih dipakai tabel lain (FK RESTRICT),
  // 1452 = nilai FK tidak ada di tabel induk
  protected function dbMessage($errno, $messages)
  {
    if (isset($messages[$errno])) {
      return $messages[$errno];
    }
    return "Terjadi kesalahan pada database (kode " . (int) $errno . ").";
  }
}
