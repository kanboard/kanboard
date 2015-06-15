/**
 * Created by wmeyer on 6/15/2015.
 */

$( document ).ready(function() {
    var redirectUrl = '<?php echo $jsonUrl ;?>';
    console.log(redirectUrl);
    //Kanboard.OpenPopover('?controller=task&action=create&project_id=1&category_id=0&column_id=1&color_id=yellow&score=&time_estimated=&date_due=&creator_id=1', Kanboard.InitAfterAjax);
    //?controller=task&action=create&another_task=1&project_id=1&owner_id=1&category_id=0&column_id=1&color_id=yellow&score=&time_estimated=&date_due=&creator_id=1
});