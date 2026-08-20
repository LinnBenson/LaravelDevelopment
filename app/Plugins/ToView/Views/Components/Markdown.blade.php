@props([
    'rid' => 'markdown-'.uniqid(),
    'name' => '',
    'value' => '',
    'placeholder' => '',
])

<!-- Markdown editor -->
<div class="toview-markdown" data-markdown-editor="{{$rid}}">
    <div class="toview-markdown-toolbar" role="toolbar" aria-label="Markdown editor toolbar">
        <div class="toview-markdown-tools">
            <button type="button" data-action="bold" title="Bold (Ctrl+B)" aria-label="Bold"><i class="bi bi-type-bold"></i></button>
            <button type="button" data-action="italic" title="Italic (Ctrl+I)" aria-label="Italic"><i class="bi bi-type-italic"></i></button>
            <button type="button" data-action="strike" title="Strikethrough" aria-label="Strikethrough"><i class="bi bi-type-strikethrough"></i></button>
            <div class="toview-markdown-heading">
                <button type="button" data-heading-toggle title="Select heading level" aria-label="Select heading level" aria-haspopup="true" aria-expanded="false">
                    <span>H1</span><i class="bi bi-chevron-down"></i>
                </button>
            </div>
        </div>
        <div class="toview-markdown-tools">
            <button type="button" data-action="quote" title="Blockquote" aria-label="Blockquote"><i class="bi bi-quote"></i></button>
            <button type="button" data-action="code" title="Code" aria-label="Code"><i class="bi bi-code-slash"></i></button>
            <button type="button" data-action="link" title="Link (Ctrl+K)" aria-label="Link"><i class="bi bi-link-45deg"></i></button>
            <button type="button" data-action="image" title="Image" aria-label="Image"><i class="bi bi-image"></i></button>
        </div>
        <div class="toview-markdown-tools">
            <button type="button" data-action="unordered" title="Bulleted list" aria-label="Bulleted list"><i class="bi bi-list-ul"></i></button>
            <button type="button" data-action="ordered" title="Numbered list" aria-label="Numbered list"><i class="bi bi-list-ol"></i></button>
            <button type="button" data-action="task" title="Task list" aria-label="Task list"><i class="bi bi-list-check"></i></button>
            <button type="button" data-action="table" title="Table" aria-label="Table"><i class="bi bi-table"></i></button>
        </div>
        <div class="toview-markdown-tools toview-markdown-tools-end">
            <button type="button" data-action="undo" title="Undo (Ctrl+Z)" aria-label="Undo"><i class="bi bi-arrow-counterclockwise"></i></button>
            <button type="button" data-action="redo" title="Redo (Ctrl+Y)" aria-label="Redo"><i class="bi bi-arrow-clockwise"></i></button>
            <button type="button" data-action="preview" title="Preview (Ctrl+Shift+P)" aria-label="Preview" aria-pressed="false"><i class="bi bi-eye"></i></button>
            <button type="button" data-action="fullscreen" title="Fullscreen" aria-label="Fullscreen" aria-pressed="false"><i class="bi bi-arrows-fullscreen"></i></button>
        </div>
    </div>
    <div class="toview-markdown-heading-menu" role="menu">
        @for( $level = 1; $level <= 6; $level++ )
            <button type="button" data-heading-level="{{$level}}" role="menuitem">H{{$level}}</button>
        @endfor
    </div>
    <div class="toview-markdown-workspace">
        <textarea rid="{{$rid}}" name="{{$name}}" placeholder="{{$placeholder}}" autocomplete="off" spellcheck="true" aria-label="Markdown content" input>{{$value}}</textarea>
        <div class="toview-markdown-preview" aria-label="Markdown preview"></div>
    </div>
    <div class="toview-markdown-status" aria-live="polite">
        <span data-status="mode">Edit mode</span>
        <span><span data-status="lines">1</span> lines · <span data-status="words">0</span> characters</span>
    </div>
