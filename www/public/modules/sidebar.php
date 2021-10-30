<br>
<h4><i class="mdi mdi-gesture-double-tap"></i>&nbsp;Navigation</h4>
<br>
<nav class="sidebar-nav ">
    <ul id="sidebarnav">
    <li class="row">
            <a class="waves-effect waves-dark col-md-12 <?php echo $page=="dashboard"?"active":"";?>" href="/cp/?"><i class="mdi mdi-view-dashboard-outline"></i>&nbsp;Dashboard</a>
        </li>
        <li class="row">
            <a class="waves-effect waves-dark col-md-12 <?php echo $thisarray["p0"]=="tickets"?"active":"";?>" href="/tickets"><i class="mdi mdi-format-list-checks"></i>&nbsp;Tickets</a>
        </li>
        <li class="row">
            <a class="waves-effect waves-dark col-md-12 <?php echo $thisarray["p0"]=="smanagement"?"active":"";?>" href="/smanagement"><i class="mdi mdi-cog-transfer-outline"></i>&nbsp;Service Management</a>
        </li>
        <li class="row">
            <a class="waves-effect waves-dark col-md-12 <?php echo $thisarray["p0"]=="kanban"?"active":"";?>" href="/kanban"><i class="mdi mdi-bulletin-board"></i>&nbsp;Kanban board</a>
        </li>
        <li class="row">
            <a class="waves-effect waves-dark col-md-12 <?php echo $thisarray["p0"]=="calendar"?"active":"";?>" href="/calendar"><i class="mdi mdi-calendar-clock-outline"></i>&nbsp;Time Management</a>
        </li>
        <li class="row">
            <a class="waves-effect waves-dark col-md-12 <?php echo $thisarray["p0"]=="cpinfo"?"active":"";?>" href="/cpinfo"><i class="mdi mdi-file-document-edit-outline"></i>&nbsp;Documentation</a>
        </li>
    </ul>
</nav>