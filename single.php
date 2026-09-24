<?php
/**
 * Pagina unui articol — generată din vizualizarea „postView” din blog.html.
 */
get_header();
?>

<main>
<?php while ( have_posts() ) : the_post();
  $byline = get_post_meta( get_the_ID(), 'byline', true );
  $source = get_post_meta( get_the_ID(), 'source_url', true );
?>
  <div id="postView" style="display:block;">
    <div class="wrap">
      <a class="back" href="<?php echo esc_url( dao_blog_url() ); ?>">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
        Înapoi la blog
      </a>
      <div class="meta">
        <span class="eyebrow"><?php echo esc_html( dao_tag_label() ); ?></span>
      </div>
      <h1><?php the_title(); ?></h1>
      <p class="sub"><?php echo esc_html( get_the_date() . ( $byline ? ' · ' . $byline : '' ) ); ?></p>
      <div class="content">
        <?php
        if ( trim( get_the_content() ) ) {
          the_content();
        } else {
          echo '<p>Acest titlu face parte din arhiva clubului, dar textul integral nu a fost încă transcris aici. Poți citi articolul original folosind linkul de mai jos.</p>';
        }
        ?>
      </div>
      <?php if ( $source ) : ?>
      <div class="source">Articol original publicat pe site-ul vechi al clubului: <a href="<?php echo esc_url( $source ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $source ); ?></a></div>
      <?php endif; ?>
    </div>
  </div>
<?php endwhile; ?>
</main>

<?php get_footer();
