// :::::::::::::::::::::::::::::::::::::::::: Declaration variables :::::::::::::::::::::::::::::::::::::::::::::::::::
var allow = false;
var answerImg = document.getElementById('AnswerImage');
var AnswerZones; // Tab contains all informations of Coords
var el = document.getElementById('movable'); // To get the shape and the color of the answer zone
var imgx; // Coord x of the answer zone
var imgy; // Coord y of the answer zone
var indice = 0; // Number of answer zone
var j; // For for instruction
var mousex; // Position x of the mouse
var mousey; // position y of the mouse
var pressMAJ; // If key MAJ pressed or not
var pressCTRL; // If key CTRL pressed or not
var pressALT; // If key ALT pressed or not
var pressS; // If key s pressed or not
var result; // src of the answer image
var sens; // Move of the mouse
var scalex = 0; // Width of the image after resize
var scaley = 0; // Height of the image after resize
var t; // Contain all the information of one answer zone (ccords, shape, color ...)
var ts; // Src of the answer zone
var tx; // Position x of the answer zone
var ty; // Position y of the answer zone
var tx1; // Limit up of the answer zone 
var tx2; // Limit down of the answer zone
var ty1; // Limit left of the answer zone
var ty2; // Limit right of the answer zone
var value = 0; // Size of the resizing
var x = 0; // Mouse x after move
var xPrecedent = 0; // Mouse x before move
var y = 0; // Mouse y after move
var yPrecedent = 0; // Mouse y before move




// Display alert into navigator language
if (navigator.browserLanguage) {
    var language = navigator.browserLanguage; // IE
} else {
    var language = navigator.language; // FIrefox
}

// :::::::::::::::::::::::::::::::::::::::::: Functions :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

// Get the url's picture matching to the label in the list
function sendData(select,path,prefx) {
    //"use strict";

    // Send the label of the picture to get the adress in order to display it
    $.ajax({
        type: 'POST',
        url: path,
        data: {
            value : select, prefix : prefx
        },
        cache: false,
        success: function (data) {
            answerImg.src = data;
        }
    });
}

// Display the selected picture
function LoadPic(path,prefx) {
    //"use strict";

    var list = document.InterGraphForm.ujm_exobundle_interactiongraphictype_document; // List of all the user's pictures
    var select = list.options[list.selectedIndex].innerHTML; // Label of the selected picture

    sendData(select,path,prefx);

    // New picture load, initialization var :
    value = 0;
    AnswerZones = [];
    document.getElementById('coordsZone').value = 0;
    
    for (j = 0 ; j < indice ; j++) {
        if(document.getElementById('img' + j)){
            document.getElementById('img' + j).parentNode.removeChild(document.getElementById('img' + j));
        }
    }
    indice = 0;
}

// Submit form without an empty field
function Verifier(noTitle, noQuestion, noImg, noAnswerZone) {
    //"use strict";

    var imgOk = false; // Image is upload
    var questionOk = false; // Question is asked
    var titleOk = false; // Question has a title
    var zoneOk = false; // Answer zones are defined

    // No title
    if (document.InterGraphForm.ujm_exobundle_interactiongraphictype_interaction_question_title.value === '') {
        alert(noTitle);
        return false;
    } else {
        titleOk = true;
    }

    // No question asked
    if (document.InterGraphForm.ujm_exobundle_interactiongraphictype_interaction_invite.value === '' && titleOk === true) {
        alert(noQuestion);
        return false;
    } else {
        questionOk = true;
    }

    // No picture load
    if (document.getElementById('AnswerImage').src.indexOf('users_document') == -1 && titleOk === true && questionOk === true) {
        alert(noImg);
        return false;
    } else {
        imgOk = true;
    }

    // No answer zone
    if (document.getElementById('coordsZone').value == 0 && imgOk === true && titleOk === true && questionOk === true) {
        alert(noAnswerZone);
        return false;
    } else {
        zoneOk = true;
    }

    // Submit if required fields not empty
    if (imgOk === true && zoneOk === true && titleOk === true && questionOk === true) {
        document.getElementById('imgwidth').value = answerImg.width; // Pass width of the image to the controller
        document.getElementById('imgheight').value = answerImg.height; // Pass height of the image to the controller
        
        document.getElementById('InterGraphForm').submit();
    }
}

