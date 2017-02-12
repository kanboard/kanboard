KB.component('file-upload', function (containerElement, options) {
    var inputFileElement = null;
    var dropzoneElement = null;
    var files = [];
    var currentFileIndex = 0;

    function onProgress(e) {
        if (e.lengthComputable) {
            var progress = e.loaded / e.total;
            var percentage = Math.floor(progress * 100);

            KB.find('#file-progress-' + currentFileIndex).attr('value', progress);
            KB.find('#file-percentage-' + currentFileIndex).replaceText('(' + percentage + '%)');
        }
    }

    function onError() {
        var errorElement = KB.dom('div').addClass('file-error').text(options.labelUploadError).build();
        KB.find('#file-item-' + currentFileIndex).add(errorElement);
    }

    function onServerError(response) {
        var errorElement = KB.dom('div').addClass('file-error').text(response.message).build();
        KB.find('#file-item-' + currentFileIndex).add(errorElement);
        KB.trigger('modal.stop');
    }

    function onComplete() {
        currentFileIndex++;

        if (currentFileIndex < files.length) {
            KB.http.uploadFile(options.url, files[currentFileIndex], onProgress, onComplete, onError, onServerError);
        } else {
            KB.trigger('modal.stop');
            KB.trigger('modal.hide');

            var alertElement = KB.dom('div')
                .addClass('alert')
                .addClass('alert-success')
                .text(options.labelSuccess)
                .build();

            var buttonElement = KB.dom('button')
                .attr('type', 'button')
                .addClass('btn')
                .addClass('btn-blue')
                .click(onCloseWindow)
                .text(options.labelCloseSuccess)
                .build();

            KB.dom(dropzoneElement).replace(KB.dom('div').add(alertElement).add(buttonElement).build());
        }
    }

    function onCloseWindow() {
        window.location.reload();
    }

    function onSubmit() {
        currentFileIndex = 0;
        uploadFiles();
    }

    function onFileChange() {
        files = inputFileElement.files;
        showFiles();
    }

    function onClickFileBrowser() {
        files = [];
        currentFileIndex = 0;
        inputFileElement.click();
    }

    function onDragOver(e) {
        e.stopPropagation();
        e.preventDefault();
    }

    function onDrop(e) {
        e.stopPropagation();
        e.preventDefault();

        files = e.dataTransfer.files;
        showFiles();
    }

    function uploadFiles() {
        if (files.length > 0) {
            KB.http.uploadFile(options.url, files[currentFileIndex], onProgress, onComplete, onError, onServerError);
        }
    }

    function showFiles() {
        if (files.length > 0) {
            KB.trigger('modal.enable');

            KB.dom(dropzoneElement)
                .empty()
                .add(buildFileListElement());
        } else {
            KB.trigger('modal.disable');

            KB.dom(dropzoneElement)
                .empty()
                .add(buildInnerDropzoneElement());
        }
    }

    function buildFileInputElement() {
        return KB.dom('input')
            .attr('id', 'file-input-element')
            .attr('type', 'file')
            .attr('name', 'files[]')
            .attr('multiple', true)
            .on('change', onFileChange)
            .hide()
            .build();
    }

    function buildInnerDropzoneElement() {
        var dropzoneLinkElement = KB.dom('a')
            .attr('href', '#')
            .text(options.labelChooseFiles)
            .click(onClickFileBrowser)
            .build();

        return KB.dom('div')
            .attr('id', 'file-dropzone-inner')
            .text(options.labelDropzone + ' ' + options.labelOr + ' ')
            .add(dropzoneLinkElement)
            .build();
    }

    function buildDropzoneElement() {
        var dropzoneElement = KB.dom('div')
            .attr('id', 'file-dropzone')
            .add(buildInnerDropzoneElement())
            .build();

        dropzoneElement.ondragover = onDragOver;
        dropzoneElement.ondrop = onDrop;
        dropzoneElement.ondragover = onDragOver;

        return dropzoneElement;
    }

    function buildFileListItem(index) {
        var isOversize = false;
        var progressElement = KB.dom('progress')
            .attr('id', 'file-progress-' + index)
            .attr('value', 0)
            .build();

        var percentageElement = KB.dom('span')
            .attr('id', 'file-percentage-' + index)
            .text('(0%)')
            .build();

        var itemElement = KB.dom('li')
            .attr('id', 'file-item-' + index)
            .add(progressElement)
            .text(' ' + files[index].name + ' ')
            .add(percentageElement);

        if (files[index].size > options.maxSize) {
            itemElement.add(KB.dom('div').addClass('file-error').text(options.labelOversize).build());
            isOversize = true;
        }

        if (isOversize) {
            KB.trigger('modal.disable');
        }

        return itemElement.build();
    }

    function buildFileListElement() {
        var fileListElement = KB.dom('ul')
            .attr('id', 'file-list')
            .build();

        for (var i = 0; i < files.length; i++) {
            fileListElement.appendChild(buildFileListItem(i));
        }

        return fileListElement;
    }

    this.render = function () {
        KB.on('modal.submit', onSubmit);
        KB.on('modal.close', function () {
           KB.removeListener('modal.submit', onSubmit);
        });

        inputFileElement = buildFileInputElement();
        dropzoneElement = buildDropzoneElement();
        containerElement.appendChild(inputFileElement);
        containerElement.appendChild(dropzoneElement);
    };
});
