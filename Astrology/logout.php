<?php
session_start();
session_unset();
session_destroy();
header("Location: /vibe_tech_labs/Astrology/");
exit();
?>
