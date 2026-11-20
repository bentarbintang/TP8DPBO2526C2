<?php
include_once("config.php");
include_once("models/DB.php");
include_once("models/Department.php");
include_once("views/Template.php");
include_once("views/DepartmentView.php");
include_once("controllers/BaseController.php");

class DepartmentController extends BaseController
{
  // Properti kontroler
  private $department;

  // Konstruktor
  function __construct()
  {
    $this->department = new Department(Config::$db_host, Config::$db_user, Config::$db_pass, Config::$db_name);
  }

  // Halaman utama: daftar prodi + form tambah/ubah
  public function index($id_edit = null)
  {
    $this->department->open();

    $data = array(
      'department' => array(),
      'edit' => null,
      'flash' => null
    );

    $this->department->getDepartment();
    while ($row = $this->department->getResult()) {
      array_push($data['department'], $row);
    }

    // Mode ubah: ambil satu data berdasarkan id
    if ($id_edit !== null) {
      $row = false;
      if ($this->validId($id_edit)) {
        $this->department->getDepartmentById((int) $id_edit);
        $row = $this->department->getResult();
      }

      if (!$row) {
        $this->department->close();
        $this->setFlash('danger', 'Data program studi tidak ditemukan.');
        $this->redirect('department.php');
      }
      $data['edit'] = $row;
    }

    $this->department->close();

    $data['flash'] = $this->getFlash();

    $view = new DepartmentView();
    $view->render($data);
  }

  // Mengambil dan membersihkan input form
  private function collect($post)
  {
    return array(
      'code' => strtoupper($this->value($post, 'code')),
      'name' => $this->value($post, 'name'),
      'head_name' => $this->value($post, 'head_name'),
      'building' => $this->value($post, 'building')
    );
  }

  // Validasi input, mengembalikan pesan error ("" jika valid)
  private function validate($data)
  {
    if (!preg_match('/^[A-Z]{2,5}$/', $data['code'])) {
      return "Kode prodi harus 2-5 huruf (contoh: IF).";
    }
    if ($data['name'] === '' || strlen($data['name']) > 100) {
      return "Nama program studi wajib diisi (maksimal 100 karakter).";
    }
    if ($data['head_name'] === '' || strlen($data['head_name']) > 100) {
      return "Nama ketua prodi wajib diisi (maksimal 100 karakter).";
    }
    if ($data['building'] === '' || strlen($data['building']) > 100) {
      return "Gedung wajib diisi (maksimal 100 karakter).";
    }
    return "";
  }

  function add($post)
  {
    $data = $this->collect($post);

    $error = $this->validate($data);
    if ($error !== "") {
      $this->setFlash('danger', $error);
      $this->redirect('department.php');
    }

    $this->department->open();
    $ok = $this->department->add($data);
    $errno = $this->department->getErrno();
    $this->department->close();

    if ($ok) {
      $this->setFlash('success', 'Program studi berhasil ditambahkan.');
    } else {
      $this->setFlash('danger', $this->dbMessage($errno, array(
        1062 => 'Kode program studi sudah digunakan.'
      )));
    }
    $this->redirect('department.php');
  }

  function edit($post)
  {
    $id = $this->value($post, 'id');
    if (!$this->validId($id)) {
      $this->setFlash('danger', 'ID program studi tidak valid.');
      $this->redirect('department.php');
    }

    $data = $this->collect($post);

    $error = $this->validate($data);
    if ($error !== "") {
      $this->setFlash('danger', $error);
      $this->redirect('department.php?id_edit=' . (int) $id);
    }

    $this->department->open();
    $ok = $this->department->update((int) $id, $data);
    $errno = $this->department->getErrno();
    $this->department->close();

    if ($ok) {
      $this->setFlash('success', 'Program studi berhasil diperbarui.');
      $this->redirect('department.php');
    }

    $this->setFlash('danger', $this->dbMessage($errno, array(
      1062 => 'Kode program studi sudah digunakan.'
    )));
    $this->redirect('department.php?id_edit=' . (int) $id);
  }

  function delete($id)
  {
    if (!$this->validId($id)) {
      $this->setFlash('danger', 'ID program studi tidak valid.');
      $this->redirect('department.php');
    }

    $this->department->open();
    $ok = $this->department->delete((int) $id);
    $errno = $this->department->getErrno();
    $affected = $this->department->affected_rows;
    $this->department->close();

    if ($ok && $affected > 0) {
      $this->setFlash('success', 'Program studi berhasil dihapus.');
    } else if ($ok) {
      $this->setFlash('danger', 'Data program studi tidak ditemukan.');
    } else {
      $this->setFlash('danger', $this->dbMessage($errno, array(
        1451 => 'Program studi tidak dapat dihapus karena masih memiliki dosen. Pindahkan atau hapus dosennya terlebih dahulu.'
      )));
    }
    $this->redirect('department.php');
  }
}
