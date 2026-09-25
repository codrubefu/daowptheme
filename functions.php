<?php
/**
 * Club DAO theme setup.
 */

add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', [ 'search-form', 'gallery', 'caption', 'style', 'script' ] );
	add_theme_support( 'editor-styles' );
	register_nav_menus( [ 'primary' => 'Meniu principal' ] );
} );

/**
 * Zonele primei pagini sunt blocuri „Grup” cu clasele din index.html (hero, about-grid, qkd-grid…).
 * În temele clasice WordPress înfășoară conținutul grupului într-un div suplimentar, care strică grilele CSS.
 */
remove_filter( 'render_block_core/group', 'wp_restore_group_inner_container' );

/** În editor, prima pagină arată ca pe site (front.css); restul paginilor și articolele cu blog.css. */
add_action( 'current_screen', function ( $screen ) {
	if ( 'post' !== $screen->base ) {
		return;
	}
	$post_id = (int) ( $_GET['post'] ?? 0 );
	$sheet   = $post_id && $post_id === (int) get_option( 'page_on_front' ) ? 'front' : 'blog';
	add_editor_style( [
		'assets/css/fonts.css',
		"assets/css/{$sheet}.css",
		'assets/css/editor.css',
	] );
} );

/** Prima pagină folosește stilul din index.html, restul site-ului pe cel din blog.html. */
add_action( 'wp_enqueue_scripts', function () {
	$ver   = wp_get_theme()->get( 'Version' );
	$sheet = is_front_page() ? 'front' : 'blog';

	if ( is_front_page() ) {
		// Pe prima pagină CSS-ul e pus direct în HTML: o cerere în minus înainte de afișare (LCP mai mic).
		foreach ( [ 'fonts', 'front' ] as $name ) {
			$css = file_get_contents( get_theme_file_path( "assets/css/{$name}.css" ) );
			$css = str_replace( 'url(../', 'url(' . get_theme_file_uri( 'assets/' ), $css );
			wp_register_style( "dao-{$name}", false, [], $ver );
			wp_enqueue_style( "dao-{$name}" );
			wp_add_inline_style( "dao-{$name}", $css );
		}
	} else {
		wp_enqueue_style( 'dao-fonts', get_theme_file_uri( 'assets/css/fonts.css' ), [], $ver );
		wp_enqueue_style( 'dao-blog', get_theme_file_uri( 'assets/css/blog.css' ), [ 'dao-fonts' ], $ver );
	}

	// Imaginile erau inline (base64) în HTML-ul original; acum sunt fișiere în temă.
	wp_add_inline_style( 'dao-' . $sheet, sprintf(
		':root{--logo-circle:url("%s");--logo-full:url("%s");}',
		esc_url( get_theme_file_uri( 'assets/img/logo-circle.png' ) ),
		esc_url( get_theme_file_uri( 'assets/img/logo-full.png' ) )
	) );

	// Hero-ul primei pagini e un slider: slide-urile sunt blocuri „Grup” cu clasa „slide” în pagina „Acasă”.
	if ( is_front_page() ) {
		wp_enqueue_script( 'dao-hero-slider', get_theme_file_uri( 'assets/js/hero-slider.js' ), [], $ver, [ 'in_footer' => true, 'strategy' => 'defer' ] );
	}
} );

add_action( 'wp_head', function () {
	echo '<link rel="icon" href="' . esc_url( get_theme_file_uri( 'assets/img/logo-circle.png' ) ) . '">' . "\n";

	// Descrierea pentru motoarele de căutare: rezumatul articolului/paginii, altfel sloganul site-ului.
	$desc = is_singular() && ! is_front_page() ? get_the_excerpt() : get_bloginfo( 'description' );
	$desc = wp_trim_words( wp_strip_all_tags( $desc ), 30, '…' );
	if ( $desc ) {
		echo '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n";
	}
}, 1 );

/** Scriptul de emoji al WordPress nu e folosit, dar blochează încărcarea paginii. */
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'wp_enqueue_scripts', 'wp_enqueue_emoji_styles' );

