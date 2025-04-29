var files = [];
KB.component('file-upload-task-create', function (containerElement, options) {
    var inputFileElement = null;
    var dropzoneElement = null;
    var totalSize = 0;
    var currentScreenshotSize = 0;
    var inputElement = null;

    function onFileLoaded(e) {
        createImage(e.target.result);
        sizeOfImage(e.target.result);
    }

    function sizeOfImage(e) {
        totalSize -= currentScreenshotSize;
        currentScreenshotSize = e.length;
        totalSize += currentScreenshotSize;
        checkMaxSize();
    }

    function onPaste(e) {
        if (e.clipboardData && e.clipboardData.items) {
            var items = e.clipboardData.items;

            if (items) {
                for (var i = 0; i < items.length; i++) {
                    // Find an image in pasted elements
                    if (items[i].type.indexOf("image") !== -1) {
                        var blob = items[i].getAsFile();

                        // Get the image as base64 data
                        var reader = new FileReader();
                        reader.onload = onFileLoaded;
                        reader.readAsDataURL(blob);
                    }
                }
            }
        }
    }

    function initialize() {
        window.addEventListener('paste', onPaste, false);
    }

    function createImage(blob) {
        var pastedImage = new Image();
        pastedImage.src = blob;

        // Send the image content to the form variable
        pastedImage.onload = function() {
            var sourceSplit = blob.split('base64,');
            inputElement.value = sourceSplit[1];
        };

        var zone = document.getElementById('screenshot-zone');
        zone.innerHTML = '';
        zone.className = 'screenshot-pasted';
        zone.appendChild(pastedImage);
    }

    function checkMaxSize() {
        var isOversize = false;
        var message = KB.find('#message-container');
        var messageElement = KB.dom(containerElement).parent('.task-form-bottom');

        if (totalSize > options.maxSize) {
            isOversize = true;
        }

        if (isOversize) {
            KB.trigger('modal.disable');
            if (!message) {
                messageElement
                    .insertBefore(KB.dom('div')
                    .attr('id', 'message-container')
                    .attr('class', 'alert alert-error')
                    .text(options.labelOversize)
                    .build(), messageElement.firstChild);
            }
        } else {
            KB.trigger('modal.enable');
            if (message){
                message.remove();
            }
        }
    }

    function onFileChange() {
        for (var i = 0; i < inputFileElement.files.length; i++) {
            files.push(inputFileElement.files[i]);
            totalSize += files[i].size;
        }
        showFiles();
    }

    function onClickFileBrowser() {
        files = [];
        inputFileElement.click();
    }

    function onDragOver(e) {
        e.stopPropagation();
        e.preventDefault();
    }

    function onDrop(e) {
        e.stopPropagation();
        e.preventDefault();

        for (var i = 0; i < e.dataTransfer.files.length; i++) {
            files.push(e.dataTransfer.files[i]);
            totalSize += files[i].size;
        }

        showFiles();
    }

    function showFiles() {
        var newFileList = new DataTransfer();
        files.forEach(function (file) {
            newFileList.items.add(file);
        });
        inputFileElement.files = newFileList.files;
        if (files.length > 0) {
            KB.dom(dropzoneElement)
                .empty()
                .add(buildFileListElement());
        } else {
            KB.dom(dropzoneElement)
                .empty()
                .add(buildInnerDropzoneElement());
        }
        checkMaxSize();
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

        return dropzoneElement;
    }

    function buildFileListItem(index) {

        var deleteElement = KB.dom('span')
            .attr('id', 'file-delete-' + index)
            .html('<a href="#"><i class="fa fa-trash fa-fw"></i></a>')
            .on('click', function () {
                totalSize -= files[index].size;
                files.splice(index, 1);
                KB.find('#file-item-' + index).remove();
                showFiles();
            })
            .build();

        var itemElement = KB.dom('li')
            .attr('id', 'file-item-' + index)
            .add(deleteElement)
            .text(' ' + files[index].name + ' ');

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

    function onLoad() {
        if (options.screenshot) {
            var data = 'data:image/png;base64,' + options.screenshot;
            createImage(data);
        }
        if (files.length !== 0) {
            showFiles();
        }
    }

    this.render = function () {
        KB.on('modal.submit', onSubmit);
        KB.on('modal.stop', function () {
            KB.removeListener('modal.submit', onSubmit);
        });
        KB.on('modal.close', function () {
            files = [];
            KB.removeListener('modal.submit', onSubmit);
        });
        inputElement = KB.dom('input')
            .attr('type', 'hidden')
            .attr('name', 'screenshot')
            .build();

        inputFileElement = buildFileInputElement();
        dropzoneElement = buildDropzoneElement();
        containerElement.appendChild(inputElement);
        containerElement.appendChild(inputFileElement);
        containerElement.appendChild(dropzoneElement);
        initialize();
        onLoad();
    };

    function onSubmit() {
        var form = document.querySelector('#modal-content form');

        if (form) {
            var url = form.getAttribute('action');

            if (url) {
                KB.http.postForm(url, form).success(function (response) {
                    KB.trigger('modal.stop');

                    if (response) {
                        KB.modal.replaceHtml(response);
                    } else {
                        KB.modal.close();
                    }
                }).error(function (response) {
                    KB.trigger('modal.stop');

                    if (response.hasOwnProperty('message')) {
                        window.alert(response.message);
                    }
                });
            }
        }
    }
});
