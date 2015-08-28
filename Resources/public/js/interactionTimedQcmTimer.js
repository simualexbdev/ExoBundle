/**
 * Created by CPA SIMUSANTE
 *
 * on 13/07/15.
 */

// url appelée par la requête AJAX lorsque la question est soumise
var path;
// L'id de l'exercice
var exoID;
// L'id de la question
var interTimedQcmID;
// Type de la question (1: multiple ; 2: unique)
var codeTypeTimedQcm;
// Tableau contenant l'id des propositions choisies par l'utilisateur
var userChoices;
// Tableau contenant l'id de tous les choix possibles à la question
var choicesID;
// Tableau contenant la ou les bonnes réponses à la question
var rightChoices;
// L'id de l'examen en cours
var paperID;

$(document).ready(function() {
    $('#submit_response').on('click', function (event) {
        /*
         * Annule l'évènement lancé par défaut par le submit du formulaire pour qu'il n'y ait pas à la fois
         * l'évènement du formulaire et l'évènement de la requête ajax qui se lance en même temps.
         */
        event.preventDefault();

        disableSubmitButton();

        getUserChoices();

        getAllChoices();

        timedQcmAjaxRequest();
    });
});

// Fonction jQuery chronométrant une question de type TimedQcm d'un exercice
function interactionTimedQcmTimer(url, interPVID, codeTypePV, test, cookie) {
        var timeArray = ($("#remainingTime").text()).split(':');
        var remainingTime;
        var timer;
        var hours = 0;
        var minutes = 0;
        var seconds = 0;
        path = url;
        exoID = $('input[name="exoID"]').val(); //exoID;
        interTimedQcmID = interPVID;
        codeTypeTimedQcm = codeTypePV;
        choicesID = new Array();
        paperID = test;

        // Déclaration du temps nécessaire à la redirection

        // Conversion de la chaine de temps en secondes
        var redirTime = (parseInt(timeArray[0]) * 3600) + (parseInt(timeArray[1]) * 60) + parseInt(timeArray[2]);

        // Je lance un interval qui a lieu toutes les secondes
        timer = setInterval(function() {
            // Lorsque le formulaire est soumis...
            //$('#responseForm').on('submit', function(event) {
            $('#submit_response').on('click', function (event) {

                    remainingTime = new Date (1, 1, 1, 0, 0, 0);

                    // Ici j'affiche mon texte dans l'élement avec l'Id remainingTime, soit le temps restant à afficher
                    $("#remainingTime").text("0" + remainingTime.getHours() + ':' + "0" + remainingTime.getMinutes() + ':' + "0" + remainingTime.getSeconds());
                    // Stop le timer (la boucle)
                    clearInterval(timer);
            });

            // Ici, je crée la nouvelle date à afficher après un tick de l'intervalle
            remainingTime = new Date (1, 1, 1, 0, 0, redirTime);

            // Formatage de l'affichage du temps restant
            if (remainingTime.getHours() < 10) {
                hours = "0" + remainingTime.getHours();
            }
            else {
                hours = remainingTime.getHours();
            }

            if (remainingTime.getMinutes() < 10) {
                minutes = "0" + remainingTime.getMinutes();
            }
            else {
                minutes = remainingTime.getMinutes();
            }

            if (remainingTime.getSeconds() < 10) {
                seconds = "0" + remainingTime.getSeconds();
            }
            else {
                seconds = remainingTime.getSeconds();
            }

            // Ici j'affiche mon texte dans l'élement avec l'Id remainingTime, soit le temps restant à afficher
            $("#remainingTime").text(hours + ':' + minutes + ':' + seconds);

            // Ici je redirige la personne
            if (redirTime == 0) {
                // Stop le timer (la boucle)
                clearInterval(timer);
                ajaxSubmitResponse();
            }

            redirTime--;
        }, 1000);
}

// Désactive la possibilité de soumettre une nouvelle fois le formulaire en rendant inactif le bouton submit de ce dernier
function disableSubmitButton() {
    $('#submit_response').attr('disabled', true);
}

