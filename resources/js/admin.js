/**
 * OsteoBook – Admin (FullCalendar + Availability settings)
 */
( function () {
    'use strict';

    const config = window.osteoBookAdmin;
    if ( ! config ) return;

    /* ══════════════════════════════════════════
       FullCalendar
    ══════════════════════════════════════════ */
    const calendarEl = document.getElementById( 'osteobook-calendar' );

    if ( calendarEl && typeof FullCalendar !== 'undefined' ) {
        const calendar = new FullCalendar.Calendar( calendarEl, {
            initialView:   'timeGridWeek',
            locale:        'fr',
            headerToolbar: {
                left:   'prev,next today',
                center: 'title',
                right:  'dayGridMonth,timeGridWeek,timeGridDay',
            },
            slotMinTime:  '07:00:00',
            slotMaxTime:  '20:00:00',
            allDaySlot:   false,
            height:       'auto',
            events:       async ( fetchInfo, successCallback, failureCallback ) => {
                try {
                    const url = `${ config.apiUrl }/calendar/events?start=${ encodeURIComponent( fetchInfo.startStr ) }&end=${ encodeURIComponent( fetchInfo.endStr ) }`;
                    const res = await apiFetch( url );

                    if ( ! res.ok ) throw new Error( 'Fetch failed' );

                    successCallback( await res.json() );
                } catch ( err ) {
                    failureCallback( err );
                }
            },
            eventClick: info => {
                if ( info.event.url ) {
                    info.jsEvent.preventDefault();
                    window.open( info.event.url, '_blank', 'noopener' );
                }
            },
            eventDidMount: info => {
                // Tooltip with details
                info.el.title = info.event.title;
            },
        } );

        calendar.render();
    }

    /* ══════════════════════════════════════════
       Weekly availability form
    ══════════════════════════════════════════ */
    const weeklyForm = document.getElementById( 'osteobook-weekly-form' );

    if ( weeklyForm ) {
        weeklyForm.addEventListener( 'submit', async e => {
            e.preventDefault();

            const formData = new FormData( weeklyForm );
            const weekly   = {};

            for ( const [ key, value ] of formData.entries() ) {
                const match = key.match( /^days\[(\w+)\]$/ );
                if ( match ) {
                    const day  = match[ 1 ];
                    const raw  = String( value ).trim();
                    weekly[ day ] = raw
                        ? raw.split( /\s+/ ).filter( s => /^\d{2}:\d{2}$/.test( s ) )
                        : [];
                }
            }

            try {
                const res = await apiFetch( `${ config.apiUrl }/availability`, {
                    method:  'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body:    JSON.stringify( { weekly } ),
                } );

                showNotice( res.ok ? config.i18n.saved : config.i18n.error, res.ok ? 'success' : 'error' );
            } catch {
                showNotice( config.i18n.networkError, 'error' );
            }
        } );
    }

    /* ══════════════════════════════════════════
       Exception form
    ══════════════════════════════════════════ */
    const exceptionForm = document.getElementById( 'osteobook-exception-form' );

    if ( exceptionForm ) {
        exceptionForm.addEventListener( 'submit', async e => {
            e.preventDefault();

            const date    = document.getElementById( 'osteobook-ex-date' ).value;
            const rawSlots = document.getElementById( 'osteobook-ex-slots' ).value.trim();
            const slots   = rawSlots
                ? rawSlots.split( /\s+/ ).filter( s => /^\d{2}:\d{2}$/.test( s ) )
                : [];

            try {
                const res = await apiFetch( `${ config.apiUrl }/availability/exception`, {
                    method:  'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body:    JSON.stringify( { date, slots } ),
                } );

                if ( res.ok ) {
                    showNotice( config.i18n.saved, 'success' );
                    setTimeout( () => window.location.reload(), 800 );
                } else {
                    showNotice( config.i18n.error, 'error' );
                }
            } catch {
                showNotice( config.i18n.networkError, 'error' );
            }
        } );
    }

    /* ══════════════════════════════════════════
       Delete exception buttons
    ══════════════════════════════════════════ */
    document.querySelectorAll( '.osteobook-delete-exception' ).forEach( btn => {
        btn.addEventListener( 'click', async () => {
            if ( ! window.confirm( config.i18n.confirmDelete ) ) return;

            const date = btn.dataset.date;

            try {
                const res = await apiFetch( `${ config.apiUrl }/availability/exception`, {
                    method:  'DELETE',
                    headers: { 'Content-Type': 'application/json' },
                    body:    JSON.stringify( { date } ),
                } );

                if ( res.ok ) {
                    btn.closest( 'tr' )?.remove();
                    showNotice( config.i18n.saved, 'success' );
                } else {
                    showNotice( config.i18n.error, 'error' );
                }
            } catch {
                showNotice( config.i18n.networkError, 'error' );
            }
        } );
    } );

    /* ══════════════════════════════════════════
       Helpers
    ══════════════════════════════════════════ */
    function apiFetch( url, options = {} ) {
        return fetch( url, {
            ...options,
            headers: {
                'X-WP-Nonce': config.nonce,
                ...( options.headers || {} ),
            },
        } );
    }

    function showNotice( message, type ) {
        document.querySelector( '.osteobook-notice' )?.remove();

        const notice = document.createElement( 'div' );
        notice.className = `notice notice-${ type } is-dismissible osteobook-notice`;
        notice.innerHTML = `<p>${ message }</p>`;

        const h1 = document.querySelector( '.osteobook-admin-wrap h1' );
        h1?.insertAdjacentElement( 'afterend', notice );

        setTimeout( () => notice.remove(), 4000 );
    }
} )();
