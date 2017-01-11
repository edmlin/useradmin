<?php
function __autoload($class_name) {
    require_once strtolower("models/class_{$class_name}". '.php');
}