// Change the shape and the color of the answer zone
function changezone(prefix) {
    //"use strict";

    if (document.getElementById('shape').value === 'circle') {
        switch (document.getElementById('color').value) {
        case 'white' :
            el.src = prefix+'circlew.png';
            break;

        case 'red' :
            el.src = prefix+'circler.png';
            break;

        case 'blue' :
            el.src = prefix+'circleb.png';
            break;

        case 'purple' :
            el.src = prefix+'circlep.png';
            break;

        case 'green' :
            el.src = prefix+'circleg.png';
            break;

        case 'orange' :
            el.src = prefix+'circleo.png';
            break;

        case 'yellow' :
            el.src = prefix+'circley.png';
            break;

        default :
            el.src = prefix+'circlew.png';
            break;
        }

    } else if (document.getElementById('shape').value === 'rect') {
        switch (document.getElementById('color').value) {
        case 'white' :
            el.src = prefix+'rectanglew.jpg';
            break;

        case 'red' :
            el.src = prefix+'rectangler.jpg';
            break;

        case 'blue' :
            el.src = prefix+'rectangleb.jpg';
            break;

        case 'purple' :
            el.src = prefix+'rectanglep.jpg';
            break;

        case 'green' :
            el.src = prefix+'rectangleg.jpg';
            break;

        case 'orange' :
            el.src = prefix+'rectangleo.jpg';
            break;

        case 'yellow' :
            el.src = prefix+'rectangley.jpg';
            break;

        default :
            el.src = prefix+'rectanglew.jpg';
        }
    }
}

function  ResizeImg(sens) {
    //"use strict";

    if (sens === 'gauche') {
        value -= 5;
    } else if (sens === 'droite') {
        value += 5;
    }

    scalex = answerImg.width + value; // New picture width

    var ratio = answerImg.height / answerImg.width;
    scaley = scalex * ratio; // New picture height proportional to width

    if (scalex > 27 && scaley > 27) { // Not resize too small or negativ
        answerImg.width = scalex;
        answerImg.height = scaley;
    }
}

function  ResizePointer(sens) {
    //"use strict";
    
    if (sens === 'gauche') {
        cible.width -= 5;
    } else if (sens === 'droite') {
        cible.width += 5;
    }

    if(cible.width < 10){
        cible.width = 10;
    }
    cible.height += cible.width * (cible.height / cible.height);

    for (var i = 0, c = AnswerZones.length; i < c; i++) {
        var x = cible.style.left.substr(0, cible.style.left.indexOf('p'))- answerImg.offsetLeft + 10;
        var y = cible.style.top.substr(0, cible.style.top.indexOf('p')) - answerImg.offsetTop + 10;
        var coord = x +'_' + y;

        if (coord == AnswerZones[i].substring(AnswerZones[i].indexOf(';')+1, AnswerZones[i].indexOf('-'))){
            AnswerZones[i] = AnswerZones[i].replace(AnswerZones[i].substr(AnswerZones[i].indexOf('~')+1),cible.width);
            break;
        }
    }
    document.getElementById('coordsZone').value = AnswerZones;
}

function MouseSens(event) {
    xPrecedent = x;
    yPrecedent = y;

    if (event.x !== undefined && event.y !== undefined) { // IE
        x = event.layerX;
        y = event.layerY;
    } else { // Firefox
        x = event.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
        y = event.clientY + document.body.scrollTop + document.documentElement.scrollTop;
    }

    x -= answerImg.offsetLeft; // MouseX position
    y -= answerImg.offsetTop;  // MouseY position

    if (x < xPrecedent) { // Gauche
        sens = 'gauche';
    } else if (x > xPrecedent) { // Droite
        sens = 'droite';
    }

    return sens;
}

// :::::::::::::::::::::::::::::::::::::::::: EventListener :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

