const userId = document.getElementById( "user" ).dataset.id;
const username = document.getElementById( "user" ).dataset.username;
const socket = new WebSocket( "ws://localhost:8080/chat?userId=" + userId );

var clientInformation = {
    date: new Date().getTime().toString()
};

socket.addEventListener( "open", function () {
    console.log( "CONNECTED" );
} );

socket.addEventListener( "message", function ( e ) {
    console.log( e.data );
    try {
        const message = JSON.parse( e.data );
        addMessage( message.name, message.message );
    } catch ( e ) {
        console.log( e );
    }
} );

document.getElementById( "sendBtn" ).addEventListener( "click", function () {
    const message = {
        id: userId,
        name: username,
        message: document.getElementById( "message" ).value
    };
    socket.send( JSON.stringify( message ) );
    addMessage( message.name, message.message );
    document.getElementById( "message" ).value = "";
} );

function addMessage( name, message ) {
    if ( message.trim() === '' ) {
        return;
    }
    const messageHTML = "<div class='message'><strong>" + name + ":</strong> " + message + "</div>";
    document.getElementById( "chat" ).innerHTML += messageHTML
}
