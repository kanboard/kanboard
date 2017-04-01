KB.keyboardShortcuts = function () {
    function goToLink (selector) {
        if (! KB.modal.isOpen()) {
            var element = KB.find(selector);

            if (element !== null) {
                window.location = element.attr('href');
            }
        }
    }

    function submitForm() {
        if (KB.modal.isOpen()) {
            KB.modal.submitForm();
        } else {
            var forms = $("form");

            if (forms.length == 1) {
                forms.submit();
            } else if (forms.length > 1) {
                if (document.activeElement.tagName === 'INPUT' || document.activeElement.tagName === 'TEXTAREA') {
                    $(document.activeElement).parents("form").submit();
                }
            }
        }
    }

    KB.onKey('?', function () {
        if (! KB.modal.isOpen()) {
            KB.modal.open(KB.find('body').data('keyboardShortcutUrl'));
        }
    });

    KB.onKey('Escape', function () {
        if (! KB.exists('#suggest-menu')) {
            KB.trigger('modal.close');
            _KB.get("Dropdown").close();
        }
    });

    KB.onKey('Enter', submitForm, true, true);
    KB.onKey('Enter', submitForm, true, false, true);

    KB.onKey('b', function () {
        if (! KB.modal.isOpen()) {
            KB.trigger('board.selector.open');
        }
    });

    if (KB.exists('#board')) {
        KB.onKey('c', function () {
            if (! KB.modal.isOpen()) {
                _KB.get('BoardHorizontalScrolling').toggle();
            }
        });

        KB.onKey('s', function () {
            if (! KB.modal.isOpen()) {
                _KB.get('BoardCollapsedMode').toggle();
            }
        });

        KB.onKey('n', function () {
            if (! KB.modal.isOpen()) {
                KB.modal.open(KB.find('#board').data('taskCreationUrl'), 'large', false);
            }
        });
    }

    if (KB.exists('#task-view')) {
        KB.onKey('e', function () {
            if (! KB.modal.isOpen()) {
                KB.modal.open(KB.find('#task-view').data('editUrl'), 'large', false);
            }
        });

        KB.onKey('c', function () {
            if (! KB.modal.isOpen()) {
                KB.modal.open(KB.find('#task-view').data('commentUrl'), 'medium', false);
            }
        });

        KB.onKey('s', function () {
            if (! KB.modal.isOpen()) {
                KB.modal.open(KB.find('#task-view').data('subtaskUrl'), 'medium', false);
            }
        });

        KB.onKey('l', function () {
            if (! KB.modal.isOpen()) {
                KB.modal.open(KB.find('#task-view').data('internalLinkUrl'), 'medium', false);
            }
        });
    }

    KB.onKey('f', function () {
        if (! KB.modal.isOpen()) {
            KB.focus('#form-search');
        }
    });

    KB.onKey('r', function () {
        if (! KB.modal.isOpen()) {
            var reset = $(".filter-reset").data("filter");
            var input = $("#form-search");

            input.val(reset);
            $("form.search").submit();
        }
    });

    KB.onKey('v+o', function () {
        goToLink('a.view-overview');
    });

    KB.onKey('v+b', function () {
        goToLink('a.view-board');
    });

    KB.onKey('v+l', function () {
        goToLink('a.view-listing');
    });
};
