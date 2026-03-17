<?php
$pageTitle = 'Contact Us';
require_once 'header.php';

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($conn, $_POST['name'] ?? '');
    $email = sanitize($conn, $_POST['email'] ?? '');
    $phone = sanitize($conn, $_POST['phone'] ?? '');
    $message = sanitize($conn, $_POST['message'] ?? '');

    if ($name && $email && $message) {
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, phone, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $phone, $message);
        if ($stmt->execute()) {
            $success = t('msg_sent_success');
        } else {
            $error = "Failed to send message. Please try again.";
        }
    } else {
        $error = "Please fill in all required fields.";
    }
}
?>

<!-- Page Header -->
<div class="page-header">
  <div class="container">
    <h1><i class="fas fa-envelope me-2"></i><?php echo t('contact_us'); ?></h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/"><?php echo t('home'); ?></a></li>
        <li class="breadcrumb-item active"><?php echo t('contact'); ?></li>
      </ol>
    </nav>
  </div>
</div>

<section class="section-sacred">
  <div class="container">
    <div class="row g-4">

      <!-- Contact Form -->
      <div class="col-lg-7">
        <div class="sacred-card">
          <h3 class="mb-4"><i class="fas fa-paper-plane me-2" style="color:var(--chandan-gold);"></i><?php echo t('send_message'); ?></h3>

          <?php if($success): ?>
            <div id="toast-data" data-message="<?php echo htmlspecialchars($success); ?>" data-type="success" data-redirect="contact.php"></div>
          <?php endif; ?>
          <?php if($error): ?>
            <div id="toast-data" data-message="<?php echo htmlspecialchars($error); ?>" data-type="error"></div>
          <?php endif; ?>

          <form method="POST" class="form-sacred">
            <div class="row g-3">
              <div class="col-md-6">
                <label><?php echo t('full_name'); ?> *</label>
                <input type="text" name="name" class="form-control" required placeholder="<?php echo t('enter_your_name'); ?>">
              </div>
              <div class="col-md-6">
                <label><?php echo t('email'); ?> *</label>
                <input type="email" name="email" class="form-control" required placeholder="<?php echo t('enter_your_email'); ?>">
              </div>
              <div class="col-12">
                <label><?php echo t('phone'); ?></label>
                <input type="tel" name="phone" class="form-control" placeholder="<?php echo t('enter_phone_number'); ?>">
              </div>
              <div class="col-12">
                <label><?php echo t('message'); ?> *</label>
                <textarea name="message" class="form-control" rows="5" required placeholder="<?php echo t('write_message'); ?>"></textarea>
              </div>
              <div class="col-12">
                <button type="submit" class="btn-sacred">
                  <i class="fas fa-paper-plane"></i> <?php echo t('send'); ?>
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>

      <!-- Contact Info -->
      <div class="col-lg-5">
        <div class="contact-info-card">
          <h4><i class="fas fa-info-circle me-2"></i><?php echo t('get_in_touch_title'); ?></h4>
          <p style="margin-bottom:2rem;"><?php echo t('contact_reach_out'); ?></p>

          <div class="contact-info-item">
            <div class="icon"><i class="fas fa-envelope"></i></div>
            <div>
              <strong style="color:var(--chandan-gold);"><?php echo t('email'); ?></strong>
              <p class="mb-0">info@astropanchang.com</p>
            </div>
          </div>

          <div class="contact-info-item">
            <div class="icon"><i class="fas fa-phone"></i></div>
            <div>
              <strong style="color:var(--chandan-gold);"><?php echo t('phone'); ?></strong>
              <p class="mb-0">+91 98765 43210</p>
            </div>
          </div>

          <div class="contact-info-item">
            <div class="icon"><i class="fas fa-map-marker-alt"></i></div>
            <div>
              <strong style="color:var(--chandan-gold);"><?php echo t('address'); ?></strong>
              <p class="mb-0">India</p>
            </div>
          </div>

          <div class="contact-info-item">
            <div class="icon"><i class="fas fa-clock"></i></div>
            <div>
              <strong style="color:var(--chandan-gold);"><?php echo t('working_hours'); ?></strong>
              <p class="mb-0">Mon-Sat: 9:00 AM — 6:00 PM</p>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<?php require_once 'footer.php'; ?>
