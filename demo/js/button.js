window.addEventListener("load", start, false);

var bgcolor='#f00';

function start()
{
    var d1 = document.getElementById( 'hitme' );
    d1.addEventListener( "click", colorDiv, true );
}

function colorDiv( evt )
{
    var d1 = document.getElementById( 'bluebox' );
    if(bgcolor=='#f00')
    {
        bgcolor='#000';
    } else {
        bgcolor='#f00';
    }
    d1.style.backgroundColor=bgcolor;
}