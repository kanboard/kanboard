// Initialization
$(function() {

    Kanboard.Init();

    if (Kanboard.Exists("board")) {
        Kanboard.Board.Init();
    }
    else if (Kanboard.Exists("calendar")) {
        Kanboard.Calendar.Init();
    }
    else if (Kanboard.Exists("task-section")) {
        Kanboard.Task.Init();
    }
    else if (Kanboard.Exists("analytic-section")) {
        Kanboard.Analytic.Init();
    }
});