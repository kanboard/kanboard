function Screenshot() {
    this.pasteCatcher = null;
}

Screenshot.prototype.execute = function() {
    this.initialize();
};

// Setup event listener and workarounds
Screenshot.prototype.initialize = function() {
    this.destroy();

    if (! window.Clipboard) {

        // Create a contenteditable element
        this.pasteCatcher = document.createElement("div");
        this.pasteCatcher.id = "screenshot-pastezone";
        this.pasteCatcher.contentEditable = "true";

        // Insert the content editable at the top to avoid scrolling down in the board view
        this.pasteCatcher.style.opacity = 0;
        this.pasteCatcher.style.position = "fixed";
        this.pasteCatcher.style.top = 0;
        this.pasteCatcher.style.right = 0;
        this.pasteCatcher.style.width = 0;

        document.body.insertBefore(this.pasteCatcher, document.body.firstChild);

        // Set focus on the contenteditable element
        this.pasteCatcher.focus();

        // Set the focus when clicked anywhere in the document
        document.addEventListener("click", this.setFocus.bind(this));

        // Set the focus when clicked in screenshot dropzone (popover)
        document.getElementById("screenshot-zone").addEventListener("click", this.setFocus.bind(this));
    }

    window.addEventListener("paste", this.pasteHandler.bind(this));
};

// Destroy contentEditable element
Screenshot.prototype.destroy = function() {
    if (this.pasteCatcher != null) {
        document.body.removeChild(this.pasteCatcher);
    }
    else if (document.getElementById("screenshot-pastezone")) {
        document.body.removeChild(document.getElementById("screenshot-pastezone"));
    }

    document.removeEventListener("click", this.setFocus.bind(this));
    this.pasteCatcher = null;
};

// Set focus on contentEditable element
Screenshot.prototype.setFocus = function() {
    if (this.pasteCatcher !== null) {
        this.pasteCatcher.focus();
    }
};

// Paste event callback
Screenshot.prototype.pasteHandler = function(e) {
    // Firefox doesn't have the property e.clipboardData.items (only Chrome)
    if (e.clipboardData && e.clipboardData.items) {

        var items = e.clipboardData.items;

        if (items) {

            for (var i = 0; i < items.length; i++) {

                // Find an image in pasted elements
                if (items[i].type.indexOf("image") !== -1) {

                    var blob = items[i].getAsFile();

                    // Get the image as base64 data
                    var reader = new FileReader();
                    var self = this;
                    reader.onload = function(event) {
                        self.createImage(event.target.result);
                    };

                    reader.readAsDataURL(blob);
                }
            }
        }
    }
    else {

        // Handle Firefox
        setTimeout(this.checkInput.bind(this), 100);
    }
};

// Parse the input in the paste catcher element
Screenshot.prototype.checkInput = function() {
    var child = this.pasteCatcher.childNodes[0];

    if (child) {
        // If the user pastes an image, the src attribute
        // will represent the image as a base64 encoded string.
        if (child.tagName === "IMG") {
            this.createImage(child.src);
        }
    }

    this.pasteCatcher.innerHTML = "";
};

// Creates a new image from a given source
Screenshot.prototype.createImage = function(blob) {
    var pastedImage = new Image();
    pastedImage.src = blob;

    // Send the image content to the form variable
    pastedImage.onload = function() {
        var sourceSplit = blob.split("base64,");
        var sourceString = sourceSplit[1];
        $("input[name=screenshot]").val(sourceString);
    };

    var zone = document.getElementById("screenshot-zone");
    zone.innerHTML = "";
    zone.className = "screenshot-pasted";
    zone.appendChild(pastedImage);

    this.destroy();
    this.initialize();
};
