<?php

$classes = $_SERVER["DOCUMENT_ROOT"];
$classes .= "/php/lib/classes.php";
require($classes);


$post = new CreateNupFlight();
$post->execute();