KB.keyboardShortcuts = function () {
    function goToLink (selector) {
        var element = KB.find(selector);

        if (element !== null) {
            window.location = element.attr('href');
        }
    }

    function submitForm() {
        var forms = $("form");

        if (forms.length == 1) {
            forms.submit();
        } else if (forms.length > 1) {
            if (document.activeElement.tagName === 'INPUT' || document.activeElement.tagName === 'TEXTAREA') {
                $(document.activeElement).parents("form").submit();
            } else if (KB.modal.isOpen()) {
                KB.modal.getForm().submit();
            }
        }
    }

    KB.onKey('?', function () {
        KB.modal.open(KB.find('body').data('keyboardShortcutUrl'));
    });

    KB.onKey('Escape', function () {
        if (! KB.exists('#suggest-menu')) {
            KB.trigger('modal.close');
            _KB.get("Dropdown").close();
        }
    });

    KB.onKey('Meta+Enter', submitForm, true);
    KB.onKey('Control+Enter', submitForm, true);

    KB.onKey('b', function () {
        KB.trigger('board.selector.open');
    });

    if (KB.exists('#board')) {
        KB.onKey('c', function () {
            _KB.get('BoardHorizontalScrolling').toggle();
        });

        KB.onKey('s', function () {
            _KB.get('BoardCollapsedMode').toggle();
        });

        KB.onKey('n', function () {
            KB.modal.open(KB.find('#board').data('taskCreationUrl'), 'large', false);
        });
    }

    if (KB.exists('#task-view')) {
        KB.onKey('e', function () {
            KB.modal.open(KB.find('#task-view').data('editUrl'), 'large');
        });

        KB.onKey('c', function () {
            KB.modal.open(KB.find('#task-view').data('commentUrl'));
        });

        KB.onKey('s', function () {
            KB.modal.open(KB.find('#task-view').data('subtaskUrl'));
        });

        KB.onKey('l', function () {
            KB.modal.open(KB.find('#task-view').data('internalLinkUrl'));
        });
    }

    KB.onKey('f', function () {
        KB.focus('#form-search');
    });

    KB.onKey('r', function () {
        var reset = $(".filter-reset").data("filter");
        var input = $("#form-search");

        input.val(reset);
        $("form.search").submit();
    });

    KB.onKey('v+o', function () {
        goToLink('a.view-overview');
    });

    KB.onKey('v+b', function () {
        goToLink('a.view-board');
    });

    KB.onKey('v+c', function () {
        goToLink('a.view-calendar');
    });

    KB.onKey('v+l', function () {
        goToLink('a.view-listing');
    });

    KB.onKey('v+g', function () {
        goToLink('a.view-gantt');
    });
};
