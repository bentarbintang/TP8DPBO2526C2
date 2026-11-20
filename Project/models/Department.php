<?php

class Department extends DB
{
    // Read semua prodi + jumlah dosen tiap prodi
    function getDepartment()
    {
        $query = "SELECT d.id, d.code, d.name, d.head_name, d.building,
                         COUNT(l.id) AS total_lecturers
                  FROM departments d
                  LEFT JOIN lecturers l ON l.department_id = d.id
                  GROUP BY d.id, d.code, d.name, d.head_name, d.building
                  ORDER BY d.name ASC";
        return $this->execute($query);
    }

    function getDepartmentById($id)
    {
        $query = "SELECT id, code, name, head_name, building FROM departments WHERE id = ?";
        return $this->executePrepared($query, "i", array($id));
    }

    function add($data)
    {
        $query = "INSERT INTO departments (code, name, head_name, building) VALUES (?, ?, ?, ?)";
        return $this->executePrepared($query, "ssss", array(
            $data['code'],
            $data['name'],
            $data['head_name'],
            $data['building']
        ));
    }

    function update($id, $data)
    {
        $query = "UPDATE departments SET code = ?, name = ?, head_name = ?, building = ? WHERE id = ?";
        return $this->executePrepared($query, "ssssi", array(
            $data['code'],
            $data['name'],
            $data['head_name'],
            $data['building'],
            $id
        ));
    }

    function delete($id)
    {
        $query = "DELETE FROM departments WHERE id = ?";
        return $this->executePrepared($query, "i", array($id));
    }
}
