var request;

var ActionClass = {
    button: {
        addDisabled: function (object, callback) {
            object.prop('disabled', true);
            ActionClass.callback(callback);
        },
        delDisabled: function (object, callback) {
            object.prop('disabled', false);
            ActionClass.callback(callback);
        }
    },
    field: {
        addError: function (object, error, callback) {
            object
                .parents('.form-group')
                .addClass('has-error')
                .find('.help-block')
                .html(error);
            ActionClass.callback(callback);
        },
        delError: function (object, callback) {
            object
                .parents('.form-group')
                .removeClass('has-error')
                .find('.help-block')
                .html('');
            ActionClass.callback(callback);
        }
    },
    callback: function (callback) {
        if (callback && typeof(callback) === "function") {
            return callback();
        }
    }
};

var MainClass = {
    experiment: function () {
        var option = {
            message: $("#inputMessage"),
            button: $("#buttonExperiment"),
            error: false
        };

        if (option.message.val().length > 0) {
            ActionClass.field.delError(option.message);
        } else {
            ActionClass.field.addError(option.message, "Вопрос не введён");
            option.error = true;
        }

        if(option.error){
            return false;
        }

        if (request) {
            request.abort();
        }

        ActionClass.button.addDisabled(option.button);

        var m = {
            message: option.message.val()
        };


        request = $.ajax({
            type: "POST",
            url: '/api?action=experiment',
            dataType: 'json',
            data: m
        })
            .done(function (data) {
                ActionClass.button.delDisabled(option.button);
                if (data['response']) {
                    if (data['response']['success']) {
                        $('.mtr').html(
                            '<div class="alert alert-success">'+
                                '<p>Результат:</p>'+
                                '<h2>Идентификатор: ' + data['response']['specId'] + '</h2>'+
                                '<h2>Наименование: ' + data['response']['specName'] + '</h2>'+
                            '</div>'
                        );
                    } else {
                        alert(data['response']['errorCode']);
                    }
                    console.log(data);
                }
            })
            .fail(function () {
                ActionClass.button.delDisabled(option.button);
                alert('400');
            });


        return false;
    },
    training: function () {
        var option = {
            message: $("#inputMessage"),
            selectSpecialization: $("#selectSpecialization"),
            button: $("#buttonTraining"),
            error: false
        };

        if (option.message.val().length > 0) {
            ActionClass.field.delError(option.message);
        } else {
            ActionClass.field.addError(option.message, "Вопрос не введён");
            option.error = true;
        }

        if (option.selectSpecialization.val() > 0) {
            ActionClass.field.delError(option.selectSpecialization);
        } else {
            ActionClass.field.addError(option.selectSpecialization, "Специализация не выбрана");
            option.error = true;
        }

        if(option.error){
            return false;
        }

        if (request) {
            request.abort();
        }

        ActionClass.button.addDisabled(option.button);

        var m = {
            message: option.message.val(),
            specId: option.selectSpecialization.val()
        };

        request = $.ajax({
            type: "POST",
            url: '/api?action=training',
            dataType: 'json',
            data: m
        })
            .done(function (data) {
                ActionClass.button.delDisabled(option.button);
                if (data['response']) {
                    if (data['response']['success']) {
                        $('.mtt__success').fadeIn(100);
                        setTimeout(function(){
                            $('.mtt__success').fadeOut(1000);
                        },1000);
                        $('.mtr').html('');
                    } else {
                        alert(data['response']['errorCode']);
                    }
                }
                console.log(data);
            })
            .fail(function () {
                ActionClass.button.delDisabled(option.button);
                alert('400');
            });


        return false;
    },
    clear: function () {
        $('.mtr').html('');
        $("#inputMessage").val('');
        return false;
    }
};

