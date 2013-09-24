
window.addEventListener("load", start, false);

function start() {
    var d1  = document.getElementById( 'hitme' );
    d1.addEventListener( "click", colorDiv, true );
}

function colorDiv( evt ) {
    var d1  = document.getElementById( 'bluebox' );
    d1.style.backgroundColor='#000';
}