<?php
include_once("config.php");
include_once("models/DB.php");
include_once("models/Lecturer.php");
include_once("models/Department.php");
include_once("views/Template.php");
include_once("views/LecturerView.php");
include_once("controllers/BaseController.php");

class LecturerController extends BaseController
{
  // Properti kontroler
  private $lecturer;
  private $department;

  // Konstruktor
  function __construct()
  {
    $this->lecturer = new Lecturer(Config::$db_host, Config::$db_user, Config::$db_pass, Config::$db_name);
    $this->department = new Department(Config::$db_host, Config::$db_user, Config::$db_pass, Config::$db_name);
  }

  // Halaman utama: daftar dosen (JOIN prodi) + form tambah/ubah
  public function index($id_edit = null)
  {
    // Membuka jalur ke database
    $this->lecturer->open();
    $this->department->open();

    $data = array(
      'lecturer' => array(),
      'department' => array(),
      'ranks' => Lecturer::$ranks,
      'edit' => null,
      'flash' => null
    );

    // Data dosen (tabel)
    $this->lecturer->getLecturer();
    while ($row = $this->lecturer->getResult()) {
      array_push($data['lecturer'], $row);
    }

    // Data prodi (dropdown Foreign Key)
    $this->department->getDepartment();
    while ($row = $this->department->getResult()) {
      array_push($data['department'], $row);
    }

    // Mode ubah: ambil satu data berdasarkan id
    if ($id_edit !== null) {
      $row = false;
      if ($this->validId($id_edit)) {
        $this->lecturer->getLecturerById((int) $id_edit);
        $row = $this->lecturer->getResult();
      }

      if (!$row) {
        $this->lecturer->close();
        $this->department->close();
        $this->setFlash('danger', 'Data dosen tidak ditemukan.');
        $this->redirect('index.php');
      }
      $data['edit'] = $row;
    }

    // Menutup jalur
    $this->lecturer->close();
    $this->department->close();

    $data['flash'] = $this->getFlash();

    $view = new LecturerView();
    $view->render($data);
  }

  // Mengambil dan membersihkan input form
  private function collect($post)
  {
    return array(
      'name' => $this->value($post, 'name'),
      'nidn' => $this->value($post, 'nidn'),
      'email' => $this->value($post, 'email'),
      'phone' => $this->value($post, 'phone'),
      'academic_rank' => $this->value($post, 'academic_rank'),
      'join_date' => $this->value($post, 'join_date'),
      'department_id' => $this->value($post, 'department_id')
    );
  }

  // Validasi input, mengembalikan pesan error ("" jika valid)
  private function validate($data)
  {
    if ($data['name'] === '' || strlen($data['name']) > 100) {
      return "Nama dosen wajib diisi (maksimal 100 karakter).";
    }
    if (!preg_match('/^[0-9]{10}$/', $data['nidn'])) {
      return "NIDN harus terdiri dari 10 digit angka.";
    }
    if (strlen($data['email']) > 100 || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
      return "Format email tidak valid.";
    }
    if (!preg_match('/^[0-9+\- ]{8,20}$/', $data['phone'])) {
      return "No. telepon harus 8-20 karakter (angka, +, - atau spasi).";
    }
    if (!in_array($data['academic_rank'], Lecturer::$ranks, true)) {
      return "Jabatan akademik tidak valid.";
    }

    $date = DateTime::createFromFormat('Y-m-d', $data['join_date']);
    if (!$date || $date->format('Y-m-d') !== $data['join_date']) {
      return "Tanggal bergabung tidak valid.";
    }

    if (!$this->validId($data['department_id'])) {
      return "Program studi wajib dipilih.";
    }
    return "";
  }

  function add($post)
  {
    $data = $this->collect($post);

    $error = $this->validate($data);
    if ($error !== "") {
      $this->setFlash('danger', $error);
      $this->redirect('index.php');
    }
    $data['department_id'] = (int) $data['department_id'];

    $this->lecturer->open();
    $ok = $this->lecturer->add($data);
    $errno = $this->lecturer->getErrno();
    $this->lecturer->close();

    if ($ok) {
      $this->setFlash('success', 'Data dosen berhasil ditambahkan.');
    } else {
      $this->setFlash('danger', $this->dbMessage($errno, array(
        1062 => 'NIDN atau email sudah terdaftar pada dosen lain.',
        1452 => 'Program studi yang dipilih tidak valid.'
      )));
    }
    $this->redirect('index.php');
  }

  function edit($post)
  {
    $id = $this->value($post, 'id');
    if (!$this->validId($id)) {
      $this->setFlash('danger', 'ID dosen tidak valid.');
      $this->redirect('index.php');
    }

    $data = $this->collect($post);

    $error = $this->validate($data);
    if ($error !== "") {
      $this->setFlash('danger', $error);
      $this->redirect('index.php?id_edit=' . (int) $id);
    }
    $data['department_id'] = (int) $data['department_id'];

    $this->lecturer->open();
    $ok = $this->lecturer->update((int) $id, $data);
    $errno = $this->lecturer->getErrno();
    $this->lecturer->close();

    if ($ok) {
      $this->setFlash('success', 'Data dosen berhasil diperbarui.');
      $this->redirect('index.php');
    }

    $this->setFlash('danger', $this->dbMessage($errno, array(
      1062 => 'NIDN atau email sudah terdaftar pada dosen lain.',
      1452 => 'Program studi yang dipilih tidak valid.'
    )));
    $this->redirect('index.php?id_edit=' . (int) $id);
  }

  function delete($id)
  {
    if (!$this->validId($id)) {
      $this->setFlash('danger', 'ID dosen tidak valid.');
      $this->redirect('index.php');
    }

    $this->lecturer->open();
    $ok = $this->lecturer->delete((int) $id);
    $errno = $this->lecturer->getErrno();
    $affected = $this->lecturer->affected_rows;
    $this->lecturer->close();

    if ($ok && $affected > 0) {
      $this->setFlash('success', 'Data dosen berhasil dihapus.');
    } else if ($ok) {
      $this->setFlash('danger', 'Data dosen tidak ditemukan.');
    } else {
      $this->setFlash('danger', $this->dbMessage($errno, array(
        1451 => 'Dosen tidak dapat dihapus karena masih mengampu mata kuliah. Pindahkan atau hapus mata kuliahnya terlebih dahulu.'
      )));
    }
    $this->redirect('index.php');
  }
}