/** Fonturile sunt în temă: se cer odată cu CSS-ul, ca textul să apară direct cu fontul final (fără salt de layout). */
add_action( 'wp_head', function () {
	foreach ( [ 'work-sans-ro', 'cormorant-garamond-ro' ] as $font ) {
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( get_theme_file_uri( "assets/fonts/{$font}.woff2" ) )
		);
	}
}, 2 );

/**
 * Imaginea primului slide din hero e LCP-ul paginii: se cere cu prioritate mare, fără lazy-load.
 * Slide-urile ascunse vin după ea, cu prioritate mică, ca să nu-i ia din bandă.
 */
add_filter( 'render_block_core/group', function ( $html, $block ) {
	static $slide = 0;
	$classes = ' ' . ( $block['attrs']['className'] ?? '' ) . ' ';
	if ( ! is_front_page() || ! in_the_loop() || ! str_contains( $classes, ' slide ' ) ) {
		return $html;
	}
	$first = 0 === $slide++;
	$img   = new WP_HTML_Tag_Processor( $html );
	if ( $img->next_tag( 'img' ) ) {
		$img->set_attribute( 'fetchpriority', $first ? 'high' : 'low' );
		$img->remove_attribute( 'loading' );
		$html = $img->get_updated_html();
	}
	return $html;
}, 10, 2 );

/** Blogul afișează toate articolele pe o singură pagină, ca în blog.html. */
add_action( 'pre_get_posts', function ( $q ) {
	if ( ! is_admin() && $q->is_main_query() && ( $q->is_home() || $q->is_category() ) ) {
		$q->set( 'posts_per_page', -1 );
	}
} );

/** URL-ul paginii de blog (pagina setată ca „Pagina articolelor”). */
function dao_blog_url() {
	$id = (int) get_option( 'page_for_posts' );
	return $id ? get_permalink( $id ) : home_url( '/' );
}

/** Link către o secțiune a primei pagini (ancoră locală pe prima pagină). */
function dao_section_url( $id ) {
	return is_front_page() ? '#' . $id : home_url( '/#' . $id );
}

/** Eticheta afișată deasupra articolului: meta „tag_label” sau categoriile articolului. */
function dao_tag_label( $post = null ) {
	$label = get_post_meta( get_the_ID(), 'tag_label', true );
	if ( $label ) {
		return $label;
	}
	$cats = get_the_category( $post );
	return implode( ' · ', wp_list_pluck( $cats, 'name' ) );
}

/**
 * Link-urile meniului din antet: meniul din Aspect → Meniuri pus pe „Meniu principal”,
 * altfel link-urile implicite. Se afișează ca <a> simple, ca în designul original.
 */
function dao_menu_links( $mark_active = true ) {
	$links     = [];
	$locations = get_nav_menu_locations();
	$items     = empty( $locations['primary'] ) ? false : wp_get_nav_menu_items( $locations['primary'] );

	if ( $items ) {
		foreach ( $items as $item ) {
			if ( ! $item->menu_item_parent ) {
				$links[] = [ $item->url, $item->title, $item->target ];
			}
		}
	} else {
		$links = [
			[ dao_section_url( 'club' ), 'Despre club' ],
			[ dao_section_url( 'qkd' ), 'Qwan Ki Do' ],
			[ dao_section_url( 'program' ), 'Program' ],
			[ dao_blog_url(), 'Blog' ],
			[ dao_section_url( 'contact' ), 'Contact' ],
		];
	}

	$on_blog = is_home() || is_singular( 'post' ) || is_category() || is_archive();
	foreach ( $links as $link ) {
		$url = $link[0];
		// Pe prima pagină, ancorele spre secțiunile ei rămân locale (#club), fără reîncărcare.
		if ( is_front_page() && str_starts_with( $url, home_url( '/#' ) ) ) {
			$url = substr( $url, strlen( home_url( '/' ) ) );
		}
		$active = $mark_active && $on_blog && untrailingslashit( $link[0] ) === untrailingslashit( dao_blog_url() );
		printf(
			'<a href="%s"%s%s>%s</a>' . "\n",
			esc_url( $url ),
			$active ? ' class="active"' : '',
			empty( $link[2] ) ? '' : ' target="' . esc_attr( $link[2] ) . '" rel="noopener"',
			esc_html( $link[1] )
		);
	}
}
