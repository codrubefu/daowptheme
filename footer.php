<footer>
  <div class="wrap foot-row">
    <span style="display:flex; align-items:center; gap:10px;">
      <span style="width:26px; height:26px; border-radius:50%; background-image:var(--logo-circle); background-size:contain; background-position:center; background-repeat:no-repeat; flex-shrink:0;" role="img" aria-label="Sigla Clubului DAO"></span>
      Club DAO — Qwan Ki Do Iași · © <?php echo esc_html( wp_date( 'Y' ) ); ?>. Toate drepturile rezervate.
    </span>
    <span><?php echo is_front_page() ? 'Site reconstruit într-o formă modernă · conținut original preluat de pe dao.ovio.ro' : 'Arhivă reconstruită după conținutul publicat pe dao.ovio.ro'; ?></span>
  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
