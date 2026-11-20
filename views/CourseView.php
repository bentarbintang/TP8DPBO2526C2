<?php

class CourseView
{
  public function render($data)
  {
    $edit = $data['edit'];
    $no = 1;
    $dataTabel = '';
    $optLecturer = '';

    foreach ($data['course'] as $row) {
      $dataTabel .= "<tr>
                <td class='text-center'>" . $no++ . "</td>
                <td><span class='badge bg-dark'>" . Template::e($row['code']) . "</span></td>
                <td class='fw-semibold'>" . Template::e($row['name']) . "</td>
                <td class='text-center'>" . (int) $row['credits'] . "</td>
                <td class='text-center'>" . (int) $row['semester'] . "</td>
                <td>" . Template::e($row['lecturer_name']) . "</td>
                <td><span class='badge bg-secondary me-1'>" . Template::e($row['department_code']) . "</span>" . Template::e($row['department_name']) . "</td>
                <td class='text-nowrap'>
                  <a href='course.php?id_edit=" . (int) $row['id'] . "' class='btn btn-sm btn-warning'>Edit</a>
                  <form action='course.php' method='POST' class='d-inline' onsubmit=\"return confirm('Yakin ingin menghapus data ini?');\">
                    <input type='hidden' name='id' value='" . (int) $row['id'] . "'>
                    <button type='submit' name='delete' class='btn btn-sm btn-danger'>Hapus</button>
                  </form>
                </td>
              </tr>";
    }

    if ($dataTabel == '') {
      $dataTabel = "<tr><td colspan='8' class='text-center text-muted py-4'>Belum ada data mata kuliah.</td></tr>";
    }

    // Dropdown Foreign Key: dosen pengampu
    if (!$edit) {
      $optLecturer .= "<option value='' disabled selected>-- Pilih Dosen Pengampu --</option>";
    }
    foreach ($data['lecturer'] as $row) {
      $selected = ($edit && $edit['lecturer_id'] == $row['id']) ? " selected" : "";
      $optLecturer .= "<option value='" . (int) $row['id'] . "'" . $selected . ">" . Template::e($row['name'] . " (" . $row['department_code'] . ")") . "</option>";
    }

    $tpl = new Template("templates/course.html");

    $tpl->replace("DATA_PAGE_TITLE", "Mata Kuliah");
    $tpl->replace("DATA_NAVBAR", Template::navbar('COURSE'));
    $tpl->replace("DATA_FLASH", Template::flash($data['flash']));

    if ($edit) {
      $tpl->replace("DATA_FORM_TITLE", "Ubah Mata Kuliah");
      $tpl->replace("DATA_FORM_ID", "<input type='hidden' name='id' value='" . (int) $edit['id'] . "'>");
      $tpl->replace("DATA_ACTION_NAME", "update");
      $tpl->replace("DATA_BUTTON_LABEL", "Simpan Perubahan");
      $tpl->replace("DATA_BUTTON_CLASS", "btn-warning");
      $tpl->replace("DATA_CANCEL", "<a href='course.php' class='btn btn-outline-secondary'>Batal</a>");
      $tpl->replace("DATA_VAL_CODE", Template::e($edit['code']));
      $tpl->replace("DATA_VAL_NAME", Template::e($edit['name']));
      $tpl->replace("DATA_VAL_CREDITS", Template::e($edit['credits']));
      $tpl->replace("DATA_VAL_SEMESTER", Template::e($edit['semester']));
    } else {
      $tpl->replace("DATA_FORM_TITLE", "Tambah Mata Kuliah");
      $tpl->replace("DATA_ACTION_NAME", "add");
      $tpl->replace("DATA_BUTTON_LABEL", "Tambah");
      $tpl->replace("DATA_BUTTON_CLASS", "btn-primary");
    }

    $tpl->replace("DATA_OPT_LECTURER", $optLecturer);
    $tpl->replace("DATA_TOTAL", count($data['course']));
    $tpl->replace("DATA_TABEL", $dataTabel);
    $tpl->write();
  }
}
