KB.component('screenshot', function (containerElement) {
    var inputElement = null;

    function onFileLoaded(e) {
        createImage(e.target.result);
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

        initialize();
    }

    this.render = function () {
        inputElement = KB.dom('input')
            .attr('type', 'hidden')
            .attr('name', 'screenshot')
            .build();

        containerElement.appendChild(inputElement);
        initialize();
    };
});
