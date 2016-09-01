function addClassElement(){

    var holder_divs = document.getElementById('holder').children;

    for (var i = 0; i<holder_divs.length;i++){
        holder_divs[i].setAttribute('class','element');
    }

};