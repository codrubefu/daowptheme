/**
 * Sliderul din hero-ul primei pagini.
 * Slide-urile sunt blocuri „Grup” cu clasa „slide” din pagina „Acasă” — se adaugă/șterg din editor.
 */
( function () {
	const hero = document.querySelector( '.hero' );
	const slides = hero ? Array.from( hero.querySelectorAll( '.slide' ) ) : [];

	if ( slides.length < 2 ) {
		return;
	}

	const DELAY = 6000;
	const reduceMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
	let current = 0;
	let timer = null;

	hero.classList.add( 'is-slider' );
	hero.setAttribute( 'role', 'region' );
	hero.setAttribute( 'aria-roledescription', 'carusel' );
	hero.setAttribute( 'aria-label', 'Prezentare club' );
	slides.forEach( ( slide, i ) => {
		slide.setAttribute( 'role', 'group' );
		slide.setAttribute( 'aria-roledescription', 'slide' );
		slide.setAttribute( 'aria-label', `${ i + 1 } din ${ slides.length }` );
	} );

	const arrow = ( dir, label, path ) => {
		const b = document.createElement( 'button' );
		b.type = 'button';
		b.className = `hero-arrow hero-${ dir }`;
		b.setAttribute( 'aria-label', label );
		b.innerHTML = `<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8"><path d="${ path }"/></svg>`;
		return b;
	};
	const nav = document.createElement( 'div' );
	nav.className = 'hero-nav';
	const prev = arrow( 'prev', 'Slide-ul anterior', 'M15 18l-6-6 6-6' );
	const next = arrow( 'next', 'Slide-ul următor', 'M9 6l6 6-6 6' );
	const dotsWrap = document.createElement( 'div' );
	dotsWrap.className = 'hero-dots';
	const dots = slides.map( ( _, i ) => {
		const d = document.createElement( 'button' );
		d.type = 'button';
		d.className = 'hero-dot';
		d.setAttribute( 'aria-label', `Slide-ul ${ i + 1 }` );
		d.addEventListener( 'click', () => { show( i ); restart(); } );
		dotsWrap.appendChild( d );
		return d;
	} );
	nav.append( prev, dotsWrap, next );
	hero.querySelector( '.hero-slides' ).after( nav );

	function show( n ) {
		current = ( n + slides.length ) % slides.length;
		slides.forEach( ( slide, i ) => {
			const active = i === current;
			slide.classList.toggle( 'is-active', active );
			slide.setAttribute( 'aria-hidden', String( ! active ) );
			slide.inert = ! active;
		} );
		dots.forEach( ( d, i ) => d.setAttribute( 'aria-current', String( i === current ) ) );
	}
	function stop() {
		clearInterval( timer );
		timer = null;
	}
	function play() {
		if ( reduceMotion || timer ) {
			return;
		}
		timer = setInterval( () => show( current + 1 ), DELAY );
	}
	function restart() {
		stop();
		play();
	}

	prev.addEventListener( 'click', () => { show( current - 1 ); restart(); } );
	next.addEventListener( 'click', () => { show( current + 1 ); restart(); } );

	// Pauză cât timp utilizatorul citește / navighează cu tastatura în hero.
	hero.addEventListener( 'mouseenter', stop );
	hero.addEventListener( 'mouseleave', play );
	hero.addEventListener( 'focusin', stop );
	hero.addEventListener( 'focusout', ( e ) => { if ( ! hero.contains( e.relatedTarget ) ) { play(); } } );
	document.addEventListener( 'visibilitychange', () => ( document.hidden ? stop() : play() ) );

	hero.addEventListener( 'keydown', ( e ) => {
		if ( e.key === 'ArrowLeft' ) { show( current - 1 ); }
		if ( e.key === 'ArrowRight' ) { show( current + 1 ); }
	} );

	// Swipe pe telefon.
	let startX = null;
	hero.addEventListener( 'touchstart', ( e ) => { startX = e.touches[ 0 ].clientX; }, { passive: true } );
	hero.addEventListener( 'touchend', ( e ) => {
		if ( startX === null ) {
			return;
		}
		const dx = e.changedTouches[ 0 ].clientX - startX;
		startX = null;
		if ( Math.abs( dx ) > 50 ) {
			show( current + ( dx < 0 ? 1 : -1 ) );
			restart();
		}
	}, { passive: true } );

	show( 0 );
	play();
} )();
