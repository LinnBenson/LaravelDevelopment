@props([
    'rid' => 'upload-'.uniqid(),
    'name' => '',
    'value' => '',
    'placeholder' => '',
    'min' => null,
    'max' => null,
    'exts' => '',
])

@php
    $initialLinks = is_array( $value ) ? $value : preg_split( '/[|\r\n]+/', (string) $value, -1, PREG_SPLIT_NO_EMPTY );
    $minFiles = max( 0, (int) ( $min ?? 0 ) );
    $maxFiles = $max === null || $max === '' ? 0 : max( 0, (int) $max );
@endphp

<!-- Upload tool -->
<div class="toview-upload" data-upload="{{$rid}}" data-state="loading">
    <input class="toview-upload-value" type="text" rid="{{$rid}}" name="{{$name}}" value="{{implode( '|', $initialLinks )}}" tabindex="-1" aria-hidden="true" input />
    <input class="toview-upload-file" type="file" tabindex="-1" aria-hidden="true" />
    <div class="toview-upload-drop" role="button" tabindex="0" aria-label="Select or drop files">
        <div class="toview-upload-drop-icon"><i class="bi bi-cloud-arrow-up"></i></div>
        <div class="toview-upload-drop-text">
            <strong>{{$placeholder !== '' ? $placeholder : 'Select files or drag them here'}}</strong>
            <span data-upload="hint">{{__( 'base.loading' )}}...</span>
        </div>
        <button type="button" data-upload="browse">{{__("base.upload")}}</button>
    </div>
    <div class="toview-upload-progress" aria-hidden="true">
        <span data-upload="progress"></span>
    </div>
    <div class="toview-upload-list" data-upload="list"></div>
    <div class="toview-upload-footer">
        <span data-upload="status">{{__( 'base.loading' )}}...</span>
        <button type="button" data-upload="clear">{{__("base.clear")}}</button>
    </div>
</div>

