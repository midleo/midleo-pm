<br>
<h4><i class="mdi mdi-gesture-double-tap"></i>&nbsp;Actions</h4>
<br>
<div class="list-group">
    <?php if(!empty($zobj['reqtype'])){?><a
        href='/tickets/type/<?php echo $zobj['reqtype'];?>/<?php echo in_array($thisarray["p2"], array("new","view"))?$thisarray["p1"]:$thisarray["last"];?>'
        class="waves-effect waves-light list-group-item list-group-item-light list-group-item-action"><i
            class="mdi mdi-cogs"></i>&nbsp;Input form: <?php echo $typereq[$zobj['reqtype']];?></a><?php } ?>
    <a href='/tickets/log/<?php echo in_array($thisarray["p2"], array("new","view"))?$thisarray["p1"]:$thisarray["last"];?>'
        class="waves-effect waves-light list-group-item list-group-item-light list-group-item-action"><i
            class="mdi mdi-history"></i>&nbsp;History</a>
    <a href='/tickets/files/<?php echo in_array($thisarray["p2"], array("new","view"))?$thisarray["p1"]:$thisarray["last"];?>'
        class="waves-effect waves-light list-group-item list-group-item-light list-group-item-action"><i
            class="mdi mdi-file-document-multiple-outline"></i>&nbsp;Documentation</a>
    <a id="reqidbut" class="waves-effect waves-light list-group-item list-group-item-light list-group-item-action"><i
            class="mdi mdi-sitemap"></i>&nbsp;Service workflow</a>
    <a href='/reqtasks/<?php echo in_array($thisarray["p2"], array("new","view"))?$thisarray["p1"]:$thisarray["last"];?>'
        class="waves-effect waves-light list-group-item list-group-item-light list-group-item-action"><i
            class="mdi mdi-calendar-check-outline"></i>&nbsp;Subtasks</a>
    <a href='/reqcomm/<?php echo in_array($thisarray["p2"], array("new","view"))?$thisarray["p1"]:$thisarray["last"];?>'
        class="waves-effect waves-light list-group-item list-group-item-light list-group-item-action"><i
            class="mdi mdi-comment-multiple-outline"></i>&nbsp;Comments</a>
    <a href='/ticketinfo/<?php echo in_array($thisarray["p2"], array("new","view"))?$thisarray["p1"]:$thisarray["last"];?>'
        class="waves-effect waves-light list-group-item list-group-item-light list-group-item-action"><i
            class="mdi mdi-history"></i>&nbsp;Back to the request</a>
</div>
<div class="modal" id="modal-hist" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" style="min-width:90%;">
        <div class="modal-content">
            <div class="modal-header">
                Service workflow
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body"></div>
            <input type="hidden" id="reqid"
                value="<?php echo in_array($thisarray["p2"], array("new","view"))?$thisarray["p1"]:$thisarray["last"];?>">
        </div>
    </div>
</div>