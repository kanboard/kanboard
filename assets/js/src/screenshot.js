Kanboard.Screenshot = (function() {

    var pasteCatcher = null;

    // Setup event listener and workarounds
    function init()
    {
        if (! window.Clipboard) {

            // Create a contenteditable element
            pasteCatcher = document.createElement("div");
            pasteCatcher.setAttribute("contenteditable", "");
            pasteCatcher.style.opacity = 0;
            document.body.appendChild(pasteCatcher);

            // Make sure it is always in focus
            pasteCatcher.focus();
            document.addEventListener("click", function() { pasteCatcher.focus(); });
        }

        window.addEventListener("paste", pasteHandler);
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
        pasteCatcher.innerHTML = "";

        if (child) {
            // If the user pastes an image, the src attribute
            // will represent the image as a base64 encoded string.
            if (child.tagName === "IMG") {
                createImage(child.src);
            }
        }
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

        document.getElementById("screenshot-inner").style.display = "none";
        document.getElementById("screenshot-zone").className = "screenshot-pasted";
        document.getElementById("screenshot-zone").appendChild(pastedImage);
    }

    jQuery(document).ready(function() {

        if (Kanboard.Exists("screenshot-zone")) {
            init();
        }
    });

    return {
        Init: init
    };
})();