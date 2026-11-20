<?php
session_start();

include_once("models/DB.php");
include_once("views/Template.php");
include_once("controllers/CourseController.php");

$course = new CourseController();

if (isset($_POST['add'])) {
    // Create: simpan mata kuliah baru
    $course->add($_POST);
} else if (isset($_POST['update'])) {
    // Update: simpan perubahan mata kuliah
    $course->edit($_POST);
} else if (isset($_POST['delete'])) {
    // Delete: hapus mata kuliah
    $course->delete(isset($_POST['id']) ? $_POST['id'] : null);
} else if (isset($_GET['id_edit'])) {
    // Tampilkan form ubah dengan data terisi
    $course->index($_GET['id_edit']);
} else {
    // Read: tampilkan daftar mata kuliah
    $course->index();
}
