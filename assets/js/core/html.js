KB.html.label = function (label, id) {
    return KB.dom('label').attr('for', id).text(label).build();
};

KB.html.radio = function (label, name, value) {
    return KB.dom('label')
        .add(KB.dom('input')
            .attr('type', 'radio')
            .attr('name', name)
            .attr('value', value)
            .build()
        )
        .text(label)
        .build();
};

KB.html.radios = function (items) {
    var html = KB.dom('div');

    for (var item in items) {
        if (items.hasOwnProperty(item)) {
            html.add(KB.html.radio(item.label, item.name, item.value));
        }
    }
};
