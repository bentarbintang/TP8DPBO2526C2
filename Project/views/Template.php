<?php

class Template
{
    var $filename = '';
    var $content = '';

    function __construct($filename = '')
    {
        $this->filename = $filename;

        $this->content = implode('', @file($filename));
    }

    // Menghapus placeholder DATA_XXX yang tidak terisi
    function clear()
    {
        $this->content = preg_replace("/DATA_[A-Z|_|0-9]+/", "", $this->content);
    }

    function write()
    {
        $this->clear();
        print $this->content;
    }

    function getContent()
    {
        $this->clear();
        return $this->content;
    }

    // Mengganti placeholder $old dengan nilai $new.
    // Memakai str_replace (bukan preg_replace) supaya karakter khusus seperti
    // "$1" atau "\" pada data tidak merusak hasil render.
    function replace($old = '', $new = '')
    {
        if (is_int($new)) {
            $value = sprintf("%d", $new);
        } elseif (is_float($new)) {
            $value = sprintf("%f", $new);
        } elseif (is_array($new)) {
            $value = '';

            foreach ($new as $item) {
                $value .= $item . ' ';
            }
        } else {
            $value = (string) $new;
        }
        $this->content = str_replace($old, $value, $this->content);
    }

    // Escape output agar aman dari XSS
    static function e($text)
    {
        return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
    }

    // Navbar dari templates/navbar.html, $active = LECTURER | DEPARTMENT | COURSE
    static function navbar($active)
    {
        $nav = new Template("templates/navbar.html");

        foreach (array('LECTURER', 'DEPARTMENT', 'COURSE') as $menu) {
            $nav->replace("DATA_ACTIVE_" . $menu, ($menu == $active) ? "active" : "");
        }

        return $nav->getContent();
    }

    // Pesan notifikasi (alert Bootstrap) dari controller
    static function flash($flash)
    {
        if (empty($flash)) {
            return '';
        }

        $type = ($flash['type'] == 'success') ? 'success' : 'danger';

        return "<div class='alert alert-" . $type . " alert-dismissible fade show' role='alert'>"
            . self::e($flash['message'])
            . "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Tutup'></button>"
            . "</div>";
    }
}
