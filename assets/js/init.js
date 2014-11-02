// Initialization
$(function() {

    Kanboard.Init();

    if ($("#board").length) {
        Kanboard.Board.Init();
    }
    else if ($("#task-section").length) {
        Kanboard.Task.Init();
    }
});