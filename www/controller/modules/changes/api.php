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
                default:echo json_encode(array('error' => true, 'type' => "error", 'errorlog' => "please use the API correctly."));exit;
            }
        } else {echo json_encode(array('error' => true, 'type' => "error", 'errorlog' => "please use the API correctly."));exit;}
    }
    public static function getList()
    {
        global $jobstatus;
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
                $data['statusn'] = $jobstatus[$val['chgstatus']]["name"];
                $data['statusbut'] = $jobstatus[$val['chgstatus']]["statcolor"];
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
        global $jobstatus;
        global $priorityarr;
        global $projcodes;
        session_start();
        $pdo = pdodb::connect();
        $data = json_decode(file_get_contents("php://input"));
        if(!empty($data->chgid)){ 
            $sql="select taskcurr from changes where chgnum=?";
            $q = $pdo->prepare($sql);
            $q->execute(array(htmlspecialchars($data->chgid)));
            if ($zobj = $q->fetch(PDO::FETCH_ASSOC)) {
                $taskcur=$zobj["taskcurr"];
            } else {
                $taskcur="0";
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
                if($val['nestid']==$taskcur){
                    $data['taskbutname'] = $val['taskstatus']=="0"?"Start":"Finish";
                    $data['taskbutshow'] = true;
                } else {
                    $data['taskbutshow'] = false;
                    $data['taskbutname'] = "";
                }
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
}
