<?php
/**
 * Cardul unui articol din listă.
 */
$byline = get_post_meta( get_the_ID(), 'byline', true );
?>
<a class="card" href="<?php the_permalink(); ?>">
  <span class="tag"><?php echo esc_html( dao_tag_label() ); ?></span>
  <h2><?php the_title(); ?></h2>
  <p><?php echo esc_html( get_the_excerpt() ?: 'Titlu original din arhiva clubului — rezumat indisponibil, vezi sursa originală.' ); ?></p>
  <?php if ( $byline ) : ?><span class="byline"><?php echo esc_html( $byline ); ?></span><?php endif; ?>
  <span class="date"><?php echo esc_html( get_the_date() ); ?></span>
  <span class="readmore">Citește articolul →</span>
</a>
