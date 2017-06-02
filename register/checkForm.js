// JS para comprobar que los datos de registro sean válidos
$(document).ready(function(){
  $("form#registerForm input#nick").focusout(function(){
    checkIfExists($(this));
  });

  $("form#registerForm input#email").focusout(function(){
    checkEmail($(this));
  });

  $("form#registerForm input#pass").focusout(function(){
    checkPass($(this));
  });

  $("form#registerForm input#passRepeat").focusout(function(){
    checkPassRepeat($("form#registerForm input#pass"), $(this));
  });

  $("form#registerForm").submit(function(e){
    checkIfExists($("form#registerForm input#nick"));
    checkEmail($("form#registerForm input#email"));
    checkPass($("form#registerForm input#pass"));
    checkPassRepeat($("form#registerForm input#pass"),
      $("form#registerForm input#passRepeat"));
    if(grecaptcha.getResponse() == ""){
      alertInput(true, $("form#registerForm #recaptcha"), 
      "Por favor, compruebe que no es un robot.");
    } else 
      alertInput(false, $("form#registerForm #recaptcha"));
    if($("form#registerForm .alert-danger").length > 0){
      alertInput(true, $("form#registerForm #registrarse"), 
        "Por favor, revise los errores en el formulario");
      $("#alert-registrarse").appendTo($(".registerTitle").parents(".container"));
      return false;
    } else {
      alertInput(false, $("form#registerForm #registrarse"));
    }
  });
});

// Comprobar si el nombre de usuario (nick) ya está registrado
function checkIfExists(nickInput){
  if(nickInput.val().trim() != ""){
    var formData = new FormData();
    formData.append("check", "nick");
    formData.append("nick", nickInput.val());
    $.ajax({
      url: "/register/checkUserBD.php",
      type: "post",
      dataType: "json",
      data: formData,
      cache: false,
      contentType: false,
      processData: false,
      success: function(json){
        if(!json.exists){
          nickInput.parent().removeClass("has-error");
          nickInput.parent().addClass("has-success");
          alertInput(false, nickInput);
        } else {
          nickInput.parent().removeClass("has-success");
          nickInput.parent().addClass("has-error");
          alertInput(true, nickInput, "Este nick ya está registrado.");
        }
      }
    });
  } else {
    nickInput.parent().removeClass("has-success");
    nickInput.parent().addClass("has-error");
    alertInput(true, nickInput, "Por favor, introduzca un Nick.");
  }
}

// Comprobar si el email tiene un formato válido
function checkEmail(emailInput){
  regEmail = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  if(emailInput.val().match(regEmail)){
    var formData = new FormData();
    formData.append("check", "email");
    formData.append("email", emailInput.val());
    $.ajax({
      url: "/register/checkUserBD.php",
      type: "post",
      dataType: "json",
      data: formData,
      cache: false,
      contentType: false,
      processData: false,
      success: function(json){
        if(!json.exists){
          emailInput.parent().removeClass("has-error");
          emailInput.parent().addClass("has-success");
          alertInput(false, emailInput);
        } else {
          emailInput.parent().removeClass("has-success");
          emailInput.parent().addClass("has-error");
          alertInput(true, emailInput, "Este email ya está registrado.");
        }
      }
    });
  } else if(emailInput.val().trim() == ""){
    emailInput.parent().removeClass("has-success");
    emailInput.parent().addClass("has-error");
    alertInput(true, emailInput, "Por favor, introduzca un email.");
  } else {
    emailInput.parent().removeClass("has-success");
    emailInput.parent().addClass("has-error");
    alertInput(true, emailInput, "Email no válido.");
  }
}

// Comprobar contraseña: mínimo 8 caracteres, con al menos una mayúscula,
// una minúscula, un número y un caracter no alfanumérico (puntuación, arroba, etc)
function checkPass(passInput){
  regPass = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.{8,})(?![0-9a-zA-Z]*$)/;
  if(passInput.val().match(regPass)){
    passInput.parent().removeClass("has-error");
    passInput.parent().addClass("has-success");
    alertInput(false, passInput);
    return true;
  } else if (passInput.val() == ""){
    passInput.parent().removeClass("has-success");
    passInput.parent().addClass("has-error");
    alertInput(true, passInput, "Por favor, introduzca una contraseña.");
  } else {
    passInput.parent().removeClass("has-success");
    passInput.parent().addClass("has-error");
    alertInput(true, passInput, 
      "La contraseña no cumple con los requisitos. "
      + "<a data-toggle='modal' href='#passReq'>Ver requisitos</a>");
    return false;
  }
}

// Comprobar si los dos campos de contraseña coinciden
function checkPassRepeat(passInput, passRepeatInput){
  if(checkPass(passInput) && passInput.val() == passRepeatInput.val()){
    passRepeatInput.parent().removeClass("has-error");
    passRepeatInput.parent().addClass("has-success");
    alertInput(false, passRepeatInput);
  } else if(!checkPass(passInput) && passRepeatInput.val() == "") {
    passRepeatInput.parent().removeClass("has-success");
    passRepeatInput.parent().addClass("has-error");
    alertInput(true, passRepeatInput, "Por favor, introduzca de nuevo la contraseña");
  } else if(!checkPass(passInput) && passRepeatInput.val() != "") {
    passRepeatInput.parent().removeClass("has-success");
    passRepeatInput.parent().removeClass("has-error");
    alertInput(false, passRepeatInput);
  } else {
    passRepeatInput.parent().removeClass("has-success");
    passRepeatInput.parent().addClass("has-error");
    alertInput(true, passRepeatInput, "Las contraseñas no coinciden");
  }
}

// Función para imprimir u ocultar errores con alerts de bootstrap bajo el input
function alertInput(show, input, message){
  if(show && !$("#alert-"+input.attr("id"))[0]){
    alert = "<div class='alert alert-danger' id='alert-"+input.attr("id")+"' "
      +"style='display:none'>"+message+"</div>";
    $(alert).appendTo(input.parent());
    $("#alert-"+input.attr("id")).slideDown();
  } else if (show && $("#alert-"+input.attr("id"))[0]){
    $("#alert-"+input.attr("id")).html(message);
  } else {
    $("#alert-"+input.attr("id")).slideUp(400, function(){
      $("#alert-"+input.attr("id")).remove();
    });
  }
}