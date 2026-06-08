(function($) {

    "use strict";

    var $userForm = $('#userForm');
    if ($userForm.length) {
        // En edición el formulario lleva data-password-required="false":
        // la contraseña pasa a ser opcional (se deja en blanco para no cambiarla).
        var pwdFlag = $userForm.attr('data-password-required');
        var passwordRequired = !(pwdFlag === 'false' || pwdFlag === false);
        $userForm.validate({
            rules: {
                role_id: {
                    required:true
                },
                password: {
                    required: passwordRequired
                },
                confirm_password: {
                    // Solo se exige si efectivamente se escribió una contraseña.
                    required: function () {
                        return $('#password').val().length > 0;
                    },
                    equalTo: "#password"
                },
                email: {
                    required:true
                },
                status: {
                    required:true
                },
                first_name: {
                    required:true
                },
                last_name: {
                    required:true
                }
            }
        });
    }
    $('#roleForm').validate({
        rules: {
            name:{
                required:true
            }
        }
    });
    $('#vehiculoForm').validate({
        rules: {
            marca: {
                required: true
            },
            modelo: {
                required: true
            },
            ano: {
                required: true
            },
            patente: {
                required: true
            },
            tipo_vehiculo: {
                required: true
            },
            tipo_combustible: {
                required: true
            },
            identificador_vehiculo: {
                required: true
            }
        }
    });
    $('#pageForm').validate({
        rules: {
            title: {
                required: true
            },
            content: {
                required: true
            },
            meta_title: {
                required: true
            }
        }
    });
    $('#categoryForm').validate({
        rules: {
            name: {
              required: true,
            },
            meta_title: {
                required: true,
            }
        }
    });
    $('#tagForm').validate({
        rules: {
            name: {
              required: true,
            },
        }
    });
    $('#blogForm').validate({
        rules: {
            title: {
              required: true,
            },
            categories: {
                required: true,
            },
            content: {
                required: true,
            },
            tags: {
                required: true,
            },
            meta_title: {
                required: true,
            }
        }
    });
    $('#ProveedoresForm').validate({
        rules: {
            nombre: {
              required: true,
            },
            numero_documento: {
                required: true,
            }
        }
    });
    $('#ClientesForm').validate({
        rules: {
            nombre: {
              required: true,
            },
            numero_documento: {
                required: true,
            }
        }
    });

  })(jQuery);