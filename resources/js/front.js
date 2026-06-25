/**
 * OsteoBook – Front-end booking form
 */
( function () {
    'use strict';

    const config = window.osteoBookConfig;
    if ( ! config ) return;

    const form = document.getElementById( 'osteobook-form' );
    if ( ! form ) return;

    const dateInput = document.getElementById( 'osteobook-date' );
    const slotsWrap = document.getElementById( 'osteobook-slots-wrap' );
    const slotsEl = document.getElementById( 'osteobook-slots' );
    const slotInput = document.getElementById( 'osteobook-slot-input' );
    const successEl = document.getElementById( 'osteobook-success' );
    const errorsEl = document.getElementById( 'osteobook-errors' );
    const summaryEl = document.getElementById( 'osteobook-summary' );
    const btnNext = document.getElementById( 'osteobook-btn-next' );
    const btnPrev = document.getElementById( 'osteobook-btn-prev' );
    const btnSubmit = document.getElementById( 'osteobook-btn-submit' );
    const steps = [ ...form.querySelectorAll( '.osteobook-step' ) ];

    let currentStep = 1;

    /* ── Date change → fetch slots ── */
    dateInput.addEventListener( 'change', async () => {
        const date = dateInput.value;
        if ( ! date ) return;

        slotInput.value = '';
        btnNext.disabled = true;
        slotsEl.innerHTML = `<span class="osteobook-loading">${ config.i18n.loading }</span>`;
        slotsWrap.style.display = 'block';

        try {
            const res  = await apiFetch( `${ config.apiUrl }/slots?date=${ encodeURIComponent( date ) }` );
            const data = await res.json();

            renderSlots( data.slots || [] );
        } catch {
            slotsEl.innerHTML = `<span class="osteobook-empty">${ config.i18n.noSlots }</span>`;
        }
    } );

    function renderSlots( slots ) {
        if ( ! slots.length ) {
            slotsEl.innerHTML = `<span class="osteobook-empty">${ config.i18n.noSlots }</span>`;
            return;
        }

        slotsEl.innerHTML = slots
            .map( s => `<button type="button" class="osteobook-slot-btn" data-slot="${ escHtml( s ) }">${ escHtml( s ) }</button>` )
            .join( '' );

        slotsEl.querySelectorAll( '.osteobook-slot-btn' ).forEach( btn => {
            btn.addEventListener( 'click', () => {
                slotsEl.querySelectorAll( '.osteobook-slot-btn' ).forEach( b => b.classList.remove( 'is-active' ) );
                btn.classList.add( 'is-active' );
                slotInput.value  = btn.dataset.slot;
                btnNext.disabled = false;
            } );
        } );
    }

    /* ── Step navigation ── */
    btnNext.addEventListener( 'click', () => {
        if ( ! dateInput.value || ! slotInput.value ) return;
        updateSummary();
        showStep( 2 );
    } );

    btnPrev.addEventListener( 'click', () => {
        showStep( 1 );
    } );

    function showStep( step ) {
        currentStep = step;
        steps.forEach( s => {
            s.style.display = parseInt( s.dataset.step ) === step ? '' : 'none';
        } );
        btnPrev.style.display   = step > 1 ? '' : 'none';
        btnNext.style.display   = step < steps.length ? '' : 'none';
        btnSubmit.style.display = step === steps.length ? '' : 'none';
    }

    function updateSummary() {
        if ( ! summaryEl ) return;
        summaryEl.textContent = `📅 ${ dateInput.value }  ·  🕐 ${ slotInput.value }`;
    }

    /* ── Form submit ── */
    form.addEventListener( 'submit', async e => {
        e.preventDefault();
        clearMessages();

        const originalLabel  = btnSubmit.textContent;
        btnSubmit.disabled   = true;
        btnSubmit.textContent = config.i18n.submitting;

        const payload = Object.fromEntries( new FormData( form ) );

        try {
            const res  = await apiFetch( `${ config.apiUrl }/reservations`, {
                method:  'POST',
                headers: { 'Content-Type': 'application/json' },
                body:    JSON.stringify( payload ),
            } );
            const data = await res.json();

            if ( res.ok && data.success ) {
                form.style.display        = 'none';
                successEl.textContent     = data.message || config.i18n.success;
                successEl.style.display   = 'block';
                successEl.scrollIntoView( { behavior: 'smooth' } );
            } else {
                showErrors( data.errors || [ config.i18n.error ] );
                btnSubmit.disabled   = false;
                btnSubmit.textContent = originalLabel;
            }
        } catch {
            showErrors( [ config.i18n.error ] );
            btnSubmit.disabled   = false;
            btnSubmit.textContent = originalLabel;
        }
    } );

    /* ── Helpers ── */
    function apiFetch( url, options = {} ) {
        return fetch( url, {
            ...options,
            headers: {
                'X-WP-Nonce': config.nonce,
                ...( options.headers || {} ),
            },
        } );
    }

    function showErrors( errors ) {
        errorsEl.innerHTML    = errors.map( e => `<p>${ escHtml( e ) }</p>` ).join( '' );
        errorsEl.style.display = 'block';
        errorsEl.scrollIntoView( { behavior: 'smooth' } );
    }

    function clearMessages() {
        successEl.style.display = 'none';
        errorsEl.style.display  = 'none';
    }

    function escHtml( str ) {
        return String( str )
            .replace( /&/g, '&amp;' )
            .replace( /</g, '&lt;' )
            .replace( />/g, '&gt;' )
            .replace( /"/g, '&quot;' );
    }
} )();
