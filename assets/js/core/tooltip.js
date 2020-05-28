KB.on('dom.ready', function() {
    function onMouseOver(mytarget) {
        if (! exists()) {
            create(mytarget);
        }
    }

    function onMouseLeaveContainer() {
        setTimeout(destroy, 500);
    }

    function mouseLeftParent() {
        setTimeout(destroyIfNotOnTooltip, 500);
    }

    function mouseOnTooltip() {
        document.getElementById("tooltip-container").mouseOnTooltip = true;
    }

    function destroyIfNotOnTooltip() {
        var div = document.getElementById("tooltip-container");
        if(div != null && !div.mouseOnTooltip) destroy();
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
        containerElement.addEventListener("mouseenter", mouseOnTooltip, false);
        containerElement.mouseOnTooltip = false;

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
    
    // for dynamically added elements, we add our event listeners to the doc body
    // we need to use mouseover, because mouseenter only triggers on the body in this case
    document.body.addEventListener('mouseover', function(e) {
        if (e.target.classList.contains('tooltip')) {
            onMouseOver(e.target);
        }
        // to catch the case where the event doesn't fire on tooltip but on the i-subelement
        //    (this seems to depend on how you move your mouse over the element ...)
        if (e.target.classList.contains('fa') && e.target.parentNode.classList.contains('tooltip')) {
            onMouseOver(e.target.parentNode);
        }
    });
    document.body.addEventListener('mouseout', function(e) {
        if (e.target.classList.contains('tooltip')) {
            mouseLeftParent();
        }
        if (e.target.classList.contains('fa') && e.target.parentNode.classList.contains('tooltip')) {
            mouseLeftParent();
        }
    });
});
