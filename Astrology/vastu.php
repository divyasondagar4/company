<?php
$pageTitle = 'Vastu Shastra';
require_once 'header.php';
?>

<!-- Page Header -->
<div class="page-header">
  <div class="container">
    <h1><i class="fas fa-compass me-2"></i><?php echo t('vastu_shastra'); ?></h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/"><?php echo t('home'); ?></a></li>
        <li class="breadcrumb-item active"><?php echo t('vastu_shastra'); ?></li>
      </ol>
    </nav>
  </div>
</div>

<section class="section-sacred">
  <div class="container">
    <div class="row align-items-center mb-5">
      <div class="col-lg-4 text-center">
        <div class="vastu-direction animate-on-scroll"></div>
      </div>
      <div class="col-lg-8">
        <div class="animate-on-scroll">
          <h2><?php echo t('vastu_ancient_science'); ?></h2>
          <div class="hero-divine-line"></div>
          <p><?php echo t('vastu_intro_text'); ?></p>
        </div>
      </div>
    </div>

    <!-- Directions -->
    <div class="section-header">
      <h2><?php echo t('vastu_directions_title'); ?></h2>
      <div class="header-line"></div>
    </div>

    <div class="row g-4">
      <div class="col-md-6 col-lg-3 animate-on-scroll">
        <div class="sacred-card text-center">
          <div class="card-icon mx-auto" style="background:linear-gradient(135deg, #FFE4B5, #FFD700);">
            <i class="fas fa-arrow-up" style="color:#B8860B;"></i>
          </div>
          <h4><?php echo t('north'); ?></h4>
          <p><?php echo t('north_desc'); ?></p>
        </div>
      </div>
      <div class="col-md-6 col-lg-3 animate-on-scroll">
        <div class="sacred-card text-center">
          <div class="card-icon mx-auto" style="background:linear-gradient(135deg, #FFDAB9, #FF8C00);">
            <i class="fas fa-arrow-right" style="color:#FF6347;"></i>
          </div>
          <h4><?php echo t('east'); ?></h4>
          <p><?php echo t('east_desc'); ?></p>
        </div>
      </div>
      <div class="col-md-6 col-lg-3 animate-on-scroll">
        <div class="sacred-card text-center">
          <div class="card-icon mx-auto" style="background:linear-gradient(135deg, #E0E0E0, #A0A0A0);">
            <i class="fas fa-arrow-down" style="color:#696969;"></i>
          </div>
          <h4><?php echo t('south'); ?></h4>
          <p><?php echo t('south_desc'); ?></p>
        </div>
      </div>
      <div class="col-md-6 col-lg-3 animate-on-scroll">
        <div class="sacred-card text-center">
          <div class="card-icon mx-auto" style="background:linear-gradient(135deg, #E0F0FF, #87CEEB);">
            <i class="fas fa-arrow-left" style="color:#4682B4;"></i>
          </div>
          <h4><?php echo t('west'); ?></h4>
          <p><?php echo t('west_desc'); ?></p>
        </div>
      </div>
    </div>

    <!-- Vastu Tips -->
    <div class="mt-5">
      <div class="section-header">
        <h2><?php echo t('vastu_tips'); ?></h2>
        <div class="header-line"></div>
      </div>

      <div class="row g-4">
        <div class="col-md-6 animate-on-scroll">
          <div class="sacred-card">
            <h4><i class="fas fa-door-open me-2" style="color:var(--chandan-gold);"></i><?php echo t('main_entrance'); ?></h4>
            <ul style="color:var(--text-secondary);">
              <li><?php echo t('vastu_tip_entrance_1'); ?></li>
              <li><?php echo t('vastu_tip_entrance_2'); ?></li>
              <li><?php echo t('vastu_tip_entrance_3'); ?></li>
              <li><?php echo t('vastu_tip_entrance_4'); ?></li>
            </ul>
          </div>
        </div>
        <div class="col-md-6 animate-on-scroll">
          <div class="sacred-card">
            <h4><i class="fas fa-bed me-2" style="color:var(--chandan-gold);"></i><?php echo t('bedroom'); ?></h4>
            <ul style="color:var(--text-secondary);">
              <li><?php echo t('vastu_tip_bedroom_1'); ?></li>
              <li><?php echo t('vastu_tip_bedroom_2'); ?></li>
              <li><?php echo t('vastu_tip_bedroom_3'); ?></li>
              <li><?php echo t('vastu_tip_bedroom_4'); ?></li>
            </ul>
          </div>
        </div>
        <div class="col-md-6 animate-on-scroll">
          <div class="sacred-card">
            <h4><i class="fas fa-utensils me-2" style="color:var(--chandan-gold);"></i><?php echo t('kitchen'); ?></h4>
            <ul style="color:var(--text-secondary);">
              <li><?php echo t('vastu_tip_kitchen_1'); ?></li>
              <li><?php echo t('vastu_tip_kitchen_2'); ?></li>
              <li><?php echo t('vastu_tip_kitchen_3'); ?></li>
              <li><?php echo t('vastu_tip_kitchen_4'); ?></li>
            </ul>
          </div>
        </div>
        <div class="col-md-6 animate-on-scroll">
          <div class="sacred-card">
            <h4><i class="fas fa-pray me-2" style="color:var(--chandan-gold);"></i><?php echo t('pooja_room'); ?></h4>
            <ul style="color:var(--text-secondary);">
              <li><?php echo t('vastu_tip_pooja_1'); ?></li>
              <li><?php echo t('vastu_tip_pooja_2'); ?></li>
              <li><?php echo t('vastu_tip_pooja_3'); ?></li>
              <li><?php echo t('vastu_tip_pooja_4'); ?></li>
            </ul>
          </div>
        </div>
      </div>
    </div>

  </div>
</section>

<?php require_once 'footer.php'; ?>
