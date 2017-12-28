var UINestable = function() {

    var updateOutput = function(e, item) {
        var prev = $(item).prev();
        prev = prev.attr("data-id");
        var next = $(item).next();
        next = next.attr("data-id");
        parent = $(item).parent().parent().attr("data-id");
        if (prev === undefined)
            prev = 0;
        if (next === undefined)
            next = 0;
        if (parent === undefined)
            parent = 0;
        $.ajax({
            url: $(e.target).attr("sort-action") + "?id=" + $(item).attr('data-id') + "&prev=" + prev + "&next=" + next + "&parent=" + parent,
            beforeSend: function() {
                App.blockUI(".content-blocked", false);
            },
            success: function()
            {
                App.unblockUI(".content-blocked", false);
            }
        });
    };


    return {
        init: function() {
            $('#nestable_list').nestable({
                group: 1
            }).on('change', updateOutput);
        }

    };

}();