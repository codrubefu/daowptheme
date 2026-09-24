<?php
/**
 * Prima pagină — conținutul (secțiunile din index.html) vine din pagina „Acasă”,
 * împărțită în zone (grupuri denumite: Hero, Despre club, Program…) editabile din editorul de blocuri.
 */
get_header();
?>

<main id="top">
<?php while ( have_posts() ) : the_post(); the_content(); endwhile; ?>
</main>

<?php get_footer();
