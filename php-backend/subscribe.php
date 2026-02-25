<?php
include "db.php";

$conn->query("UPDATE users SET subscription_status='active' WHERE id=1");

echo "<h2>Subscription Activated </h2>
<p>Go back and click Download again.</p>";