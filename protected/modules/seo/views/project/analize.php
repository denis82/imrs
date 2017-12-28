<div id="progress-container">
    <label class="control-label"><?= $message ?></label>
    <div class="progress">
        <div style="width: 0%;" class="bar" id="progress-bar"></div>
    </div>
</div>
<script>
    $(document).ready(function() {
        call(0, 1);
    })
    function call(pos, step)
    {
        $.ajax({
            url: "?pos=" + pos + "&step=" + step,
            dataType: "json",
        }).done(function(data) {
            $("#progress-bar").attr("style", "width:" + data.percent + "%");
            $("#progress-container").find(".control-label").html(data.message);
            if (data.state === "progress")
                call(parseInt(data.pos) + 1, data.step);
            else
            {
                $("#progress-container").find(".control-label").html("Анализ завершен");
                $("#progress-bar").parent().hide();
                document.location.href = "/seo/project/";
            }

        });
    }
</script>