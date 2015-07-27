Kanboard.Screenshot = (function() {

    var pasteCatcher = null;

    // Setup event listener and workarounds
    function init()
    {
        destroy();

        if (! window.Clipboard) {

            // Create a contenteditable element
            pasteCatcher = document.createElement("div");
            pasteCatcher.id = "screenshot-pastezone";
            pasteCatcher.contentEditable = "true";

            // Insert the content editable at the top to avoid scrolling down in the board view
            pasteCatcher.style.opacity = 0;
            pasteCatcher.style.position = "fixed";
            pasteCatcher.style.top = 0;
            pasteCatcher.style.right = 0;
            pasteCatcher.style.width = 0;

            document.body.insertBefore(pasteCatcher, document.body.firstChild);

            // Set focus on the contenteditable element
            pasteCatcher.focus();

            // Set the focus when clicked anywhere in the document
            document.addEventListener("click", setFocus);

            // Set the focus when clicked in screenshot dropzone (popover)
            document.getElementById("screenshot-zone").addEventListener("click", setFocus);
        }

        window.addEventListener("paste", pasteHandler);
    }

    // Set focus on the contentEditable element
    function setFocus()
    {
        if (pasteCatcher !== null) {
            pasteCatcher.focus();
        }
    }

    // Destroy contenteditable
    function destroy()
    {
        if (pasteCatcher != null) {
            document.body.removeChild(pasteCatcher);
        }
        else if (document.getElementById("screenshot-pastezone")) {
            document.body.removeChild(document.getElementById("screenshot-pastezone"));
        }

        document.removeEventListener("click", setFocus);
        pasteCatcher = null;
    }

    // Paste event callback
    function pasteHandler(e)
    {
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
                        reader.onload = function(event) {
                            createImage(event.target.result);
                        };

                        reader.readAsDataURL(blob);
                    }
                }
            }
        }
        else {

            // Handle Firefox
            setTimeout(checkInput, 100);
        }
    }

    // Parse the input in the paste catcher element
    function checkInput()
    {
        var child = pasteCatcher.childNodes[0];

        if (child) {
            // If the user pastes an image, the src attribute
            // will represent the image as a base64 encoded string.
            if (child.tagName === "IMG") {
                createImage(child.src);
            }
        }

        pasteCatcher.innerHTML = "";
    }

    // Creates a new image from a given source
    function createImage(blob)
    {
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

        destroy();
        init();
    }

    jQuery(document).ready(function() {

        if (Kanboard.Exists("screenshot-zone")) {
            init();
        }
    });

    return {
        Init: init,
        Destroy: destroy
    };
})();