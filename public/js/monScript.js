var nbre = document.getElementById('nombre');
var val = document.getElementById('prix');
var ttl = document.getElementById('total');

$(document).ready(function(){
    $('#nombre').change(function(){
        console.log('kjsd')
        let value = document.getElementById('prix').value;
        ttl.value = document.getElementById('nombre').value * value;
    });
});