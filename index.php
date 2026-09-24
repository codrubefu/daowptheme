<?php
/**
 * Fallback (404, căutare etc.).
 */
get_header();
?>

<main>
  <div id="postView" style="display:block;">
    <div class="wrap">
      <?php if ( have_posts() && ! is_404() ) : ?>
        <h1><?php echo is_search() ? 'Rezultate: ' . esc_html( get_search_query() ) : esc_html( wp_get_document_title() ); ?></h1>
        <div class="grid" style="margin-top:36px;">
          <?php while ( have_posts() ) : the_post(); get_template_part( 'template-parts/card' ); endwhile; ?>
        </div>
      <?php else : ?>
        <p class="eyebrow">404</p>
        <h1>Pagina nu a fost găsită.</h1>
        <div class="content"><p>Mergi la <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color:var(--crimson);">prima pagină</a> sau la <a href="<?php echo esc_url( dao_blog_url() ); ?>" style="color:var(--crimson);">blog</a>.</p></div>
      <?php endif; ?>
    </div>
  </div>
</main>

<?php get_footer();
