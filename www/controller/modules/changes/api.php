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
                case 'taskdo': ClassMPM_chgapi::doTasks();
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
        session_start();
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
            $sql="select * from changes_tasks where chgnum=?";            
            $q = $pdo->prepare($sql);
            $q->execute(array(htmlspecialchars($data->chgid)));
            $zobj = $q->fetchAll();
            $data = array();
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
        session_start();
        $data = json_decode(file_get_contents("php://input"));
        if(!empty($data->taskid)){ 
            $pdo = pdodb::connect();
            $now = date('Y-m-d H:i:s');
            if($data->case=="delete"){
                $sql="delete from changes_tasks where nestid=?";
                $q = $pdo->prepare($sql);
                $q->execute(array(htmlspecialchars($data->taskid)));
                $sql="update changes set taskall=taskall-1 where chgnum=?";
                $q = $pdo->prepare($sql);
                $q->execute(array(htmlspecialchars($data->chg)));
            }
            if($data->case=="start"){
                $sql="update changes_tasks set started='".$now."',taskstatus='3' where nestid=?";
                $q = $pdo->prepare($sql);
                $q->execute(array(htmlspecialchars($data->taskid)));
                $sql="update changes set taskcurr=? where chgnum=?";
                $q = $pdo->prepare($sql);
                $q->execute(array(htmlspecialchars($data->taskid),htmlspecialchars($data->chg)));
            }
            if($data->case=="finish"){
                $sql="update changes_tasks set finished='".$now."',taskstatus='4' where nestid=?";
                $q = $pdo->prepare($sql);
                $q->execute(array(htmlspecialchars($data->taskid)));
                $sql="update changes set taskcurr=taskcurr+1 where chgnum=?";
                $q = $pdo->prepare($sql);
                $q->execute(array(htmlspecialchars($data->chg)));
            }
            pdodb::disconnect();
            gTable::closeAll();
        }
        exit;
    }
}
