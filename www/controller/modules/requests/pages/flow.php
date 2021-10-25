<?php if (!empty($thisarray["p2"])) {
  $sql="select * from requests where sname=?";
  $q = $pdo->prepare($sql);
  $q->execute(array($thisarray["p2"]));
  if($zobj = $q->fetch(PDO::FETCH_ASSOC)){ ?>
<div class="card">
  <div class="card-header">
    <h2>List of created Message flows/sets for request <b><?php echo $zobj['reqname'];?></b></h2>
  </div>
  <div class="card-body">
    <div id="ngApp" ng-app="ngApp" ng-controller="ngCtrl"><div class="row">
      <div class="col-md-8">
          <input type="text" ng-model="search" class="form-control  topsearch" placeholder="Search message flow...">
      </div>
      </div><br><br>
      <div class="row"><div class="col-md-12">
        <table class="table table-striped table-vmiddle table-hover stylish-table mb-0">
          <thead>
            <tr>
              <th class="text-center">Name</th>
              <th class="text-center">Changed</th>
              <th class="text-center" style="width:120px;">Commands</th>
            </tr>
          </thead>
          <tbody ng-init="getAllflows('<?php echo $thisarray["p2"];?>')">
            <tr dir-paginate="d in names | filter:search | orderBy:'name':reverse | itemsPerPage:10" pagination-id="prodx">
              <td class="text-center"><a href="/flows/{{ d.flowid }}" target="_parent">{{ d.flowname | limitTo:2*textlimit }}{{d.name.length > 2*textlimit ? '...' : ''}}</a></td>
              <td class="text-center">{{ d.modified }}</td>
              <td class="text-center">
                <a href="/requests/flow/<?php echo $thisarray["p2"];?>/{{ d.flowid }}/log" target="_parent" class="btn btn-inverse waves-effect">Log</a>
                <?php if($_SESSION['user_level']=="5" || $_SESSION['user_level']=="3"){?><a ng-click="deleteflows('<?php echo $thisarray["p2"];?>',d.flowid,'<?php echo $_SESSION['user'];?>')" class="btn btn-danger bg waves-effect"><svg class="midico midico-outline"><use href="/assets/images/icon/midleoicons.svg#i-x" xlink:href="/assets/images/icon/midleoicons.svg#i-x" /></svg></a><?php } ?>
              </td>
            </tr>
          </tbody>
        </table>
        <dir-pagination-controls pagination-id="prodx" boundary-links="true" on-page-change="pageChangeHandler(newPageNumber)" template-url="/assets/templ/pagination.tpl.html"></dir-pagination-controls>
        <div class="modal fade" id="modal-flow-form" tabindex="-1" role="dialog" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <form name="form">
                <div class="modal-body form-horizontal">
                  <div class="form-group">
                    <label class="col-md-2 control-label" data-trigger="hover" data-bs-toggle="popover" data-bs-placement="right" data-html="true" data-content="Please use unique names. The name is limited to 256 chars/numbers" title="" data-original-title="Name of the message flow/set" ng-class="{'has-error':!flow.name}">Name</label>
                    <div class="col-md-10"><div class="fg-line"><input ng-required="true" ng-maxlength="256" ng-model="flow.name" type="text" class="form-control"></div></div>
                  </div>
                  <div class="form-group">
                    <label class="col-md-2 control-label">Info</label>
                    <div class="col-md-10"><div class="fg-line"><textarea ng-model="flow.info" rows="2" class="form-control"></textarea></div></div>
                  </div>
                </div>
                <div class="modal-footer">
                  <a id="btn-create-obj" class="waves-effect waves-light btn btn-primary" ng-click="form.$valid && createflow('<?php echo $thisarray["p2"];?>','<?php echo $_SESSION['user'];?>')"><svg class="midico midico-outline"><use href="/assets/images/icon/midleoicons.svg#i-check" xlink:href="/assets/images/icon/midleoicons.svg#i-check"/></svg>&nbsp;Create</a>
                  <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>
              </form>
            </div>
          </div>
        </div>
        <?php if($_SESSION['user_level']=="5" || $_SESSION['user_level']=="3"){?>
        <div style="z-index:9999;position:fixed;bottom:45px; right:24px;">
          <a data-bs-toggle="modal" class="waves-effect waves-light btn btn-success btn-icon btnnm" href="#modal-flow-form" ng-click="showCreateFormflow()"><i class="mdi mdi-plus-thick mdi-24px"></i></a>
        </div>
        <?php } ?>
        </div>
      </div>
    </div>
  </div>
</div>  
<?php } else {?>
<div class="alert alert-warning text-center">No such request!<br><br><a href="/requests" class="btn btn-primary" target="_parent">Return to requests</a></div>
<?php }} else { textClass::PageNotFound();  } ?>
