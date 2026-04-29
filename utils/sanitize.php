<?php
// helpers/sanitize.php
function sanitize_input(string $data): string {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function escape_output(string $data): string {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}