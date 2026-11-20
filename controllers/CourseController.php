<?php
include_once("config.php");
include_once("models/DB.php");
include_once("models/Course.php");
include_once("models/Lecturer.php");
include_once("views/Template.php");
include_once("views/CourseView.php");
include_once("controllers/BaseController.php");

class CourseController extends BaseController
{
  // Properti kontroler
  private $course;
  private $lecturer;

  // Konstruktor
  function __construct()
  {
    $this->course = new Course(Config::$db_host, Config::$db_user, Config::$db_pass, Config::$db_name);
    $this->lecturer = new Lecturer(Config::$db_host, Config::$db_user, Config::$db_pass, Config::$db_name);
  }

  // Halaman utama: daftar mata kuliah (JOIN dosen + prodi) + form tambah/ubah
  public function index($id_edit = null)
  {
    $this->course->open();
    $this->lecturer->open();

    $data = array(
      'course' => array(),
      'lecturer' => array(),
      'edit' => null,
      'flash' => null
    );

    // Data mata kuliah (tabel)
    $this->course->getCourse();
    while ($row = $this->course->getResult()) {
      array_push($data['course'], $row);
    }

    // Data dosen (dropdown Foreign Key)
    $this->lecturer->getLecturer();
    while ($row = $this->lecturer->getResult()) {
      array_push($data['lecturer'], $row);
    }

    // Mode ubah: ambil satu data berdasarkan id
    if ($id_edit !== null) {
      $row = false;
      if ($this->validId($id_edit)) {
        $this->course->getCourseById((int) $id_edit);
        $row = $this->course->getResult();
      }

      if (!$row) {
        $this->course->close();
        $this->lecturer->close();
        $this->setFlash('danger', 'Data mata kuliah tidak ditemukan.');
        $this->redirect('course.php');
      }
      $data['edit'] = $row;
    }

    $this->course->close();
    $this->lecturer->close();

    $data['flash'] = $this->getFlash();

    $view = new CourseView();
    $view->render($data);
  }

  // Mengambil dan membersihkan input form
  private function collect($post)
  {
    return array(
      'code' => strtoupper($this->value($post, 'code')),
      'name' => $this->value($post, 'name'),
      'credits' => $this->value($post, 'credits'),
      'semester' => $this->value($post, 'semester'),
      'lecturer_id' => $this->value($post, 'lecturer_id')
    );
  }

  // Validasi input, mengembalikan pesan error ("" jika valid)
  private function validate($data)
  {
    if (!preg_match('/^[A-Z]{2,4}[0-9]{3}$/', $data['code'])) {
      return "Kode mata kuliah harus berformat 2-4 huruf diikuti 3 angka (contoh: IF305).";
    }
    if ($data['name'] === '' || strlen($data['name']) > 150) {
      return "Nama mata kuliah wajib diisi (maksimal 150 karakter).";
    }
    if (!ctype_digit($data['credits']) || (int) $data['credits'] < 1 || (int) $data['credits'] > 6) {
      return "SKS harus berupa angka 1 sampai 6.";
    }
    if (!ctype_digit($data['semester']) || (int) $data['semester'] < 1 || (int) $data['semester'] > 8) {
      return "Semester harus berupa angka 1 sampai 8.";
    }
    if (!$this->validId($data['lecturer_id'])) {
      return "Dosen pengampu wajib dipilih.";
    }
    return "";
  }

  // Mengubah nilai angka dari string menjadi integer sebelum dikirim ke model
  private function castNumbers($data)
  {
    $data['credits'] = (int) $data['credits'];
    $data['semester'] = (int) $data['semester'];
    $data['lecturer_id'] = (int) $data['lecturer_id'];
    return $data;
  }

  function add($post)
  {
    $data = $this->collect($post);

    $error = $this->validate($data);
    if ($error !== "") {
      $this->setFlash('danger', $error);
      $this->redirect('course.php');
    }
    $data = $this->castNumbers($data);

    $this->course->open();
    $ok = $this->course->add($data);
    $errno = $this->course->getErrno();
    $this->course->close();

    if ($ok) {
      $this->setFlash('success', 'Mata kuliah berhasil ditambahkan.');
    } else {
      $this->setFlash('danger', $this->dbMessage($errno, array(
        1062 => 'Kode mata kuliah sudah digunakan.',
        1452 => 'Dosen pengampu yang dipilih tidak valid.'
      )));
    }
    $this->redirect('course.php');
  }

  function edit($post)
  {
    $id = $this->value($post, 'id');
    if (!$this->validId($id)) {
      $this->setFlash('danger', 'ID mata kuliah tidak valid.');
      $this->redirect('course.php');
    }

    $data = $this->collect($post);

    $error = $this->validate($data);
    if ($error !== "") {
      $this->setFlash('danger', $error);
      $this->redirect('course.php?id_edit=' . (int) $id);
    }
    $data = $this->castNumbers($data);

    $this->course->open();
    $ok = $this->course->update((int) $id, $data);
    $errno = $this->course->getErrno();
    $this->course->close();

    if ($ok) {
      $this->setFlash('success', 'Mata kuliah berhasil diperbarui.');
      $this->redirect('course.php');
    }

    $this->setFlash('danger', $this->dbMessage($errno, array(
      1062 => 'Kode mata kuliah sudah digunakan.',
      1452 => 'Dosen pengampu yang dipilih tidak valid.'
    )));
    $this->redirect('course.php?id_edit=' . (int) $id);
  }

  function delete($id)
  {
    if (!$this->validId($id)) {
      $this->setFlash('danger', 'ID mata kuliah tidak valid.');
      $this->redirect('course.php');
    }

    $this->course->open();
    $ok = $this->course->delete((int) $id);
    $errno = $this->course->getErrno();
    $affected = $this->course->affected_rows;
    $this->course->close();

    if ($ok && $affected > 0) {
      $this->setFlash('success', 'Mata kuliah berhasil dihapus.');
    } else if ($ok) {
      $this->setFlash('danger', 'Data mata kuliah tidak ditemukan.');
    } else {
      $this->setFlash('danger', $this->dbMessage($errno, array()));
    }
    $this->redirect('course.php');
  }
}
