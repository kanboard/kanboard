function Sidebar() {
}

Sidebar.prototype.expand = function(e) {
    e.preventDefault();
    $(".sidebar-container").removeClass("sidebar-collapsed");
    $(".sidebar-collapse").show();
    $(".sidebar h2").show();
    $(".sidebar ul").show();
    $(".sidebar-expand").hide();
};

Sidebar.prototype.collapse = function(e) {
    e.preventDefault();
    $(".sidebar-container").addClass("sidebar-collapsed");
    $(".sidebar-expand").show();
    $(".sidebar h2").hide();
    $(".sidebar ul").hide();
    $(".sidebar-collapse").hide();
};

Sidebar.prototype.listen = function() {
    $(document).on("click", ".sidebar-collapse", this.collapse);
    $(document).on("click", ".sidebar-expand", this.expand);
};