// Récupère toutes les propositions que l'utisateur a coché
function getUserChoices() {
    // Si la question est de type réponse multiple, alors il est possible que l'utilisateur ait choisi de cocher plusieurs propositions
    if (codeTypeTimedQcm == 1) {
        userChoices = new Array();
    }

    // On parcours toutes les propositions qui sont cochées et on les ajoute à la liste des choix de l'utilisateur
    $("input:checked").each(function() {
        // La question est de type réponse multiple
        if (codeTypeTimedQcm == 1) {
            userChoices.push($(this).val());

        }
        // La question est de type réponse unique
        else {
            userChoices = $(this).val();
        }
    });
}

// Récupère toutes les propositions liées à la question TimedQcm
function getAllChoices() {
    if (codeTypeTimedQcm == 1) {
        $("input:checkbox").each(function () {
            choicesID.push($(this).val());
            $(this).attr("disabled", true);
        });
    }
    else {
        $("input:radio").each(function () {
            choicesID.push($(this).val());
            $(this).attr("disabled", true);
        });
    }
}

/*
 * Requête AJAX qui envoie les choix de l'utilisateur, l'id de la question et l'id de l'exercice à
 * la méthode responseTimedQcmAction du Controller : InteractionTimedQcmController
 */
function timedQcmAjaxRequest() {

    // Définition de la requête AJAX
    $.ajax({
        // Requête de type POST
        type: "POST",
        // Route à appeler ('{{ path('ujm_interactiontimedQcm_response') }}')
        url: path,
        // Données transmises par la requête
        data: {
            paperID: paperID,
            exoID:  exoID,
            interactionTimedQcmToValidated: interTimedQcmID,
            choice: JSON.stringify(userChoices)
        },
        dataType: "json", // Spécifie le type de retour attendu, le type de réponse retourné à la requête AJAX par le Controller
        // Que fait-on en cas de succès ?
        // Le paramète data correspond à la liste de toutes les bonnes réponses qu'il fallait choisir pour avoir à la question.
        success: function(data) {
            rightChoices = new Array();

            $.each(data, function(index, choice) {
                rightChoices.push(choice);
            });

            nbGoodAnswers = countNumberOfGoodAnswer();

            displayUserResultLegend();

            checkAndDisplayRightResponses(nbGoodAnswers);

            displayHtmlCourseComplement();
        },
        // Que fait-on en cas d'erreur ?
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            console.log('REQUEST : ' + XMLHttpRequest.getAllResponseHeaders());
            console.log('Response-Text : ' + XMLHttpRequest.responseText);
            console.log('Status : ' + textStatus);
            console.log('Error : ' + errorThrown);
        }
    });
}

// Soumet le questionnaire lorsque le temps imparti pour répondre à la question est écoulé
function ajaxSubmitResponse() {
    $('#submit_response').click();
}

/*
 * Compte le nombre de bonnes réponses qu'a fourni l'utilisateur et affiche, à coté de la proposition,
 * un symbole vert de type "check" pour chacune d'elles,ou un symbole rouge de type "croix" pour chaque réponse erronée fournie par l'utilisateur.
 */
function countNumberOfGoodAnswer() {
    // Le nombre de bonnes réponses de l'utilisateur, qui sera ensuite comparé avec le nombre total de bonnes réponses à la question.
    var nbGoodAnswers = 0;
    // Flag permettant de savoir si le choix de l'utilisateur parcouru (dans les boucles en dessous) est une bonne réponse ou non.
    var isGoodAnswer = false;

    // Question à choix multiples
    if (codeTypeTimedQcm == 1) {
        /*
         * On effectue la comparaison entre les choix de l'utilisateur et les bonnes réponses pour afficher, à coté de la proposition en question,
         * soit un symbole "check" vert si la réponse fournie est bonne, soit une croix rouge dans le cas contraire.
         */
        for (var i = 0; i < userChoices.length; i++) {
            for (var j = 0; j < rightChoices.length; j++) {
                if (userChoices[i] == rightChoices[j]) {
                    $('#rightChoice_' + userChoices[i]).css({"color": "green"});
                    $('#rightChoice_' + userChoices[i]).append("<i class='fa fa-check'></i>");
                    nbGoodAnswers++;
                    isGoodAnswer = true;
                    break;
                }
            }
            if (isGoodAnswer) {
                isGoodAnswer = false;
            }
            else {
                $('#rightChoice_' + userChoices[i]).css({"color": "red"});
                $('#rightChoice_' + userChoices[i]).append("<i class='fa fa-times'></i>");
            }
        }
    }
    // Question à choix unique
    else {
        for (var i = 0; i < rightChoices.length; i++) {
            if (userChoices == rightChoices[i]) {
                $('#rightChoice_' + userChoices).css({"color": "green"});
                $('#rightChoice_' + userChoices).append("<i class='fa fa-check'></i>");
                nbGoodAnswers++;
                isGoodAnswer = true;
                break;
            }
        }
        if (!isGoodAnswer) {
            $('#rightChoice_' + userChoices).css({"color": "red"});
            $('#rightChoice_' + userChoices).append("<i class='fa fa-times'></i>");
        }
    }

    return nbGoodAnswers;
}

