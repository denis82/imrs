var UIJQueryUI = function() {


    var handleDatePickers = function() {


    }

    var handleDialogs = function() {

        $("#remove-element").dialog({
            dialogClass: 'ui-dialog-red',
            autoOpen: false,
            resizable: false,
            height: 180,
            modal: true,
            buttons: [
                {
                    'class': 'btn red',
                    "text": "Удалить",
                    click: function() {
                        $(this).dialog("close");
                        var link = $(this).dialog("option", "remove-link");
                        $.ajax({
                            url: link.attr('href'),
                            beforeSend: function() {
                                App.blockUI(".content-blocked", false);
                            },
                            success: function()
                            {
                                App.unblockUI(".content-blocked", false);
                                link.parent().parent().remove();
                            }
                        });
                    }
                },
                {
                    'class': 'btn',
                    "text": "Отмена",
                    click: function() {

                        $(this).dialog("close");
                    }
                }
            ]
        });


        $(".remove-element").click(function() {
            $("#remove-element").dialog("option", "remove-link", $(this));
            $("#remove-element").dialog("open");
            $('.ui-dialog button').blur();
            return false;
        });

    }

    return {
        //main function to initiate the module
        init: function() {
            handleDatePickers();
            handleDialogs();
        }

    };

}();