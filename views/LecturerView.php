<?php

class LecturerView
{
  public function render($data)
  {
    $edit = $data['edit'];
    $no = 1;
    $dataTabel = '';
    $optRank = '';
    $optDepartment = '';

    foreach ($data['lecturer'] as $row) {
      $dataTabel .= "<tr>
                <td class='text-center'>" . $no++ . "</td>
                <td>
                  <div class='fw-semibold'>" . Template::e($row['name']) . "</div>
                  <div class='small text-muted'>" . Template::e($row['email']) . "</div>
                </td>
                <td>" . Template::e($row['nidn']) . "</td>
                <td>" . Template::e($row['phone']) . "</td>
                <td>" . Template::e($row['academic_rank']) . "</td>
                <td><span class='badge bg-secondary me-1'>" . Template::e($row['department_code']) . "</span>" . Template::e($row['department_name']) . "</td>
                <td class='text-nowrap'>" . Template::e(date('d-m-Y', strtotime($row['join_date']))) . "</td>
                <td class='text-nowrap'>
                  <a href='index.php?id_edit=" . (int) $row['id'] . "' class='btn btn-sm btn-warning'>Edit</a>
                  <form action='index.php' method='POST' class='d-inline' onsubmit=\"return confirm('Yakin ingin menghapus data ini?');\">
                    <input type='hidden' name='id' value='" . (int) $row['id'] . "'>
                    <button type='submit' name='delete' class='btn btn-sm btn-danger'>Hapus</button>
                  </form>
                </td>
              </tr>";
    }

    if ($dataTabel == '') {
      $dataTabel = "<tr><td colspan='8' class='text-center text-muted py-4'>Belum ada data dosen.</td></tr>";
    }

    // Dropdown jabatan akademik
    foreach ($data['ranks'] as $rank) {
      $selected = ($edit && $edit['academic_rank'] == $rank) ? " selected" : "";
      $optRank .= "<option value='" . Template::e($rank) . "'" . $selected . ">" . Template::e($rank) . "</option>";
    }

    // Dropdown Foreign Key: program studi
    if (!$edit) {
      $optDepartment .= "<option value='' disabled selected>-- Pilih Program Studi --</option>";
    }
    foreach ($data['department'] as $row) {
      $selected = ($edit && $edit['department_id'] == $row['id']) ? " selected" : "";
      $optDepartment .= "<option value='" . (int) $row['id'] . "'" . $selected . ">" . Template::e($row['code'] . " - " . $row['name']) . "</option>";
    }

    $tpl = new Template("templates/index.html");

    $tpl->replace("DATA_PAGE_TITLE", "Dosen");
    $tpl->replace("DATA_NAVBAR", Template::navbar('LECTURER'));
    $tpl->replace("DATA_FLASH", Template::flash($data['flash']));

    if ($edit) {
      $tpl->replace("DATA_FORM_TITLE", "Ubah Data Dosen");
      $tpl->replace("DATA_FORM_ID", "<input type='hidden' name='id' value='" . (int) $edit['id'] . "'>");
      $tpl->replace("DATA_ACTION_NAME", "update");
      $tpl->replace("DATA_BUTTON_LABEL", "Simpan Perubahan");
      $tpl->replace("DATA_BUTTON_CLASS", "btn-warning");
      $tpl->replace("DATA_CANCEL", "<a href='index.php' class='btn btn-outline-secondary'>Batal</a>");
      $tpl->replace("DATA_VAL_NAME", Template::e($edit['name']));
      $tpl->replace("DATA_VAL_NIDN", Template::e($edit['nidn']));
      $tpl->replace("DATA_VAL_EMAIL", Template::e($edit['email']));
      $tpl->replace("DATA_VAL_PHONE", Template::e($edit['phone']));
      $tpl->replace("DATA_VAL_JOIN_DATE", Template::e($edit['join_date']));
    } else {
      $tpl->replace("DATA_FORM_TITLE", "Tambah Dosen");
      $tpl->replace("DATA_ACTION_NAME", "add");
      $tpl->replace("DATA_BUTTON_LABEL", "Tambah");
      $tpl->replace("DATA_BUTTON_CLASS", "btn-primary");
    }

    $tpl->replace("DATA_OPT_RANK", $optRank);
    $tpl->replace("DATA_OPT_DEPARTMENT", $optDepartment);
    $tpl->replace("DATA_TOTAL", count($data['lecturer']));
    $tpl->replace("DATA_TABEL", $dataTabel);
    $tpl->write();
  }
}
