<?php

class Course extends DB
{
    // Read semua mata kuliah + nama dosen + nama prodi (JOIN 3 tabel)
    function getCourse()
    {
        $query = "SELECT c.id, c.code, c.name, c.credits, c.semester, c.lecturer_id,
                         l.name AS lecturer_name,
                         d.code AS department_code, d.name AS department_name
                  FROM courses c
                  JOIN lecturers l ON c.lecturer_id = l.id
                  JOIN departments d ON l.department_id = d.id
                  ORDER BY c.semester ASC, c.code ASC";
        return $this->execute($query);
    }

    function getCourseById($id)
    {
        $query = "SELECT id, code, name, credits, semester, lecturer_id FROM courses WHERE id = ?";
        return $this->executePrepared($query, "i", array($id));
    }

    function add($data)
    {
        $query = "INSERT INTO courses (code, name, credits, semester, lecturer_id) VALUES (?, ?, ?, ?, ?)";
        return $this->executePrepared($query, "ssiii", array(
            $data['code'],
            $data['name'],
            $data['credits'],
            $data['semester'],
            $data['lecturer_id']
        ));
    }

    function update($id, $data)
    {
        $query = "UPDATE courses
                  SET code = ?, name = ?, credits = ?, semester = ?, lecturer_id = ?
                  WHERE id = ?";
        return $this->executePrepared($query, "ssiiii", array(
            $data['code'],
            $data['name'],
            $data['credits'],
            $data['semester'],
            $data['lecturer_id'],
            $id
        ));
    }

    function delete($id)
    {
        $query = "DELETE FROM courses WHERE id = ?";
        return $this->executePrepared($query, "i", array($id));
    }
}
