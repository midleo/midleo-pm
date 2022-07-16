<?php
class ClassMPM_chgapi
{
    public static function getPage($thisarray)
    {
        global $website;
        global $maindir;
        session_start();
        $err = array();
        $msg = array();
        if (!empty($thisarray["p1"]) && !empty($_SESSION['user'])) {
            switch ($thisarray["p1"]) {
                case 'list':ClassMPM_chgapi::getList();
                    break;
                case 'tasks':ClassMPM_chgapi::getTasks();
                    break;
                case 'updtasks':ClassMPM_chgapi::UpdateTasks();
                    break;
                case 'taskdo': ClassMPM_chgapi::doTasks();
                    break; 
                case 'addtask': ClassMPM_chgapi::createTask();
                    break; 
                default:echo json_encode(array('error' => true, 'type' => "error", 'errorlog' => "please use the API correctly."));exit;
            }
        } else {echo json_encode(array('error' => true, 'type' => "error", 'errorlog' => "please use the API correctly."));exit;}
    }
    public static function getList()
    {
        global $projcodes;
        global $priorityarr;
        $pdo = pdodb::connect();
        $data = json_decode(file_get_contents("php://input"));
        $sql = "select * from changes order by id desc";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute()) {
            $zobj = $stmt->fetchAll();
            $data = array();
            foreach ($zobj as $val) {
                $data['name'] = $val['chgname'];
                $data['chgnum'] = $val['chgnum'];
                $data['owner'] = $val['owner'];
                $data['deadline'] = date("d.m.y", strtotime($val['deadline']));
                $data['created'] = date("d.m.y", strtotime($val['created']));
                $data['statusn'] = $projcodes[$val['chgstatus']]["name"];
                $data['statusbut'] = $projcodes[$val['chgstatus']]["badge"];
                $data['priority'] = $priorityarr[$val['priority']];
                $newdata[] = $data;
            }
        }

