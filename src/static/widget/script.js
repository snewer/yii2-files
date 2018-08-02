(function ($) {

    function getEmptyWidget() {
        return '<span>Файл не загружен</span>';
    }

    function getExistFileWidget(data) {
        return '<p><b><a target="_blank" href="' + data.url + '">' + data.name + '</a></b></p>' +
            '<p>' + data.size + '</p>' +
            '<div title="Удалить файл" class="rm-btn">&times;</div>';
    }

    $.fn.FileUploadWidget = function (options) {

        var $input = this.first();

        var $widget = $('<div class="file-upload-widget">' +
            '<div class="loader"></div>' +
            '<div class="file-upload-widget-container"></div>' +
            '<div class="file-upload-widget-tools">' +
            '<span class="file-upload-tools-clickable select-file-from-device">Выбрать файл с компьютера</span> ' +
            '<!--<span class="file-upload-tools-clickable select-file-from-url">или по URL</span>-->' +
            '</div>' +
            '</div>');

        var $widgetContainer = $widget.find('.file-upload-widget-container').first();

        $input.after($widget);

        if ($input.val() > 0) {
            $.post(options.urls.getFile, {id: $input.val()}, function (res) {
                if (res.success === true) {
                    $widgetContainer.html(getExistFileWidget(res.file));
                }
            }, 'json');
        } else {
            $widgetContainer.html(getEmptyWidget());
        }

        function uploadFile(file) {
            var formData = new FormData();
            formData.append('file', file);
            $.ajax({
                url: options.urls.uploadFile,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (data) {
                    $input.val(data.file.id);
                    $widgetContainer.html(getExistFileWidget(data.file));
                }
            });
        }

        $widget.on('click', '.select-file-from-device', function () {
            var $fileInput = $('<input type="file" style="display:none">');
            $('body').append($fileInput);
            $fileInput.on('change', function () {
                uploadFile(this.files[0]);
                $fileInput.remove();
            });
            $fileInput.click();
        });

        $widget[0].ondragover = function () {
            return false;
        };

        $widget[0].ondragleave = function () {
            return false;
        };

        $widget[0].ondrop = function (e) {
            uploadFile(e.dataTransfer.files[0]);
            e.preventDefault();
        };

        $widget.on('click', '.rm-btn', function () {
            $input.val(0);
            $widgetContainer.html(getEmptyWidget());
        });

    };

})(jQuery);