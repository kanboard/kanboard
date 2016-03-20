Kanboard.FileUpload = function(app) {
    this.app = app;
    this.files = [];
    this.currentFile = 0;
};

Kanboard.FileUpload.prototype.onPopoverOpened = function() {
    var dropzone = document.getElementById("file-dropzone");
    var self = this;

    if (dropzone) {
        dropzone.ondragover = dropzone.ondragenter = function(e) {
            e.stopPropagation();
            e.preventDefault();
        };

        dropzone.ondrop = function(e) {
            e.stopPropagation();
            e.preventDefault();
            self.files = e.dataTransfer.files;
            self.show();
            $("#file-error-max-size").hide();
        };

        $(document).on("click", "#file-browser", function(e) {
            e.preventDefault();
            $("#file-form-element").get(0).click();
        });

        $(document).on("click", "#file-upload-button", function(e) {
            e.preventDefault();
            self.currentFile = 0;
            self.checkFiles();
        });

        $("#file-form-element").change(function() {
            self.files = document.getElementById("file-form-element").files;
            self.show();
            $("#file-error-max-size").hide();
        });
    }
};

Kanboard.FileUpload.prototype.show = function() {
    $("#file-list").remove();

    if (this.files.length > 0) {
        $("#file-upload-button").prop("disabled", false);
        $("#file-dropzone-inner").hide();

        var ul = jQuery("<ul>", {"id": "file-list"});

        for (var i = 0; i < this.files.length; i++) {
            var percentage = jQuery("<span>", {"id": "file-percentage-" + i}).append("(0%)");
            var progress = jQuery("<progress>", {"id": "file-progress-" + i, "value": 0});
            var li = jQuery("<li>", {"id": "file-label-" + i})
                .append(progress)
                .append("&nbsp;")
                .append(this.files[i].name)
                .append("&nbsp;")
                .append(percentage);

            ul.append(li);
        }

        $("#file-dropzone").append(ul);
    } else {
        $("#file-dropzone-inner").show();
    }
};

Kanboard.FileUpload.prototype.checkFiles = function() {
    var max = parseInt($("#file-dropzone").data("max-size"));

    for (var i = 0; i < this.files.length; i++) {
        if (this.files[i].size > max) {
            $("#file-error-max-size").show();
            $("#file-label-" + i).addClass("file-error");
            $("#file-upload-button").prop("disabled", true);
            return;
        }
    }

    this.uploadFiles();
};

Kanboard.FileUpload.prototype.uploadFiles = function() {
    if (this.files.length > 0) {
        this.uploadFile(this.files[this.currentFile]);
    }
};

Kanboard.FileUpload.prototype.uploadFile = function(file) {
    var dropzone = document.getElementById("file-dropzone");
    var url = dropzone.dataset.url;
    var xhr = new XMLHttpRequest();
    var fd = new FormData();

    xhr.upload.addEventListener("progress", this.updateProgress.bind(this));
    xhr.upload.addEventListener("load", this.transferComplete.bind(this));

    xhr.open("POST", url, true);
    fd.append('files[]', file);
    xhr.send(fd);
};

Kanboard.FileUpload.prototype.updateProgress = function(e) {
    if (e.lengthComputable) {
        $("#file-progress-" + this.currentFile).val(e.loaded / e.total);
        $("#file-percentage-" + this.currentFile).text('(' + Math.floor((e.loaded / e.total) * 100) + '%)');
    }
};

Kanboard.FileUpload.prototype.transferComplete = function() {
    this.currentFile++;

    if (this.currentFile < this.files.length) {
        this.uploadFile(this.files[this.currentFile]);
    } else {
        var uploadButton = $("#file-upload-button");
        uploadButton.prop("disabled", true);
        uploadButton.parent().hide();
        $("#file-done").show();
    }
};
