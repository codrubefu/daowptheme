<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site">
  <div class="wrap nav">
    <a href="<?php echo esc_url( is_front_page() ? '#top' : home_url( '/' ) ); ?>" class="brand">
      <span class="mark" role="img" aria-label="Sigla Clubului DAO"></span>
      <span>Club DAO<small>Qwan Ki Do · Iași</small></span>
    </a>
    <nav class="links">
      <?php dao_menu_links(); ?>
    </nav>
    <?php if ( is_front_page() ) : ?>
    <a class="btn navbtn" href="#contact" style="display:inline-flex;">Înscrie-te</a>
    <?php endif; ?>
    <button class="burger" aria-label="Meniu" onclick="document.getElementById('mnav').classList.toggle('open')">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
    </button>
  </div>
  <div id="mnav" style="display:none; border-top:1px solid var(--line);">
    <div class="wrap" style="display:flex; flex-direction:column; padding:18px 24px; gap:16px; font-size:15px;">
      <?php dao_menu_links( false ); ?>
    </div>
  </div>
</header>