<script>
/* Upload tool interactions */
(() => {
    const uploadId = @json((string) $rid);
    const root = Array.from( document.querySelectorAll( '[data-upload]' ) ).find( ( element ) => element.dataset.upload === uploadId );
    if ( !root || root.dataset.ready === 'true' ) { return; }
    root.dataset.ready = 'true';
    const input = root.querySelector( 'input.toview-upload-file' );
    const valueInput = root.querySelector( '[input]' );
    const drop = root.querySelector( '.toview-upload-drop' );
    const browse = root.querySelector( '[data-upload="browse"]' );
    const clear = root.querySelector( '[data-upload="clear"]' );
    const list = root.querySelector( '[data-upload="list"]' );
    const hint = root.querySelector( '[data-upload="hint"]' );
    const status = root.querySelector( '[data-upload="status"]' );
    const progress = root.querySelector( '[data-upload="progress"]' );
    const uploadUrl = @json(route( 'plugins.to-view.upload' ));
    const configUrl = @json(route( 'plugins.to-view.upload.config' ));
    const initialLinks = @json(array_values( $initialLinks ));
    const requestedExtensions = @json((string) $exts);
    const minFiles = @json($minFiles);
    const maxFiles = @json($maxFiles);
    let config = null;
    let links = initialLinks.filter( ( link ) => typeof link === 'string' && link !== '' );
    let uploading = false;

    const fileName = ( link ) => {
        try { return decodeURIComponent( new URL( link, window.location.origin ).pathname.split( '/' ).pop() || 'File' ); }
        catch ( error ) { return 'File'; }
    };
    const formatBytes = ( bytes ) => {
        const units = ['B', 'KB', 'MB', 'GB']; let value = Number( bytes ) || 0; let unit = 0;
        while ( value >= 1024 && unit < units.length - 1 ) { value /= 1024; unit++; }
        return `${value >= 10 || unit === 0 ? Math.round( value ) : value.toFixed( 1 )} ${units[unit]}`;
    };
    const iconFor = ( name ) => {
        const extension = name.split( '.' ).pop()?.toLowerCase() || '';
        if ( ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg', 'ico'].includes( extension ) ) { return 'bi-file-earmark-image'; }
        if ( ['mp4', 'avi', 'mov', 'wmv', 'flv'].includes( extension ) ) { return 'bi-file-earmark-play'; }
        if ( ['mp3', 'wav', 'flac', 'aac'].includes( extension ) ) { return 'bi-file-earmark-music'; }
        if ( ['zip', 'rar', '7z', 'tar', 'gz', 'bz2', 'xz'].includes( extension ) ) { return 'bi-file-earmark-zip'; }
        if ( extension === 'pdf' ) { return 'bi-file-earmark-pdf'; }
        return 'bi-file-earmark-text';
    };
    const sync = () => {
        valueInput.value = links.join( '|' );
        let validationMessage = '';
        if ( links.length < minFiles ) { validationMessage = `At least ${minFiles} file${minFiles === 1 ? '' : 's'} must be uploaded.`; }
        if ( maxFiles > 0 && links.length > maxFiles ) { validationMessage = `A maximum of ${maxFiles} file${maxFiles === 1 ? '' : 's'} is allowed.`; }
        valueInput.setCustomValidity( validationMessage );
        valueInput.dispatchEvent( new Event( 'change', { bubbles: true } ) );
        status.textContent = links.length === 1 ? '1 file uploaded' : `${links.length} files uploaded`;
        clear.hidden = links.length === 0 || uploading;
        drop.hidden = maxFiles > 0 && links.length >= maxFiles;
    };
    const render = () => {
        list.replaceChildren();
        links.forEach( ( link, index ) => {
            const name = fileName( link ); const item = document.createElement( 'div' ); item.className = 'toview-upload-item';
            const previewBox = document.createElement( 'div' ); previewBox.className = 'toview-upload-preview';
            if ( /\.(jpe?g|png|gif|bmp|webp)$/i.test( name ) ) {
                const image = document.createElement( 'img' ); image.src = link; image.alt = ''; image.loading = 'lazy';
                image.addEventListener( 'error', () => { previewBox.innerHTML = `<i class="bi ${iconFor( name )}"></i>`; } ); previewBox.append( image );
            }else { previewBox.innerHTML = `<i class="bi ${iconFor( name )}"></i>`; }
            const info = document.createElement( 'div' ); info.className = 'toview-upload-info';
            const anchor = document.createElement( 'a' ); anchor.href = link; anchor.target = '_blank'; anchor.rel = 'noopener noreferrer'; anchor.textContent = name;
            const detail = document.createElement( 'span' ); detail.textContent = 'Uploaded'; info.append( anchor, detail );
            const remove = document.createElement( 'button' ); remove.type = 'button'; remove.title = 'Remove'; remove.setAttribute( 'aria-label', 'Remove file' ); remove.innerHTML = '<i class="bi bi-x-lg"></i>';
            remove.addEventListener( 'click', () => { links.splice( index, 1 ); render(); sync(); } );
            item.append( previewBox, info, remove ); list.append( item );
        });
    };
    const allowedExtensions = () => {
        const requested = requestedExtensions.split( /[|,\s]+/ ).map( ( extension ) => extension.trim().replace( /^\./, '' ).toLowerCase() ).filter( Boolean );
        return [...new Set( requested )];
    };
    const showError = ( message ) => {
        if ( typeof Core !== 'undefined' && typeof Core.toast === 'function' ) { Core.toast( 2, 'Upload error', message ); }
        status.textContent = message;
    };
    const upload = ( selectedFiles ) => {
        if ( uploading || !config ) { return; }
        let files = Array.from( selectedFiles || [] ); if ( files.length === 0 ) { return; }
        const remaining = maxFiles > 0 ? Math.max( 0, maxFiles - links.length ) : files.length;
        if ( remaining === 0 ) { showError( `A maximum of ${maxFiles} files is allowed.` ); return; }
        if ( files.length > remaining ) { showError( `Only ${remaining} more file${remaining === 1 ? '' : 's'} can be uploaded.` ); return; }
        const extensions = allowedExtensions();
        for ( const file of files ) {
            const extension = file.name.includes( '.' ) ? file.name.split( '.' ).pop().toLowerCase() : '';
            if ( extensions.length > 0 && !extensions.includes( extension ) ) { showError( `The .${extension || '?'} file extension is not allowed.` ); return; }
            if ( file.size <= 0 || file.size > Number( config.size ) ) { showError( `${file.name} exceeds the ${formatBytes( config.size )} limit.` ); return; }
        }
        const data = new FormData(); files.forEach( ( file ) => data.append( 'file[]', file, file.name ) );
        uploading = true; root.dataset.state = 'uploading'; progress.style.width = '0%'; status.textContent = `Uploading ${files.length} file${files.length === 1 ? '' : 's'}...`; clear.hidden = true;
        Core.web( uploadUrl ).method( 'POST' ).data( data )
            .upload( ( percent ) => { progress.style.width = `${percent}%`; status.textContent = `Uploading... ${percent}%`; } )
            .success( ( uploadedLinks ) => {
                if ( !Array.isArray( uploadedLinks ) ) { showError( 'The upload response is invalid.' ); return; }
                links = [...links, ...uploadedLinks.filter( ( link ) => typeof link === 'string' && link !== '' )]; render(); sync();
            } )
            .error( () => { showError( 'The files could not be uploaded.' ); } )
            .request().always( () => { uploading = false; root.dataset.state = 'ready'; input.value = ''; clear.hidden = links.length === 0; } );
    };
    const openPicker = () => { if ( !uploading && config ) { input.click(); } };
    browse.addEventListener( 'click', ( event ) => { event.stopPropagation(); openPicker(); } );
    drop.addEventListener( 'click', ( event ) => { if ( !event.target.closest( 'button' ) ) { openPicker(); } } );
    drop.addEventListener( 'keydown', ( event ) => { if ( ['Enter', ' '].includes( event.key ) ) { event.preventDefault(); openPicker(); } } );
    input.addEventListener( 'change', () => upload( input.files ) );
    root.addEventListener( 'dragenter', ( event ) => { event.preventDefault(); if ( config && !uploading ) { root.classList.add( 'dragging' ); } } );
    root.addEventListener( 'dragover', ( event ) => { event.preventDefault(); } );
    root.addEventListener( 'dragleave', ( event ) => { if ( !root.contains( event.relatedTarget ) ) { root.classList.remove( 'dragging' ); } } );
    root.addEventListener( 'drop', ( event ) => { event.preventDefault(); root.classList.remove( 'dragging' ); upload( event.dataTransfer.files ); } );
    clear.addEventListener( 'click', () => { if ( uploading ) { return; } links = []; render(); sync(); } );
    valueInput.addEventListener( 'invalid', ( event ) => { event.preventDefault(); showError( valueInput.validationMessage ); } );
    root.closest( 'form' )?.addEventListener( 'submit', ( event ) => {
        if ( !uploading && valueInput.checkValidity() ) { return; }
        event.preventDefault(); event.stopImmediatePropagation();
        showError( uploading ? 'Please wait for the upload to finish.' : valueInput.validationMessage );
    }, true );
    render(); sync();
    Core.web( configUrl ).success( ( uploadConfig ) => {
        config = uploadConfig; const extensions = allowedExtensions();
        input.accept = extensions.map( ( extension ) => `.${extension}` ).join( ',' ); input.multiple = maxFiles !== 1;
        const countText = maxFiles > 0 ? `Up to ${maxFiles} file${maxFiles === 1 ? '' : 's'}` : 'Multiple files allowed';
        const typeText = extensions.length > 0 ? extensions.join( ', ' ) : 'Any file type';
        hint.textContent = `${countText} · ${formatBytes( config.size )} per file · ${typeText}`;
        root.dataset.state = 'ready'; sync();
    } ).error( () => { root.dataset.state = 'error'; showError( 'Upload configuration could not be loaded.' ); } ).request();
})();
</script>
