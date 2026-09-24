<?php
/**
 * Pagini simple (create din admin) — stilul articolelor de blog.
 */
get_header();
?>

<main>
<?php while ( have_posts() ) : the_post(); ?>
  <div id="postView" style="display:block;">
    <div class="wrap">
      <h1><?php the_title(); ?></h1>
      <div class="content"><?php the_content(); ?></div>
    </div>
  </div>
<?php endwhile; ?>
</main>

<?php get_footer();