</div>
<script>
/* Markdown editor interactions and safe preview */
(() => {
    const editorId = @json((string) $rid);
    const root = Array.from( document.querySelectorAll( '[data-markdown-editor]' ) ).find( ( editor ) => editor.dataset.markdownEditor === editorId );
    if ( !root || root.dataset.ready === 'true' ) { return; }
    root.dataset.ready = 'true';
    const textarea = root.querySelector( 'textarea[input]' );
    const preview = root.querySelector( '.toview-markdown-preview' );
    const modeStatus = root.querySelector( '[data-status="mode"]' );
    const lineStatus = root.querySelector( '[data-status="lines"]' );
    const wordStatus = root.querySelector( '[data-status="words"]' );
    const previewButton = root.querySelector( '[data-action="preview"]' );
    const fullscreenButton = root.querySelector( '[data-action="fullscreen"]' );
    const toolbar = root.querySelector( '.toview-markdown-toolbar' );
    const heading = root.querySelector( '.toview-markdown-heading' );
    const headingToggle = root.querySelector( '[data-heading-toggle]' );
    const headingMenu = root.querySelector( '.toview-markdown-heading-menu' );
    const fullscreenPlaceholder = document.createComment( `markdown-editor-${editorId}` );
    let fullscreenScrollY = 0;
    const escapeHtml = ( value ) => String( value ).replaceAll( '&', '&amp;' ).replaceAll( '<', '&lt;' ).replaceAll( '>', '&gt;' ).replaceAll( '"', '&quot;' ).replaceAll( "'", '&#039;' );
    const safeUrl = ( value, image = false ) => {
        const url = String( value || '' ).trim();
        if ( /^(https?:|mailto:|tel:|\/|#)/i.test( url ) ) { return escapeHtml( url ); }
        if ( image && /^data:image\/(png|gif|jpeg|webp);base64,/i.test( url ) ) { return escapeHtml( url ); }
        return '#';
    };
    const inline = ( value ) => {
        let content = escapeHtml( value ); const codes = [];
        content = content.replace( /`([^`\n]+)`/g, ( match, code ) => { codes.push( `<code>${code}</code>` ); return `\u0000CODE${codes.length - 1}\u0000`; } );
        content = content.replace( /!\[([^\]]*)\]\((\S+)\)/g, ( match, alt, url ) => `<img src="${safeUrl( url, true )}" alt="${alt}">` );
        content = content.replace( /\[([^\]]+)\]\((\S+)\)/g, ( match, text, url ) => `<a href="${safeUrl( url )}" target="_blank" rel="noopener noreferrer">${text}</a>` );
        content = content.replace( /\*\*([^\n]+?)\*\*|__([^\n]+?)__/g, '<strong>$1$2</strong>' ).replace( /~~([^\n]+?)~~/g, '<del>$1</del>' );
        content = content.replace( /(^|[^*])\*([^*\n]+?)\*(?!\*)|(^|[^_])_([^_\n]+?)_(?!_)/g, '$1$3<em>$2$4</em>' );
        return content.replace( /\u0000CODE(\d+)\u0000/g, ( match, index ) => codes[Number( index )] );
    };
    const markdown = ( source ) => {
        const blocks = []; let content = String( source || '' ).replace( /\r\n?/g, '\n' );
        content = content.replace( /```([^\n]*)\n([\s\S]*?)```/g, ( match, language, code ) => {
            blocks.push( `<pre><code${language.trim() ? ` class="language-${escapeHtml( language.trim() )}"` : ''}>${escapeHtml( code.replace( /\n$/, '' ) )}</code></pre>` );
            return `\n\u0000BLOCK${blocks.length - 1}\u0000\n`;
        });
        const lines = content.split( '\n' ); const output = []; let paragraph = []; let list = null; let quote = [];
        const flushParagraph = () => { if ( paragraph.length ) { output.push( `<p>${inline( paragraph.join( '\n' ) ).replaceAll( '\n', '<br>')}</p>` ); paragraph = []; } };
        const flushList = () => { if ( list ) { output.push( `</${list}>` ); list = null; } };
        const flushQuote = () => { if ( quote.length ) { output.push( `<blockquote>${quote.map( inline ).join( '<br>' )}</blockquote>` ); quote = []; } };
        for ( let index = 0; index < lines.length; index++ ) {
            const line = lines[index]; const next = lines[index + 1] || '';
            if ( line.includes( '\u0000BLOCK' ) ) { flushParagraph(); flushList(); flushQuote(); output.push( line ); continue; }
            if ( /^\s*$/.test( line ) ) { flushParagraph(); flushList(); flushQuote(); continue; }
            const heading = line.match( /^(#{1,6})\s+(.+)$/ );
            if ( heading ) { flushParagraph(); flushList(); flushQuote(); output.push( `<h${heading[1].length}>${inline( heading[2] )}</h${heading[1].length}>` ); continue; }
            if ( /^\s*([-*_])(?:\s*\1){2,}\s*$/.test( line ) ) { flushParagraph(); flushList(); flushQuote(); output.push( '<hr>' ); continue; }
            if ( line.includes( '|' ) && /^\s*\|?\s*:?-{3,}:?/.test( next ) ) {
                flushParagraph(); flushList(); flushQuote(); const row = ( value ) => value.trim().replace( /^\||\|$/g, '' ).split( '|' ).map( ( cell ) => cell.trim() );
                const headers = row( line ); const rows = []; index += 2;
                while ( index < lines.length && lines[index].includes( '|' ) && lines[index].trim() !== '' ) { rows.push( row( lines[index] ) ); index++; } index--;
                output.push( `<table><thead><tr>${headers.map( ( cell ) => `<th>${inline( cell )}</th>` ).join( '' )}</tr></thead><tbody>${rows.map( ( cells ) => `<tr>${cells.map( ( cell ) => `<td>${inline( cell )}</td>` ).join( '' )}</tr>` ).join( '' )}</tbody></table>` ); continue;
            }
            const quoted = line.match( /^>\s?(.*)$/ );
            if ( quoted ) { flushParagraph(); flushList(); quote.push( quoted[1] ); continue; }
            const task = line.match( /^\s*[-*+]\s+\[([ xX])\]\s+(.+)$/ ); const unordered = line.match( /^\s*[-*+]\s+(.+)$/ ); const ordered = line.match( /^\s*\d+[.)]\s+(.+)$/ );
            if ( task || unordered || ordered ) {
                flushParagraph(); flushQuote(); const type = ordered ? 'ol' : 'ul';
                if ( list !== type ) { flushList(); output.push( `<${type}>` ); list = type; }
                output.push( task ? `<li class="task"><input type="checkbox" disabled${task[1].toLowerCase() === 'x' ? ' checked' : ''}>${inline( task[2] )}</li>` : `<li>${inline( ( ordered || unordered )[1] )}</li>` ); continue;
            }
            flushList(); flushQuote(); paragraph.push( line );
        }
        flushParagraph(); flushList(); flushQuote();
        return output.join( '\n' ).replace( /\u0000BLOCK(\d+)\u0000/g, ( match, index ) => blocks[Number( index )] );
    };
    const update = () => {
        preview.innerHTML = markdown( textarea.value );
        lineStatus.textContent = String( textarea.value === '' ? 1 : textarea.value.split( '\n' ).length );
        wordStatus.textContent = String( Array.from( textarea.value.replace( /\s/g, '' ) ).length );
    };
    const replaceSelection = ( before, after = '', fallback = '' ) => {
        const start = textarea.selectionStart; const end = textarea.selectionEnd; const selected = textarea.value.slice( start, end ) || fallback;
        textarea.setRangeText( `${before}${selected}${after}`, start, end, 'end' ); textarea.selectionStart = start + before.length; textarea.selectionEnd = start + before.length + selected.length;
        textarea.focus(); textarea.dispatchEvent( new Event( 'input', { bubbles: true } ) );
    };
    const prefixLines = ( prefix ) => {
        const start = textarea.value.lastIndexOf( '\n', Math.max( 0, textarea.selectionStart - 1 ) ) + 1; let end = textarea.value.indexOf( '\n', textarea.selectionEnd );
        if ( end < 0 ) { end = textarea.value.length; }
        const content = textarea.value.slice( start, end ).split( '\n' ).map( ( line, index ) => typeof prefix === 'function' ? prefix( line, index ) : `${prefix}${line}` ).join( '\n' );
        textarea.setRangeText( content, start, end, 'select' ); textarea.focus(); textarea.dispatchEvent( new Event( 'input', { bubbles: true } ) );
    };
    const setHeading = ( level ) => {
        const prefix = `${'#'.repeat( level )} `;
        const start = textarea.value.lastIndexOf( '\n', Math.max( 0, textarea.selectionStart - 1 ) ) + 1; let end = textarea.value.indexOf( '\n', textarea.selectionEnd );
        if ( end < 0 ) { end = textarea.value.length; }
        const content = textarea.value.slice( start, end ).split( '\n' ).map( ( line ) => `${prefix}${line.replace( /^#{1,6}\s+/, '' )}` ).join( '\n' );
        textarea.setRangeText( content, start, end, 'select' ); textarea.focus(); textarea.dispatchEvent( new Event( 'input', { bubbles: true } ) );
        closeHeading(); headingToggle.querySelector( 'span' ).textContent = `H${level}`;
    };
    const closeHeading = () => {
        heading.classList.remove( 'active' ); headingMenu.classList.remove( 'active' ); headingToggle.setAttribute( 'aria-expanded', 'false' );
    };
    const positionHeading = () => {
        const rootBox = root.getBoundingClientRect(); const toggleBox = headingToggle.getBoundingClientRect();
        const left = Math.min( Math.max( 4, toggleBox.left - rootBox.left ), root.clientWidth - headingMenu.offsetWidth - 4 );
        headingMenu.style.top = `${toolbar.offsetHeight + 6}px`; headingMenu.style.left = `${left}px`;
    };
    const actions = {
        bold: () => replaceSelection( '**', '**', 'bold text' ), italic: () => replaceSelection( '*', '*', 'italic text' ), strike: () => replaceSelection( '~~', '~~', 'strikethrough text' ),
        quote: () => prefixLines( '> ' ), code: () => textarea.value.slice( textarea.selectionStart, textarea.selectionEnd ).includes( '\n' ) ? replaceSelection( '```\n', '\n```', 'code' ) : replaceSelection( '`', '`', 'code' ),
        link: () => replaceSelection( '[', '](https://)', 'link text' ), image: () => replaceSelection( '![', '](https://)', 'image description' ), unordered: () => prefixLines( '- ' ),
        ordered: () => prefixLines( ( line, index ) => `${index + 1}. ${line}` ), task: () => prefixLines( '- [ ] ' ), table: () => replaceSelection( '| Heading 1 | Heading 2 |\n| --- | --- |\n| Content 1 | Content 2 |\n' ),
        undo: () => { textarea.focus(); document.execCommand( 'undo' ); update(); }, redo: () => { textarea.focus(); document.execCommand( 'redo' ); update(); },
        preview: () => {
            update(); const wasActive = root.classList.contains( 'split' ) || root.classList.contains( 'preview' ); root.classList.remove( 'split', 'preview' );
            if ( !wasActive ) { root.classList.add( window.innerWidth > 720 ? 'split' : 'preview' ); } const active = !wasActive;
            previewButton.classList.toggle( 'active', active ); previewButton.setAttribute( 'aria-pressed', String( active ) ); modeStatus.textContent = active ? ( root.classList.contains( 'split' ) ? 'Split preview' : 'Preview mode' ) : 'Edit mode';
        },
        fullscreen: () => {
            const active = !root.classList.contains( 'fullscreen' );
            if ( active ) {
                fullscreenScrollY = window.scrollY;
                root.before( fullscreenPlaceholder );
                document.body.append( root );
                root.classList.add( 'fullscreen' );
            }else {
                root.classList.remove( 'fullscreen' );
                if ( fullscreenPlaceholder.parentNode ) {
                    fullscreenPlaceholder.parentNode.insertBefore( root, fullscreenPlaceholder );
                    fullscreenPlaceholder.remove();
                }
                window.scrollTo( 0, fullscreenScrollY );
            }
            document.body.classList.toggle( 'toview-markdown-lock', active );
            fullscreenButton.classList.toggle( 'active', active ); fullscreenButton.setAttribute( 'aria-pressed', String( active ) ); fullscreenButton.querySelector( 'i' ).className = active ? 'bi bi-fullscreen-exit' : 'bi bi-arrows-fullscreen';
            textarea.focus();
        }
    };
    root.querySelectorAll( '[data-action]' ).forEach( ( button ) => button.addEventListener( 'click', () => actions[button.dataset.action]?.() ) );
    headingToggle.addEventListener( 'click', () => {
        const active = !heading.classList.contains( 'active' ); closeHeading();
        if ( active ) { heading.classList.add( 'active' ); headingMenu.classList.add( 'active' ); positionHeading(); headingToggle.setAttribute( 'aria-expanded', 'true' ); }
    });
    root.querySelectorAll( '[data-heading-level]' ).forEach( ( button ) => button.addEventListener( 'click', () => setHeading( Number( button.dataset.headingLevel ) ) ) );
    toolbar.addEventListener( 'scroll', () => { if ( heading.classList.contains( 'active' ) ) { positionHeading(); } } );
    document.addEventListener( 'click', ( event ) => {
        if ( heading.contains( event.target ) || headingMenu.contains( event.target ) ) { return; } closeHeading();
    });
    textarea.addEventListener( 'input', update );
    textarea.addEventListener( 'keydown', ( event ) => {
        if ( event.key === 'Tab' ) { event.preventDefault(); replaceSelection( '    ' ); return; }
        if ( !( event.ctrlKey || event.metaKey ) ) { return; } const key = event.key.toLowerCase();
        if ( ['b', 'i', 'k'].includes( key ) ) { event.preventDefault(); actions[key === 'b' ? 'bold' : key === 'i' ? 'italic' : 'link'](); }
        if ( event.shiftKey && key === 'p' ) { event.preventDefault(); actions.preview(); }
    });
    document.addEventListener( 'keydown', ( event ) => { if ( event.key === 'Escape' && root.classList.contains( 'fullscreen' ) ) { actions.fullscreen(); } } );
    update();
})();
</script>
