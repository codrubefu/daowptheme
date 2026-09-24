<?php
/**
 * Lista de articole (pagina Blog) — generată din blog.html; introducerea vine din pagina „Blog”.
 * Folosită și pentru arhivele de categorie (vezi category.php).
 */
get_header();

$current_cat = is_category() ? get_queried_object_id() : 0;
$filters     = [ 'stiri', 'competitii', 'stagii', 'examene', 'despre-qkd', 'qkd', 'covodao', 'tamthe', 'istoria-clubului', 'eseuri', 'povestiri' ];
?>

<main>
  <div id="listView">
    <section class="pagehead">
      <div class="wrap">
        <?php if ( $current_cat ) : ?>
        <p class="eyebrow">Blog & Știri</p>
        <h1><?php echo esc_html( single_cat_title( '', false ) ); ?></h1>
        <?php echo wp_kses_post( wpautop( category_description() ) ); ?>
        <?php else :
          // Introducerea vine din conținutul paginii „Blog” (editabil din admin).
          echo apply_filters( 'the_content', get_post_field( 'post_content', get_option( 'page_for_posts' ) ) );
        endif; ?>
      </div>
    </section>

    <section class="wrap">
      <div class="filters" id="filters">
        <a href="<?php echo esc_url( dao_blog_url() ); ?>"<?php echo $current_cat ? '' : ' class="active"'; ?>>Toate</a>
        <?php foreach ( $filters as $slug ) :
          $cat = get_category_by_slug( $slug );
          if ( ! $cat || ! $cat->count ) { continue; } ?>
        <a href="<?php echo esc_url( get_category_link( $cat ) ); ?>"<?php echo $current_cat === $cat->term_id ? ' class="active"' : ''; ?>><?php echo esc_html( $cat->name ); ?></a>
        <?php endforeach; ?>
      </div>

      <?php if ( have_posts() ) : ?>
      <div class="grid" id="grid">
        <?php while ( have_posts() ) : the_post(); get_template_part( 'template-parts/card' ); endwhile; ?>
      </div>
      <?php else : ?>
      <p style="padding:44px 0 80px; opacity:0.7;">Nu există articole în această categorie.</p>
      <?php endif; ?>
    </section>

    <section class="wrap">
      <div class="cta-strip">
        <div>
          <h3>Vrei să afli mai multe despre club?</h3>
          <p>Vezi programul de antrenament sau ia legătura direct cu instructorii.</p>
        </div>
        <a class="btn" href="<?php echo esc_url( home_url( '/#contact' ) ); ?>">Contact</a>
      </div>
    </section>
  </div>
</main>

<?php get_footer();
