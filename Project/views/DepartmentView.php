<?php

class DepartmentView
{
  public function render($data)
  {
    $edit = $data['edit'];
    $no = 1;
    $dataTabel = '';

    foreach ($data['department'] as $row) {
      $dataTabel .= "<tr>
                <td class='text-center'>" . $no++ . "</td>
                <td><span class='badge bg-secondary'>" . Template::e($row['code']) . "</span></td>
                <td class='fw-semibold'>" . Template::e($row['name']) . "</td>
                <td>" . Template::e($row['head_name']) . "</td>
                <td>" . Template::e($row['building']) . "</td>
                <td class='text-center'>" . (int) $row['total_lecturers'] . "</td>
                <td class='text-nowrap'>
                  <a href='department.php?id_edit=" . (int) $row['id'] . "' class='btn btn-sm btn-warning'>Edit</a>
                  <form action='department.php' method='POST' class='d-inline' onsubmit=\"return confirm('Yakin ingin menghapus data ini?');\">
                    <input type='hidden' name='id' value='" . (int) $row['id'] . "'>
                    <button type='submit' name='delete' class='btn btn-sm btn-danger'>Hapus</button>
                  </form>
                </td>
              </tr>";
    }

    if ($dataTabel == '') {
      $dataTabel = "<tr><td colspan='7' class='text-center text-muted py-4'>Belum ada data program studi.</td></tr>";
    }

    $tpl = new Template("templates/department.html");

    $tpl->replace("DATA_PAGE_TITLE", "Program Studi");
    $tpl->replace("DATA_NAVBAR", Template::navbar('DEPARTMENT'));
    $tpl->replace("DATA_FLASH", Template::flash($data['flash']));

    if ($edit) {
      $tpl->replace("DATA_FORM_TITLE", "Ubah Program Studi");
      $tpl->replace("DATA_FORM_ID", "<input type='hidden' name='id' value='" . (int) $edit['id'] . "'>");
      $tpl->replace("DATA_ACTION_NAME", "update");
      $tpl->replace("DATA_BUTTON_LABEL", "Simpan Perubahan");
      $tpl->replace("DATA_BUTTON_CLASS", "btn-warning");
      $tpl->replace("DATA_CANCEL", "<a href='department.php' class='btn btn-outline-secondary'>Batal</a>");
      $tpl->replace("DATA_VAL_CODE", Template::e($edit['code']));
      $tpl->replace("DATA_VAL_NAME", Template::e($edit['name']));
      $tpl->replace("DATA_VAL_HEAD", Template::e($edit['head_name']));
      $tpl->replace("DATA_VAL_BUILDING", Template::e($edit['building']));
    } else {
      $tpl->replace("DATA_FORM_TITLE", "Tambah Program Studi");
      $tpl->replace("DATA_ACTION_NAME", "add");
      $tpl->replace("DATA_BUTTON_LABEL", "Tambah");
      $tpl->replace("DATA_BUTTON_CLASS", "btn-primary");
    }

    $tpl->replace("DATA_TOTAL", count($data['department']));
    $tpl->replace("DATA_TABEL", $dataTabel);
    $tpl->write();
  }
}
