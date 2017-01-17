KB.component('project-select-role', function (containerElement, options) {
    var isLoading = false;
    var isSuccess = false;
    var isError = false;
    var componentElement;

    function onChange(element) {
        isLoading = true;
        options.role = element.value;
        replaceComponentElement();
        updateRole();
    }

    function updateRole() {
        KB.http.postJson(options.url, {
            id: options.id,
            role: options.role
        }).success(function () {
            isLoading = false;
            isSuccess = true;
            replaceComponentElement();
        }).error(function () {
            isLoading = false;
            isSuccess = false;
            isError = true;
            replaceComponentElement();
        });
    }

    function replaceComponentElement() {
        KB.dom(componentElement).remove();
        componentElement = buildComponentElement();
        containerElement.appendChild(componentElement);
    }

    function buildComponentElement() {
        var roles = [];
        var container = KB.dom('div');

        for (var role in options.roles) {
            if (options.roles.hasOwnProperty(role)) {
                var item = {value: role, text: options.roles[role]};

                if (options.role === role) {
                    item.selected = 'selected';
                }

                roles.push(item);
            }
        }

        container.add(KB.dom('select').change(onChange).for('option', roles).build());

        if (isLoading) {
            container.text(' ');
            container.add(KB.dom('i').attr('class', 'fa fa-spinner fa-pulse fa-fw').build());
        } else if (isSuccess) {
            container.text(' ');
            container.add(KB.dom('i').attr('class', 'fa fa-check fa-fw icon-fade-out icon-success').build());
        } else if (isError) {
            container.text(' ');
            container.add(KB.dom('i').attr('class', 'fa fa-check fa-fw icon-fade-out icon-error').build());
        }

        return container.build();
    }

    this.render = function () {
        componentElement = buildComponentElement();
        containerElement.appendChild(componentElement);
    };
});
