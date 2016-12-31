KB.component('image-slideshow', function (containerElement, options) {
    var currentImage;

    function onKeyDown(e) {
        switch (KB.utils.getKey(e)) {
            case 'Escape':
                destroySlide();
                break;
            case 'ArrowRight':
                renderNextSlide();
                break;
            case 'ArrowLeft':
                renderPreviousSlide();
                break;
        }
    }

    function onOverlayClick(element) {
        if (element.matches('.slideshow-next-icon')) {
            renderNextSlide();
        } else if (element.matches('.slideshow-previous-icon')) {
            renderPreviousSlide();
        } else if (element.matches('.slideshow-download-icon')) {
            window.location.href = element.href;
        } else {
            destroySlide();
        }
    }

    function onClick(element) {
        var imageId = KB.dom(element).data('imageId');
        var image = getImage(imageId);

        renderSlide(image);
    }

    function renderNextSlide() {
        destroySlide();

        for (var i = 0; i < options.images.length; i++) {
            if (options.images[i].id === currentImage.id) {
                var index = i + 1;

                if (index >= options.images.length) {
                    index = 0;
                }

                currentImage = options.images[index];
                break;
            }
        }

        renderSlide();
    }

    function renderPreviousSlide() {
        destroySlide();

        for (var i = 0; i < options.images.length; i++) {
            if (options.images[i].id === currentImage.id) {
                var index = i - 1;

                if (index < 0) {
                    index = options.images.length - 1;
                }

                currentImage = options.images[index];
                break;
            }
        }

        renderSlide();
    }

    function renderSlide() {
        var closeElement = KB.dom('div')
            .attr('class', 'fa fa-window-close slideshow-icon slideshow-close-icon')
            .build();

        var downloadElement = KB.dom('a')
            .attr('class', 'fa fa-download slideshow-icon slideshow-download-icon')
            .attr('href', getUrl(currentImage, 'download'))
            .build();

        var previousElement = KB.dom('div')
            .attr('class', 'fa fa-chevron-circle-left slideshow-icon slideshow-previous-icon')
            .build();

        var nextElement = KB.dom('div')
            .attr('class', 'fa fa-chevron-circle-right slideshow-icon slideshow-next-icon')
            .build();

        var imageElement = KB.dom('img')
            .attr('src', getUrl(currentImage, 'image'))
            .attr('alt', currentImage.name)
            .attr('title', currentImage.name)
            .style('maxHeight', (window.innerHeight - 50) + 'px')
            .build();

        var captionElement = KB.dom('figcaption')
            .text(currentImage.name)
            .build();

        var figureElement = KB.dom('figure')
            .add(imageElement)
            .add(captionElement)
            .build();

        var overlayElement = KB.dom('div')
            .addClass('image-slideshow-overlay')
            .add(closeElement)
            .add(downloadElement)
            .add(previousElement)
            .add(nextElement)
            .add(figureElement)
            .click(onOverlayClick)
            .build();

        document.body.appendChild(overlayElement);
        document.addEventListener('keydown', onKeyDown, false);
    }

    function destroySlide() {
        var overlayElement = KB.find('.image-slideshow-overlay');

        if (overlayElement !== null) {
            document.removeEventListener('keydown', onKeyDown, false);
            overlayElement.remove();
        }
    }

    function getImage(imageId) {
        for (var i = 0; i < options.images.length; i++) {
            if (options.images[i].id === imageId) {
                return options.images[i];
            }
        }

        return null;
    }

    function getUrl(image, type) {
        var regex = new RegExp(options.regex, 'g');
        return options.url[type].replace(regex, image.id);
    }

    function buildThumbnailElement(image) {
        return KB.dom('img')
            .attr('src', getUrl(image, 'thumbnail'))
            .attr('alt', image.name)
            .attr('title', image.name)
            .data('imageId', image.id)
            .click(onClick)
            .build();
    }

    this.render = function () {
        currentImage = options.image;
        containerElement.appendChild(buildThumbnailElement(currentImage));
    };
});
