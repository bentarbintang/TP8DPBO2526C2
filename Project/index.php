<?php
session_start();

include_once("models/DB.php");
include_once("views/Template.php");
include_once("controllers/LecturerController.php");

$lecturer = new LecturerController();

if (isset($_POST['add'])) {
    // Create: simpan data dosen baru
    $lecturer->add($_POST);
} else if (isset($_POST['update'])) {
    // Update: simpan perubahan data dosen
    $lecturer->edit($_POST);
} else if (isset($_POST['delete'])) {
    // Delete: hapus data dosen
    $lecturer->delete(isset($_POST['id']) ? $_POST['id'] : null);
} else if (isset($_GET['id_edit'])) {
    // Tampilkan form ubah dengan data terisi
    $lecturer->index($_GET['id_edit']);
} else {
    // Read: tampilkan daftar dosen
    $lecturer->index();
}
