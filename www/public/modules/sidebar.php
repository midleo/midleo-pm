<div class="sticky-top"><br>
<h4><i class="mdi mdi-gesture-double-tap"></i>&nbsp;Navigation</h4>
<br>
<div class="list-group">
    <a href="/cp/?" class="waves-effect waves-light list-group-item list-group-item-light list-group-item-action <?php echo $page=="dashboard"?"active":"";?>"><i class="mdi mdi-view-dashboard-outline"></i>&nbsp;Dashboard</a>
    <a href="/tickets" class="waves-effect waves-light list-group-item list-group-item-light list-group-item-action <?php echo $thisarray["p0"]=="tickets"?"active":"";?>"><i class="mdi mdi-format-list-checks"></i>&nbsp;Tickets</a>
    <a href="/changes" class="waves-effect waves-light list-group-item list-group-item-light list-group-item-action <?php echo $thisarray["p0"]=="changes"?"active":"";?>"><i class="mdi mdi-swap-horizontal"></i>&nbsp;Changes</a>
    <a href="/projects" class="waves-effect waves-light list-group-item list-group-item-light list-group-item-action <?php echo $thisarray["p0"]=="projects"?"active":"";?>"><i class="mdi mdi-file-tree"></i>&nbsp;Projects</a>
    <a href="/kanban" class="waves-effect waves-light list-group-item list-group-item-light list-group-item-action <?php echo $thisarray["p0"]=="kanban"?"active":"";?>"><i class="mdi mdi-trello"></i>&nbsp;Kanban board</a>
    <a href="/calendar" class="waves-effect waves-light list-group-item list-group-item-light list-group-item-action <?php echo $thisarray["p0"]=="calendar"?"active":"";?>"><i class="mdi mdi-calendar-clock-outline"></i>&nbsp;Time Management</a>
    <a href="/cpinfo" class="waves-effect waves-light list-group-item list-group-item-light list-group-item-action <?php echo $thisarray["p0"]=="cpinfo"?"active":"";?>"><i class="mdi mdi-file-document-edit-outline"></i>&nbsp;Documentation</a>
    <a href="/appconfig" class="waves-effect waves-light list-group-item list-group-item-light list-group-item-action <?php echo $page=="appconfig"?"active":"";?>"><i class="mdi mdi-cogs"></i>&nbsp;Midleo Configuration</a>
</div>
</div>