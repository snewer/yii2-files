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

        var uploadInProcess = false;

        var $widget = $('<div class="file-upload-widget">' +
            '<div class="loader">Идет загрузка ...</div>' +
            '<div class="file-upload-widget-container"></div>' +
            '<div class="file-upload-widget-tools">' +
            '<span class="file-upload-tools-clickable select-file-from-device">Выбрать файл с компьютера</span> ' +
            '<!--<span class="file-upload-tools-clickable select-file-from-url">или по URL</span>-->' +
            '</div>' +
            '</div>');

        var $loader = $widget.find('.loader').first();

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
            uploadInProcess = true;
            if (options.chunkUpload) {
                $loader.show();
                $loader.text('Идет загрузка — 0%');
                var fileId = null;
                var start = 0;
                var size = file.size;
                var chunkSize = options.chunkSize;

                setTimeout(append, 1);
                function append() {
                    var end = start + chunkSize;
                    if (size - end < 0) {
                        end = size;
                    }
                    var s = slice(file, start, end);
                    var r = new FileReader();
                    r.onload = function(){
                        $.ajax({
                            url: options.urls.appendFile,
                            type: 'POST',
                            data: {
                                'id': fileId,
                                'name': file.name,
                                'source': r.result.split(',')[1]
                            },
                            success: function (data) {
                                if (end < size) {
                                    fileId = data.file.id;
                                    start += chunkSize;
                                    $loader.text('Идет загрузка — ' + Math.floor((start / size) * 100) + '%');
                                    setTimeout(append, 1);
                                } else {
                                    $input.val(data.file.id);
                                    $widgetContainer.html(getExistFileWidget(data.file));
                                    uploadInProcess = false;
                                    $loader.hide();
                                }
                            }
                        });
                    };
                    r.readAsDataURL(s);
                }
                function slice(file, start, end) {
                    var slice = file.mozSlice ? file.mozSlice : file.webkitSlice ? file.webkitSlice : file.slice ? file.slice : function () {};
                    return slice.bind(file)(start, end);
                }
            } else {
                $loader.text('Идет загрузка ...');
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
                        uploadInProcess = false;
                        $loader.hide();
                    }
                });
            }
        }

        $widget.on('click', '.select-file-from-device', function () {
            if (uploadInProcess) return;
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
            if (uploadInProcess) return;
            uploadFile(e.dataTransfer.files[0]);
            e.preventDefault();
        };

        $widget.on('click', '.rm-btn', function () {
            $input.val('');
            $widgetContainer.html(getEmptyWidget());
        });

    };

})(jQuery);