KB.component('screenshot', function (containerElement) {
    var pasteCatcher = null;
    var inputElement = null;

    function onFileLoaded(e) {
        createImage(e.target.result);
    }

    function onPaste(e) {
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
                        reader.onload = onFileLoaded;
                        reader.readAsDataURL(blob);
                    }
                }
            }
        } else {

            // Handle Firefox
            setTimeout(checkInput, 100);
        }
    }

    function initialize() {
        destroy();

        if (! window.Clipboard) {
            // Insert the content editable at the top to avoid scrolling down in the board view
            pasteCatcher = document.createElement('div');
            pasteCatcher.id = 'screenshot-pastezone';
            pasteCatcher.contentEditable = true;
            pasteCatcher.style.opacity = 0;
            pasteCatcher.style.position = 'fixed';
            pasteCatcher.style.top = 0;
            pasteCatcher.style.right = 0;
            pasteCatcher.style.width = 0;
            document.body.insertBefore(pasteCatcher, document.body.firstChild);

            pasteCatcher.focus();

            // Set the focus when clicked anywhere in the document
            document.addEventListener('click', setFocus);

            // Set the focus when clicked in screenshot dropzone
            document.getElementById('screenshot-zone').addEventListener('click', setFocus);
        }

        window.addEventListener('paste', onPaste, false);
    }

    function destroy() {
        if (KB.exists('#screenshot-pastezone')) {
            KB.find('#screenshot-pastezone').remove();
        }

        document.removeEventListener('click', setFocus);
        pasteCatcher = null;
    }
    
    function setFocus() {
        if (pasteCatcher !== null) {
            pasteCatcher.focus();
        }
    }
    
    function checkInput() {
        var child = pasteCatcher.childNodes[0];

        if (child) {
            // If the user pastes an image, the src attribute
            // will represent the image as a base64 encoded string.
            if (child.tagName === 'IMG') {
                createImage(child.src);
            }
        }

        pasteCatcher.innerHTML = '';
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

        destroy();
        initialize();
    }

    KB.on('modal.close', function () {
        destroy();
    });

    this.render = function () {
        inputElement = KB.dom('input')
            .attr('type', 'hidden')
            .attr('name', 'screenshot')
            .build();

        containerElement.appendChild(inputElement);
        initialize();
    };
});