document.addEventListener('keydown', function (e) {
    //"use strict";

    if (e.keyCode === 16) { // Touch MAJ down
        pressMAJ = true;
        document.body.style.cursor = 'nw-resize';
    }

    if (e.keyCode === 17) { // Touch CTRL down
        pressCTRL = true;
        document.body.style.cursor = 'move';
    }

    if (e.keyCode === 83) { // Touch s down
        pressS = true;
        //document.body.style.cursor='suppr';
    }
    
    if (e.keyCode === 18) { // Touch ALT down
        pressALT = true;
    }
}, false);

document.addEventListener('keyup', function (e) {
    //"use strict";

    if (e.keyCode === 16) { // Touch MAJ up
        pressMAJ = false;
        document.body.style.cursor = 'default';
    }

    if (e.keyCode === 17) { // Touch CTRL up
        pressCTRL = false;
        document.body.style.cursor = 'default';
    }

    if (e.keyCode === 83) { // Touch s up
        pressS = false;
        //document.body.style.cursor='default';
    }
    
    if (e.KeyCode === 18) { // Touch ALT up
        pressALT = false;
    }
}, false);

document.addEventListener('mousemove', function (event) { // To resize the selected picture
    //"use strict";

    if (pressMAJ === true) {
        ResizeImg(MouseSens(event));
        pressMAJ = false;
    }
    
    if (pressALT === true && allow == true) {
        ResizePointer(MouseSens(event));
        pressALT = false;
    }
});

document.addEventListener('click', function (e) { // To add/delete answer zones
    //"use strict";
    
    if (pressCTRL === true) {

        // Position de la souris dans la fenetre :
        if (e.x !== undefined && e.y !== undefined) { // IE
            mousex = e.layerX;
            mousey = e.layerY;
        } else { // Firefox
            mousex = e.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
            mousey = e.clientY + document.body.scrollTop + document.documentElement.scrollTop;
        }

        // If out of the image
        if ((mousex) > (answerImg.offsetLeft + answerImg.width) || (mousex) < (answerImg.offsetLeft) || 
            (mousey) > (answerImg.offsetTop + answerImg.height) || (mousey) < (answerImg.offsetTop)) {
            
            if (language.indexOf('fr') > -1) {
                alert('Vous devez mettre la zone de reponse DANS l\'image ...');
            } else {
                alert('You must put the answer zone INSIDE the picture ...');
            }
            document.body.style.cursor = 'default';
        } else {
 
            var img = new Image();
    
            img.style.position = 'absolute';
            img.style.left = String(mousex - 10) + 'px';
            img.style.top = String(mousey - 10) + 'px';
            
            img.id = 'img'+indice;
            indice++;
            
            img.src = el.src;
            
            document.body.appendChild(img);

            // Add the new answer zone informations to the tab in order to send it to the controller
            imgx = parseInt(img.style.left.substr(0, img.style.left.indexOf('p')));
                imgx -= answerImg.offsetLeft - 10;
                
            imgy = parseInt(img.style.top.substr(0, img.style.top.indexOf('p')));
                imgy -= answerImg.offsetTop - 10;
                
            var val = img.src + ';' + imgx + '_' + imgy + '-' + document.getElementById('points').value + '~' + img.width;

            AnswerZones.push(val);
        }
        pressCTRL = false;

        // Send the answer zones informations to the controller
        document.getElementById('coordsZone').value = AnswerZones;
    }

    if (pressS === true) {

        document.getElementById('coordsZone').value = AnswerZones;

        for (j = 0 ; j < indice ; j++) {
            if (e.target.id == 'img' + j) {
                var image = document.getElementById(e.target.id);
                image.parentNode.removeChild(image);
                    AnswerZones.splice(j, 1);

                    if (j === 0 && AnswerZones.length < 1) {
                        document.getElementById('coordsZone').value = 0;
                    }
                    break;
            }
        }
        pressS = false;
    }
    
    for (j = 0 ; j < indice ; j++) {
        if (e.target.id == 'img' + j) {
            cible = e.target;
            allow = true;
            document.onmousedown = function () {
                return false;
            }
        }
    }

    document.onmousedown = function () {
        return true;
    } 
}, false);