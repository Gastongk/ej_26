$(document).ready(function() {

    var username = localStorage.getItem('username');
    var email = localStorage.getItem('useremail');


/*     console.log('username:', username);
    console.log('email:', email);
    console.log('userId:', userId); */
    if (username) {
        document.getElementById('nombreUsuario').innerHTML = username;
        document.getElementById('emailUsuario').innerHTML = email;
    }
  
    $("#registroForm").submit(function(event) {
        event.preventDefault(); 

        var username = $("#username").val();
        var password = $("#password").val();
        var email = $("#email").val();

        $.ajax({
            type: "POST",
            url: '../backend/api.php',
            dataType: 'json',
            data: {
                registro: 1,
                username: username,
                password: password,
                email: email
            },
            success: function(response) {
                if (response) {
                    if (response.success) {
      
                        $("#mensajeRegistro").html(response.message);
                    } 
                } else {
           
                    $("#mensajeRegistro").html("No se ejecutó el bloque 'success'");
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log("Error: " + textStatus + " - " + errorThrown);
            }
        });
    });

    $("#loginForm").submit(function(event) {
        event.preventDefault(); 
    
        var username = $("#username_L").val();
        var password = $("#password_L").val();
    
        $.ajax({
            type: "POST",
            url: '../backend/api.php',
            dataType: 'json',
            data: {
                login: 1,
                username: username,
                password: password
            },
            
            success: function(response) {
               /*  console.log(response) */
                if (response.success) {
                    // respuesta exitosa al iniciar sesión
                    localStorage.setItem('username', response.data.username);
                    localStorage.setItem('useremail', response.data.email);
                 //   window.localStorage.token = response.data.username;
                    console.log(response.data.username)
                    $("#mensajeLogin").html(response.message);
    
            //        $("#nombreUsuario").text(response.data.nombre);

                   setTimeout(function() {
                            window.location.href = "inicio.html";
                }, 2000); // tempo de espera
                } else {
                    $("#mensajeLogin").html("Error: " + response.message);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log("Error: " + textStatus + " - " + errorThrown);
            }
        });
    });


    $("#cerrarSesion").click(function() {
        localStorage.removeItem('username');
        $.ajax({
           type: "POST",
           url: "../backend/cerrar_sesion.php", 
           success: function(response) {
                window.location.href = "index.html";
           },
           error: function(jqXHR, textStatus, errorThrown) {
               console.log("Error al cerrar sesión: " + textStatus + " - " + errorThrown);
           }
       });
   });
    
 });
    