        pdodb::disconnect();
        echo json_encode($newdata, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        gTable::closeAll();
        exit;
    }
    public static function getTasks(){
        global $priorityarr;
        global $projcodes;
        $pdo = pdodb::connect();
        $data = json_decode(file_get_contents("php://input"));
        if(!empty($data->chgid)){ 
            $sql="select taskcurr,chgstatus from changes where chgnum=?";
            $q = $pdo->prepare($sql);
            $q->execute(array(htmlspecialchars($data->chgid)));
            if ($zobj = $q->fetch(PDO::FETCH_ASSOC)) {
                $taskcur=$zobj["taskcurr"];
                $chgstatus=$zobj["chgstatus"];
            } else {
                $taskcur="0";
                $chgstatus="0";
            }
            if(!empty($_SESSION["userdata"]["ugrarr"])){
                $ugrarr=$_SESSION["userdata"]["ugrarr"];
            } else {
                $ugrarr=array();
            }
            $sql="select * from changes_tasks where chgnum=? order by nestid";            
            $q = $pdo->prepare($sql);
            $q->execute(array(htmlspecialchars($data->chgid)));
            $zobj = $q->fetchAll();
            $data = array();
            $data['maxnestid'] = 0;
            foreach ($zobj as $val) {
                $data['id'] = $val['id'];
                $data['owner'] = $val['owner'];
                $data['appid'] = $val['appid'];
                $data['groupid'] = $val['groupid'];
                $data['taskstatus'] = $val['taskstatus'];
                $data['taskstatusname'] = $projcodes[$val['taskstatus']]["name"];
                $data['taskstatusbut'] = $projcodes[$val['taskstatus']]["badge"];
                $data['taskname'] = $val['taskname'];
                $data['taskinfo'] = $val['taskinfo'];
                $data['nestid'] = $val['nestid'];
                $data['maxnestid'] = $data['maxnestid']<$val['nestid']?$val['nestid']:$data['maxnestid'];
                if($val['nestid']==$taskcur){
                    $data['taskbutname'] = $val['taskstatus']=="0"?"Start":($val['taskstatus']=="3"?"Finish":"");
                    $data['taskbutshow'] = ($val['taskstatus']=="0" || $val['taskstatus']=="3")?true:false;
                } else {
                    $data['taskbutshow'] = false;
                    $data['taskbutname'] = "";
                }
                $data['taskdel']=$chgstatus==0?true:false;
                $data['taskfinished']=$val['nestid']<$taskcur?"taskfin":"";
                $data['hasacc'] = in_array($val['groupid'], $ugrarr)?true:false;
                $newdata[] = $data;
            }
        } else {
           $newdata=array();
        }
        pdodb::disconnect();
        echo json_encode($newdata, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        gTable::closeAll();
        exit;
    }
    public static function doTasks(){
        $data = json_decode(file_get_contents("php://input"));
        if(!empty($data->thisid)){ 
            $pdo = pdodb::connect();
            $now = date('Y-m-d H:i:s');
            if($data->case=="delete"){
                $sql="delete from changes_tasks where id=?";
                $q = $pdo->prepare($sql);
                $q->execute(array(htmlspecialchars($data->thisid)));
                $sql="update changes set taskall=taskall-1 where chgnum=?";
                $q = $pdo->prepare($sql);
                $q->execute(array(htmlspecialchars($data->chg)));
            }
            if($data->case=="start"){
                $sql="update changes_tasks set started='".$now."',taskstatus='3' where id=?";
                $q = $pdo->prepare($sql);
                $q->execute(array(htmlspecialchars($data->thisid))); 
                $sql="update changes set taskcurr=? where chgnum=?";
                $q = $pdo->prepare($sql);
                $q->execute(array(htmlspecialchars($data->taskid),htmlspecialchars($data->chg)));
            }
            if($data->case=="finish"){
                $sql="update changes_tasks set finished='".$now."',taskstatus='4' where id=?";
                $q = $pdo->prepare($sql);
                $q->execute(array(htmlspecialchars($data->thisid)));
                $sql="update changes set taskcurr=taskcurr+1 where chgnum=?";
                $q = $pdo->prepare($sql);
                $q->execute(array(htmlspecialchars($data->chg)));
            }
            gTable::track($_SESSION["userdata"]["usname"], $_SESSION['user'], array("appid" => "system"), $data->case." task in change <a href='/changes'><b>" . htmlspecialchars($data->chg) . "</b></a>");
            pdodb::disconnect();
            gTable::closeAll();
        }
        exit;
    }
    public static function UpdateTasks(){
        $data = json_decode(file_get_contents("php://input"));
        if(!empty($data->chgid)){ 
            $pdo = pdodb::connect();
            $pdo->beginTransaction();
            foreach(json_decode($data->object,true) as $key=>$val){
                $sql="update changes_tasks set nestid=? where id=? and chgnum=?";
                $q = $pdo->prepare($sql);
                $q->execute(array($key,$val,htmlspecialchars($data->chgid)));
            }
            $pdo->commit();
            pdodb::disconnect();
            gTable::closeAll();
        }
        exit;
    }
    public static function createTask(){
        $data = json_decode(file_get_contents("php://input"));
        if(!empty($data->chgid)){ 
            $pdo = pdodb::connect();
            $sql="insert into changes_tasks (nestid,chgnum,owner,appid,groupid,taskname,taskinfo,email) values (?,?,?,?,?,?,?,?)";
            $q = $pdo->prepare($sql);
            $q->execute(array(
                htmlspecialchars($data->task->nestid),
                htmlspecialchars($data->chgid),
                htmlspecialchars($data->task->owner),
                htmlspecialchars($data->task->appid),
                htmlspecialchars($data->task->groupid),
                htmlspecialchars($data->task->taskname),
                htmlspecialchars($data->task->taskinfo),
                htmlspecialchars($data->task->email)
            )); 
            $sql="update changes set taskall=taskall+1 where chgnum=?";
            $q = $pdo->prepare($sql);
            $q->execute(array(htmlspecialchars($data->chgid)));
            pdodb::disconnect();
            gTable::closeAll();
        }
    }
}
