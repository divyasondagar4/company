<!-- Footer -->
<footer class="footer-sacred">
  <div class="container">
    <div class="row">
      <div class="col-lg-4 col-md-6 mb-4">
        <h5><i class="fas fa-om me-2"></i><?php echo t('astro_panchang'); ?></h5>
        <p><?php echo t('footer_desc'); ?></p>
        <!-- Social Icons — Horizontal Row -->
        <div class="social-icons mt-3">
          <a href="#" class="social-icon social-facebook" title="Facebook"><i class="fab fa-facebook-f"></i></a>
          <a href="#" class="social-icon social-instagram" title="Instagram"><i class="fab fa-instagram"></i></a>
          <a href="#" class="social-icon social-youtube" title="YouTube"><i class="fab fa-youtube"></i></a>
          <a href="#" class="social-icon social-whatsapp" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
          <a href="#" class="social-icon social-twitter" title="Twitter"><i class="fab fa-twitter"></i></a>
        </div>
      </div>
      <div class="col-lg-2 col-md-6 mb-4">
        <h5><?php echo t('quick_links'); ?></h5>
        <a href="<?php echo SITE_URL; ?>/panchang.php"><?php echo t('daily_panchang'); ?></a>
        <a href="<?php echo SITE_URL; ?>/muhurat.php"><?php echo t('muhurat'); ?></a>
        <a href="<?php echo SITE_URL; ?>/festival-calendar.php"><?php echo t('festival_calendar'); ?></a>
        <a href="<?php echo SITE_URL; ?>/subscribe.php"><?php echo t('subscribe'); ?></a>
      </div>
      <div class="col-lg-3 col-md-6 mb-4">
        <h5><?php echo t('more'); ?></h5>
        <a href="<?php echo SITE_URL; ?>/festival-calendar.php"><?php echo t('festival_calendar'); ?></a>
        <a href="<?php echo SITE_URL; ?>/gallery.php"><?php echo t('gallery'); ?></a>
        <a href="<?php echo SITE_URL; ?>/contact.php"><?php echo t('contact_us'); ?></a>
        <a href="<?php echo SITE_URL; ?>/subscribe.php"><?php echo t('subscribe'); ?></a>
      </div>
      <div class="col-lg-3 col-md-6 mb-4">
        <h5><?php echo t('contact_info'); ?></h5>
        <p><i class="fas fa-envelope me-2"></i>info@astropanchang.com</p>
        <p><i class="fas fa-phone me-2"></i>+91 98765 43210</p>
        <p><i class="fas fa-map-marker-alt me-2"></i>India</p>
      </div>
    </div>
    <div class="footer-bottom">
      <p>&copy; <?php echo date('Y'); ?> <?php echo t('astro_panchang'); ?>. <?php echo t('all_rights'); ?> | <?php echo t('crafted_with_love'); ?> <i class="fas fa-heart" style="color:var(--sacred-kumkum);"></i> <?php echo t('for_divine'); ?></p>
    </div>
  </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- FullCalendar -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.js"></script>
<!-- Custom JS -->
<script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
</body>
</html>
