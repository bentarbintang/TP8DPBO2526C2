<?php

class DB
{
    var $db_host = "";
    var $db_user = "";
    var $db_pass = "";
    var $db_name = "";
    var $db_link = "";
    var $result = 0;
    var $errno = 0;
    var $error = "";
    var $affected_rows = 0;
    var $insert_id = 0;

    function __construct($db_host, $db_user, $db_pass, $db_name)
    {
        $this->db_host = $db_host;
        $this->db_user = $db_user;
        $this->db_pass = $db_pass;
        $this->db_name = $db_name;
    }

    function open()
    {
        // Error ditangani manual (lewat errno), bukan exception
        mysqli_report(MYSQLI_REPORT_OFF);

        $this->db_link = @mysqli_connect($this->db_host, $this->db_user, $this->db_pass, $this->db_name);

        if (!$this->db_link) {
            die("Koneksi database gagal: " . htmlspecialchars(mysqli_connect_error()) .
                ". Pastikan MySQL di Laragon sudah berjalan dan file SQL/tp_mvc25.sql sudah diimpor.");
        }

        mysqli_set_charset($this->db_link, "utf8mb4");
    }

    // Query biasa tanpa parameter (hanya untuk query yang tidak memakai input pengguna)
    function execute($query)
    {
        $this->resetStatus();
        $this->result = mysqli_query($this->db_link, $query);

        if ($this->result === false) {
            $this->errno = mysqli_errno($this->db_link);
            $this->error = mysqli_error($this->db_link);
        } else {
            $this->affected_rows = mysqli_affected_rows($this->db_link);
            $this->insert_id = mysqli_insert_id($this->db_link);
        }

        return $this->result;
    }

    // Query dengan prepared statement. Tanda "?" pada $query diisi oleh $params,
    // $types berisi tipe tiap parameter: i = integer, s = string, d = double.
    // Contoh: executePrepared("SELECT * FROM lecturers WHERE id = ?", "i", array($id));
    function executePrepared($query, $types = "", $params = array())
    {
        $this->resetStatus();

        $stmt = mysqli_prepare($this->db_link, $query);
        if (!$stmt) {
            $this->errno = mysqli_errno($this->db_link);
            $this->error = mysqli_error($this->db_link);
            return false;
        }

        if ($types !== "" && count($params) > 0) {
            $stmt->bind_param($types, ...$params);
        }

        if (!$stmt->execute()) {
            $this->errno = $stmt->errno;
            $this->error = $stmt->error;
            $stmt->close();
            return false;
        }

        // SELECT menghasilkan mysqli_result, INSERT/UPDATE/DELETE menghasilkan false
        $res = $stmt->get_result();
        $this->result = ($res !== false) ? $res : true;
        $this->affected_rows = $stmt->affected_rows;
        $this->insert_id = $stmt->insert_id;

        $stmt->close();
        return true;
    }

    function getResult()
    {
        if ($this->result instanceof mysqli_result) {
            return mysqli_fetch_assoc($this->result);
        }
        return false;
    }

    function getErrno()
    {
        return $this->errno;
    }

    function getError()
    {
        return $this->error;
    }

    function close()
    {
        if ($this->db_link) {
            mysqli_close($this->db_link);
        }
    }

    private function resetStatus()
    {
        $this->result = 0;
        $this->errno = 0;
        $this->error = "";
        $this->affected_rows = 0;
        $this->insert_id = 0;
    }
}
