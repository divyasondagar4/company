<?php
require_once 'db.php';

$images = [
    ['title' => 'Vedic Astrology Chart', 'image' => 'vedic_chart.png'],
    ['title' => 'Zodiac Mandala', 'image' => 'zodiac_mandala.png'],
    ['title' => 'Ancient Temple Astronomy', 'image' => 'temple_astronomy.png'],
    ['title' => 'Navagraha Cosmic View', 'image' => 'planets_cosmic.png']
];

foreach ($images as $img) {
    $title = $conn->real_escape_string($img['title']);
    $image = $conn->real_escape_string($img['image']);
    $conn->query("INSERT INTO gallery (title, image) VALUES ('$title', '$image')");
}

echo "Gallery updated successfully!";
?>
