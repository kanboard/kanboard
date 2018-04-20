KB.tooltip = function () {
    function onMouseOver(event) {
        if (! exists()) {
            create(event.target);
        }
    }

    function onMouseLeaveContainer() {
        setTimeout(destroy, 500);
    }

    function create(element) {
        var contentElement = element.querySelector("script");
        if (contentElement) {
            render(element, contentElement.innerHTML);
            return;
        }

        var link = element.dataset.href;
        if (link) {
            fetch(link, function (html) {
                if (html) {
                    render(element, html);
                }
            });
        }
    }

    function fetch(url, callback) {
        var request = new XMLHttpRequest();
        request.open("GET", url, true);
        request.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        request.onreadystatechange = function () {
            if (request.readyState === XMLHttpRequest.DONE) {
                if (request.status === 200) {
                    callback(request.responseText);
                }
            }
        };
        request.send(null);
    }

    function render(element, html) {        
        var containerElement = document.createElement("div");
        containerElement.id = "tooltip-container";
        containerElement.innerHTML = html;
        containerElement.addEventListener("mouseleave", onMouseLeaveContainer, false);

        var elementRect = element.getBoundingClientRect();
        var top = elementRect.top + window.scrollY + elementRect.height;
        containerElement.style.top = top + "px";

        if (elementRect.left > (window.innerWidth - 600)) {
            var right = window.innerWidth - elementRect.right - window.scrollX;
            containerElement.style.right = right + "px";
        } else {
            var left = elementRect.left + window.scrollX;
            containerElement.style.left = left + "px";
        }

        document.body.appendChild(containerElement);

        document.body.onclick = function(event) {
            if (! containerElement.contains(event.target)) {
                destroy();
            }
        };
    }

    function destroy() {
        var element = document.getElementById("tooltip-container");
        if (element) {
            element.parentNode.removeChild(element);
        }
    }

    function exists() {
        return !!document.getElementById("tooltip-container");
    }

    var elements = document.querySelectorAll(".tooltip");
    for (var i = 0; i < elements.length; i++) {
        elements[i].addEventListener("mouseenter", onMouseOver, false);
    }
};
