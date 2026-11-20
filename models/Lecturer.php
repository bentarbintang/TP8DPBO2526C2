<?php

class Lecturer extends DB
{
    // Pilihan jabatan akademik (sama dengan ENUM di tabel lecturers)
    static public $ranks = array('Asisten Ahli', 'Lektor', 'Lektor Kepala', 'Profesor');

    // Read semua dosen + nama prodi (JOIN)
    function getLecturer()
    {
        $query = "SELECT l.id, l.name, l.nidn, l.email, l.phone, l.academic_rank,
                         l.join_date, l.department_id,
                         d.code AS department_code, d.name AS department_name
                  FROM lecturers l
                  JOIN departments d ON l.department_id = d.id
                  ORDER BY l.name ASC";
        return $this->execute($query);
    }

    function getLecturerById($id)
    {
        $query = "SELECT id, name, nidn, email, phone, academic_rank, join_date, department_id
                  FROM lecturers WHERE id = ?";
        return $this->executePrepared($query, "i", array($id));
    }

    function add($data)
    {
        $query = "INSERT INTO lecturers (name, nidn, email, phone, academic_rank, join_date, department_id)
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        return $this->executePrepared($query, "ssssssi", array(
            $data['name'],
            $data['nidn'],
            $data['email'],
            $data['phone'],
            $data['academic_rank'],
            $data['join_date'],
            $data['department_id']
        ));
    }

    function update($id, $data)
    {
        $query = "UPDATE lecturers
                  SET name = ?, nidn = ?, email = ?, phone = ?, academic_rank = ?,
                      join_date = ?, department_id = ?
                  WHERE id = ?";
        return $this->executePrepared($query, "ssssssii", array(
            $data['name'],
            $data['nidn'],
            $data['email'],
            $data['phone'],
            $data['academic_rank'],
            $data['join_date'],
            $data['department_id'],
            $id
        ));
    }

    function delete($id)
    {
        $query = "DELETE FROM lecturers WHERE id = ?";
        return $this->executePrepared($query, "i", array($id));
    }
}
