<?php
session_start();

include_once("models/DB.php");
include_once("views/Template.php");
include_once("controllers/DepartmentController.php");

$department = new DepartmentController();

if (isset($_POST['add'])) {
    // Create: simpan program studi baru
    $department->add($_POST);
} else if (isset($_POST['update'])) {
    // Update: simpan perubahan program studi
    $department->edit($_POST);
} else if (isset($_POST['delete'])) {
    // Delete: hapus program studi
    $department->delete(isset($_POST['id']) ? $_POST['id'] : null);
} else if (isset($_GET['id_edit'])) {
    // Tampilkan form ubah dengan data terisi
    $department->index($_GET['id_edit']);
} else {
    // Read: tampilkan daftar program studi
    $department->index();
}