// Affiche le tableau servant de légende au résultat des choix faits par l'utilisateur.
function displayUserResultLegend() {
    $('#userResultLegend').css({"display": "table-cell"});
}

/*
 * Affiche les choix qu'il fallait choisir pour avoir bon à la question en les entourant en vert,
 * puis vérifie les choix fait par l'utilisateur et affiche "Bonne réponse" si tous les choix donnés par l'utilisateur sont correct.
 * Sinon, affiche "Faux" puis la ou les réponses attendues
 */
function checkAndDisplayRightResponses(nbGoodAnswer) {
    // Représente toutes les bonnes réponses à la question
    var rightChoicesLetter = new Array();
    // Tableau représentant l'alphabet
    var letter = ['A', 'B', 'C' , 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];

    for (var i = 0; i < choicesID.length; i++) {
        for (var j = 0; j < rightChoices.length; j++) {
            if (choicesID[i] == rightChoices[j]) {
                // Entoure en vert toutes les propositions qu'il fallait choisir pour avoir bon à la question
                $('#labelChoice_' + choicesID[i]).css({"border-style": "solid", "border-width": "2px", "-webkit-border-radius": "15px", "-moz-border-radius": "15px",
                    "border-radius": "15px", "border-color": "green", "padding": "4px"});
                rightChoicesLetter.push(letter[i]);
                break;
            }
        }
    }

    // Question à réponse multiple
    if (codeTypeTimedQcm == 1) {
        if ((userChoices.length == rightChoicesLetter.length) && (nbGoodAnswer == rightChoicesLetter.length)) {
            $('.panel-course-complement').before("<br/><font color=\'green\' size='3'><b>Bonne réponse</b></font>");
        }
        else {
            // Affiche BON ou FAUX à l'utilisateur + les propositions qu'il fallait cocher dans le dernier cas
            var labelResult;

            if (rightChoicesLetter.length > 1) {
                labelResult = "<br/><b><font color=\'red\' size='3'>Faux !</font></b><font size='3'> Les bonnes réponses étaient les réponses ";

                // Formatage du texte affiché concernant les réponses attendues dans le cas de choix erroné
                for (var i = 0; i < rightChoicesLetter.length; i++) {
                    labelResult = labelResult + rightChoicesLetter[i];
                    if ((i + 1) <= rightChoicesLetter.length - 2) {
                        labelResult = "<b>" + labelResult + "</b>, ";
                    }
                    else if ((i + 1) <= rightChoicesLetter.length - 1) {
                        labelResult = "<b>" + labelResult + "</b> et ";
                    }
                    else {
                        labelResult = "<b>" + labelResult + "</b>.</font>";
                    }
                }
            }
            else {
                labelResult = "<br/><b><font color=\'red\' size='3'>Faux !</font></b><font size='3'> La bonne réponse était la réponse <b>" + rightChoicesLetter[0] + "</b>.</font></b>";
            }
            $('.panel-course-complement').before("<b>" + labelResult + "</b></font>");
        }
    }
    // Question à réponse unique
    else {
        // Bonne réponse
        if (nbGoodAnswer > 0) {
            $('.panel-course-complement').before("<br/><font color=\'green\' size='3'><b>Bonne réponse !</b></font>");
        }
        // Mauvaise réponse
        else {
            $('.panel-course-complement').before("<br/><b><font color=\'red\' size='3'>Faux !</font></b><font size='3'> La bonne réponse était la réponse <b>" + rightChoicesLetter[0] + "</b>.</font>");
        }
    }
}

// Affiche le complément de cours lié à la question, juste en dessous d'elle
function displayHtmlCourseComplement() {
    $('.panel-course-complement').css({"display": "block"});
}