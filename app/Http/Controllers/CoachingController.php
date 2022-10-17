<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\User;
use App\LinkingMaster;
use App\QuickLink;
use App\CementingExpectations;
use App\AccountabilitySession;
use App\SkillsDevelopment;
use App\SkillBuilding;
use App\GoalSetting;
use App\Mail\QuickLinkNotification;
use App\Mail\CEMailNotification;
use App\Mail\SDAMailNotification;
use App\Mail\PendingLIMailNotification;
use App\Mail\ACCMailNotification;
use App\Mail\SkillBuildingNotification;

/*
 * http://gtk.php.net/
    $2y$10$B0Apm5lKLQznyK2f3YwT2uaNd1TFemdeA9ky6/67nTzvnOAdx0pZ6
 */
class CoachingController extends Controller{
    
    private $active_user;
    
    public function sendNotify(){
        $list = [];
        
        if($this->isManagement()):
            $sql = DB::select("
                SELECT 
                    lm.lnk_date,
                    lm.lnk_linker,
                    (SELECT 
                            CONCAT(e.first_name, ' ', e.last_name)
                        FROM
                            employee_info AS e
                        WHERE
                            lm.lnk_linker = e.id
                        LIMIT 1) AS linker,
                    (SELECT 
                            CONCAT(e.email)
                        FROM
                            employee_info AS e
                        WHERE
                            lm.lnk_linker = e.id
                        LIMIT 1) AS linker_email,
                    lm.lnk_linkee,
                    (SELECT 
                            CONCAT(e.first_name, ' ', e.last_name)
                        FROM
                            employee_info AS e
                        WHERE
                            lm.lnk_linkee = e.id
                        LIMIT 1) AS linkee,
                    (SELECT 
                            CONCAT(e.email)
                        FROM
                            employee_info AS e
                        WHERE
                            lm.lnk_linkee = e.id
                        LIMIT 1) AS linkee_email,
                    ln.lt_desc AS link_type
                FROM
                    linking_master AS lm
                        LEFT JOIN
                    linking_types AS ln ON ln.lt_id = lm.lnk_type
                WHERE
                    lm.lnk_acknw = 0
            ");
            foreach($sql as $res):
                $obj =[
                    "linker"        => $res->linker,
                    "linker_email"  => $res->linker_email,
                    "linkee"        => $res->linkee,
                    "linkee_email"  => $res->linkee_email,
                    "link_type"     => $res->link_type,
                    "lnk_date"      => $res->lnk_date
                ];
                $status = Mail::to([$obj['linker_email'],$obj['linkee_email']])->queue(new PendingLIMailNotification($obj));
                array_push($list,["status" => $status, "Object" => $obj]);
            endforeach;
        endif;
        
        return $list;
    }
    
    public function listCEs(){
        $_id = $this->getActiveUser();
        
        $sub_q = $this->isManagement() ? " lm.lnk_linker " : " lm.lnk_linkee ";
        
        $obj = DB::select("
            SELECT 
                lm.lnk_id,
                se.se_com_id,
                lm.lnk_date,
                lm.lnk_linker,
                (SELECT 
                        CONCAT(ei.first_name, ' ', ei.last_name)
                    FROM
                        employee_info AS ei
                    WHERE
                        ei.id = lm.lnk_linker) AS lnk_linker_name,
                (SELECT 
                        ei.email
                    FROM
                        employee_info AS ei
                    WHERE
                        ei.id = lm.lnk_linker) AS lnk_linker_email,
                lm.lnk_type,
                (SELECT 
                        lt.lt_desc
                    FROM
                        linking_types AS lt
                    WHERE
                        lt.lt_id = lm.lnk_type) AS lnk_type_desc,
                lm.lnk_linkee,
                (SELECT 
                        CONCAT(ei.first_name, ' ', ei.last_name)
                    FROM
                        employee_info AS ei
                    WHERE
                        ei.id = lm.lnk_linkee) AS lnk_linkee_name,
                se.se_focus,
                (SELECT 
                        fc_desc
                    FROM
                        lnk_focus
                    WHERE
                        fc_id = se.se_focus
                    LIMIT 1) AS focus_desc
            FROM
                setting_expectations AS se
                    LEFT JOIN
                linking_master AS lm ON lm.lnk_id = se.se_link_id
            WHERE
                lm.lnk_acknw = 1
                    AND $sub_q = $_id;
        ");
        return view("coaching.ce_list")->with("linking",$obj)->with("management",$this->isManagement());
    }
    
    public function thisLink(){
        if($this->isManagement()):
            $the_id = $this->getActiveUser();
            $_obj = DB::select("
                SELECT 
                   lm.lnk_id,
                   lm.lnk_date,
                   lm.lnk_linker,
                   (SELECT 
                           CONCAT(ei.first_name, ' ', ei.last_name)
                       FROM
                           employee_info AS ei
                       WHERE
                           ei.id = lm.lnk_linker
                       LIMIT 1) AS lnk_linker_name,
                   lm.lnk_linkee,
                   lm.lnk_type,
                   lt.lt_desc AS link_type_desc,
                   (CASE
                       WHEN
                           lm.lnk_type = 1
                       THEN
                           (SELECT 
                                   lf.fc_desc
                               FROM
                                   quick_link AS ql
                                       LEFT JOIN
                                   lnk_focus AS lf ON lf.fc_id = ql.rf_focus
                               WHERE
                                   ql.rf_lnk_id = lm.lnk_id
                               LIMIT 1)
                       WHEN
                           lm.lnk_type = 2
                       THEN
                           (SELECT 
                                   lf.fc_desc
                               FROM
                                   setting_expectations AS se
                                       LEFT JOIN
                                   lnk_focus AS lf ON lf.fc_id = se.se_focus
                               WHERE
                                   se.se_link_id = lm.lnk_id
                               LIMIT 1)
                       WHEN
                           lm.lnk_type = 3
                       THEN
                           (SELECT 
                                   lf.fc_desc
                               FROM
                                   accblty_conv AS av
                                       LEFT JOIN
                                   lnk_focus AS lf ON av.ac_focus = lf.fc_id
                               WHERE
                                   av.ac_link_id = lm.lnk_id
                               LIMIT 1)
                       WHEN
                           lm.lnk_type = 4
                       THEN
                           (SELECT 
                                   CASE
                                           WHEN sda.sda_type = 1 THEN 'Call Listening Session'
                                           WHEN sda.sda_type = 2 THEN 'Mock Calls'
                                           ELSE 'Calibration Services'
                                       END
                               FROM
                                   sda
                               WHERE
                                   sda.sda_lnk_id = lm.lnk_id
                               LIMIT 1)
                       WHEN lm.lnk_type = 5 THEN 'N/A'
                       WHEN
                           lm.lnk_type = 6
                       THEN
                           (SELECT 
                                   lf.fc_desc
                               FROM
                                   skill_building AS sb
                                       LEFT JOIN
                                   lnk_focus AS lf ON sb.sb_focus = lf.fc_id
                               WHERE
                                   sb.sb_link_id = lm.lnk_id
                               LIMIT 1)
                       WHEN
                           lm.lnk_type = 7
                       THEN
                           (SELECT 
                                   gs.gs_com
                               FROM
                                   goal_setting AS gs
                               WHERE
                                   gs.gs_link_id = lm.lnk_id
                               LIMIT 1)
                   END) AS focus,
                   (CASE
                       WHEN
                           lm.lnk_type = 1
                       THEN
                           CONCAT('<a href=\'/quick-link/',
                                   lm.lnk_id,
                                   '\' class=\'btn btn-primary\'>VIEW QUICK LINK</a>')
                       WHEN
                           lm.lnk_type = 2
                       THEN
                           (SELECT 
                                   CONCAT('<a href=\'/ce-expectation/',
                                               se.se_com_id,
                                               '\' class=\'btn btn-primary\'>VIEW CE</a>')
                               FROM
                                   setting_expectations AS se
                               WHERE
                                   se.se_link_id = lm.lnk_id
                               LIMIT 1)
                       WHEN
                           lm.lnk_type = 3
                       THEN
                           (SELECT 
                                   CONCAT('<a href=\'/acc-set/',
                                               ac_com_id,
                                               '\' class=\'btn btn-primary\'>VIEW Accountability Setting</a>')
                               FROM
                                   accblty_conv
                               WHERE
                                   ac_link_id = lm.lnk_id
                               LIMIT 1)
                       WHEN
                           lm.lnk_type = 4
                       THEN
                           (SELECT 
                                   CONCAT('<a href=\'/skill-dev-act/',
                                               sda.sda_com_id,
                                               '\' class=\'btn btn-primary\'>VIEW SDA</a>')
                               FROM
                                   sda
                               WHERE
                                   sda.sda_lnk_id = lm.lnk_id
                               LIMIT 1)
                       WHEN
                           lm.lnk_type = 5
                       THEN
                           (SELECT 
                                   CONCAT('<a href=\'/gtky/',
                                               gtky.gtk_com_num,
                                               '\' class=\'btn btn-primary\'>VIEW GTKY</a>')
                               FROM
                                   gtky
                               WHERE
                                   gtky.gtk_link_id = lm.lnk_id
                               LIMIT 1)
                       WHEN
                           lm.lnk_type = 6
                       THEN
                           (SELECT 
                                   CONCAT('<a href=\'/skill-building/',
                                               sb.sb_com_num,
                                               '\' class=\'btn btn-primary\'>VIEW SKILL BUILDING</a>')
                               FROM
                                   skill_building AS sb
                               WHERE
                                   sb.sb_link_id = lm.lnk_id
                               LIMIT 1)
                       WHEN
                           lm.lnk_type = 7
                       THEN
                           (SELECT 
                                   CONCAT('<a href=\'/goal-setting/',
                                               gs.gs_com_id,
                                               '\' class=\'btn btn-primary\'>VIEW GOAL SETTING</a>')
                               FROM
                                   goal_setting AS gs
                               WHERE
                                   gs.gs_link_id = lm.lnk_id
                               LIMIT 1)
                   END) AS link_button,
                   lm.lnk_acknw
               FROM
                   linking_master AS lm
                       LEFT JOIN
                   linking_types AS lt ON lt.lt_id = lm.lnk_type
               WHERE
                   lm.lnk_linkee = $the_id
                       AND lm.lnk_status = 1
               ORDER BY lm.lnk_id DESC;
            ");
            return view("coaching.own_linking")->with("my_links",$_obj)->with("management",$this->isManagement());
        else:
            return "No access for thisLink()";
        endif;
    }
    
    public function listGTKYs(){
        if($this->isManagement()){
            $active_user = $this->getActiveUser();
            $obj = DB::select(" 
                SELECT
                    lm.lnk_date,
                    lm.lnk_id,
                    lm.lnk_type,
                    gtky.gtk_com_num,
                    lm.lnk_linker,
                    (SELECT 
                            CONCAT(ei.first_name, ' ', ei.last_name)
                        FROM
                            employee_info AS ei
                        WHERE
                            ei.id = lm.lnk_linker
                        LIMIT 1) AS linker_name,
                    lm.lnk_linkee,
                    (SELECT 
                            CONCAT(ei.first_name, ' ', ei.last_name)
                        FROM
                            employee_info AS ei
                        WHERE
                            ei.id = lm.lnk_linkee
                        LIMIT 1) AS linkee_name
                FROM
                    linking_master AS lm
                        LEFT JOIN
                    gtky ON gtky.gtk_link_id = lm.lnk_id
                WHERE
                    lm.lnk_type = 5 AND lm.lnk_linker = $active_user;"
            );
            return view("coaching.gtky_list")->with("gtky",$obj)->with("management",1);
        }else
            return "You have no access for this feature";
    }
    
    public function listGSs(){
        $active_user = $this->getActiveUser();
        $sql = $this->isManagement() ? " and lm.lnk_linker = $active_user " : " and lm.lnk_linkee = $active_user ";
        $obj = DB::select("
            SELECT 
                lm.lnk_date,
                lm.lnk_linker,
                gs.gs_com_id,
                (SELECT 
                        CONCAT(ei.first_name, ' ', ei.last_name)
                    FROM
                        employee_info AS ei
                    WHERE
                        ei.id = lm.lnk_linker
                    LIMIT 1) AS linker_name,
                lm.lnk_linkee,
                (SELECT 
                        CONCAT(ei.first_name, ' ', ei.last_name)
                    FROM
                        employee_info AS ei
                    WHERE
                        ei.id = lm.lnk_linkee
                    LIMIT 1) AS linkee_name,
                'Goal Setting (GS)' AS lnk_type,
                gs.gs_com as focus
            FROM
                goal_setting AS gs
                    LEFT JOIN
                linking_master AS lm ON lm.lnk_id = gs.gs_link_id
            WHERE
                lm.lnk_acknw = 1 AND lm.lnk_status = 1 $sql;
        ");
         return view("coaching.gs_list")->with("linking",$obj)->with("management",$this->isManagement());
    }
    
    public function listSBs(){
        $active_user = $this->getActiveUser();
        $sql = $this->isManagement() ? " and lm.lnk_linker = $active_user " : " and lm.lnk_linkee = $active_user ";
        $obj = DB::select("
            SELECT 
                lm.lnk_date,
                lm.lnk_linker,
                av.sb_com_num,
                (SELECT 
                        CONCAT(ei.first_name, ' ', ei.last_name)
                    FROM
                        employee_info AS ei
                    WHERE
                        ei.id = lm.lnk_linker
                    LIMIT 1) AS linker_name,
                lm.lnk_linkee,
                (SELECT 
                        CONCAT(ei.first_name, ' ', ei.last_name)
                    FROM
                        employee_info AS ei
                    WHERE
                        ei.id = lm.lnk_linkee
                    LIMIT 1) AS linkee_name,
                'Skill Building (SB)' AS lnk_type,
                (SELECT 
                        fc_desc
                    FROM
                        lnk_focus
                    WHERE
                        fc_id = av.sb_focus
                    LIMIT 1) AS ac_focus
            FROM
                skill_building AS av
                    LEFT JOIN
                linking_master AS lm ON lm.lnk_id = av.sb_link_id
            WHERE
                lm.lnk_acknw = 1 AND lm.lnk_status = 1 $sql;
        ");
         return view("coaching.sb_list")->with("linking",$obj)->with("management",$this->isManagement());
    }
    
    public function listACCs(){
        $active_user = $this->getActiveUser();
        $sql = $this->isManagement() ? " and lm.lnk_linker = $active_user " : " and lm.lnk_linkee = $active_user ";
        $obj = DB::select("
            SELECT 
                lm.lnk_date,
                lm.lnk_linker,
                av.ac_com_id,
                (SELECT 
                        CONCAT(ei.first_name, ' ', ei.last_name)
                    FROM
                        employee_info AS ei
                    WHERE
                        ei.id = lm.lnk_linker
                    LIMIT 1) AS linker_name,
                lm.lnk_linkee,
                (SELECT 
                        CONCAT(ei.first_name, ' ', ei.last_name)
                    FROM
                        employee_info AS ei
                    WHERE
                        ei.id = lm.lnk_linkee
                    LIMIT 1) AS linkee_name,
                'Accountability Setting' AS lnk_type,
                (SELECT 
                        fc_desc
                    FROM
                        lnk_focus
                    WHERE
                        fc_id = av.ac_focus
                    LIMIT 1) AS ac_focus
            FROM
                accblty_conv AS av
                    LEFT JOIN
                linking_master AS lm ON lm.lnk_id = av.ac_link_id
            WHERE
                lm.lnk_acknw = 1 $sql;
        ");
        
        return view("coaching.acc_list")->with("linking",$obj)->with("management",$this->isManagement());
    }
    
    public function listSDAs(){
        $main_id = $this->getActiveUser();
        $flag = 0;
        
        if($this->isManagement()):
            $flag = 1;
            $sql = " lm.lnk_linker = $main_id";
            $conn = " lm.lnk_linkee";
            $label = "Linkee";
        else:
            $sql = " lm.lnk_linkee = $main_id";
            $conn = " lm.lnk_linker";
            $label = "Linker";
        endif;
        
        $obj = DB::select("
            SELECT 
                lm.lnk_id,
                lm.lnk_date,
                sda.sda_com_id,
                sda.sda_date_call,
                sda.sda_call_sel,
                sda.sda_www_u_said,
                sda.sda_wcm_i_said,
                sda.sda_wcm_u_said,
                sda.sda_wcm_i_said,
                sda.sda_comments,
                sda.sda_feedback,
                'Skill Development Activity' AS link_type,
                (SELECT 
                        CONCAT(ei.first_name, ' ', ei.last_name)
                    FROM
                        employee_info AS ei
                    WHERE
                        ei.id = $conn
                    LIMIT 1) AS link_person,
                CASE
                    WHEN sda.sda_type = 1 THEN 'Call Listening Session'
                    WHEN sda.sda_type = 2 THEN 'Mock Calls'
                    ELSE 'Calibration Services'
                END AS focus
            FROM
                linking_master AS lm
                    LEFT JOIN
                sda ON sda.sda_lnk_id = lm.lnk_id
            WHERE
                lm.lnk_type = 4 AND $sql
                    AND lm.lnk_acknw = 1;
        ");
        
        return view("coaching.sda_list")->with("linking",$obj)->with("management",$flag)->with("label",$label);
    }
    
    public function listQLs(){
        $main_id = $this->getActiveUser();
        $flag = 0;
        
        if($this->isManagement()):
            $flag = 1;
            $sql = " lm.lnk_linker = $main_id";
            $conn = " lm.lnk_linkee";
            $label = "Linkee";
        else:
            $sql = " lm.lnk_linkee = $main_id";
            $conn = " lm.lnk_linker";
            $label = "Linker";
        endif;
        
        $obj = DB::select("
            SELECT 
                lm.lnk_id,
                lm.lnk_date,
                lt.lt_desc AS link_type,
                (SELECT 
                        CONCAT(ei.first_name, ' ', ei.last_name)
                    FROM
                        employee_info AS ei
                    WHERE
                        ei.id = $conn
                    LIMIT 1) AS link_person,
                lf.fc_desc AS focus,
                lt.lt_link
            FROM
                linking_master AS lm
                    LEFT JOIN
                linking_types AS lt ON lt.lt_id = lm.lnk_type
                    LEFT JOIN
                quick_link AS ql ON ql.rf_lnk_id = lm.lnk_id
                    LEFT JOIN
                lnk_focus AS lf ON lf.fc_id = ql.rf_focus
            WHERE
                lm.lnk_acknw = 1 AND lm.lnk_status = 1 and lm.lnk_type = 1
                    AND $sql;
        ");
        
        return view("coaching.ql_list")->with("linking",$obj)->with("management",$flag)->with("label",$label);
    }
    
    public function viewACC(Request $req, $id){
        if($req->post("save_ac_linking")){
            $this->processSaving($req);
            return redirect("/coaching-session");
        }
        else
        if($req->post("acknw_ac_linking")){
            AccountabilitySession::where("ac_com_id",$id)
                ->update(["ac_feedback" => $req->post("ac_feedback")]);
            
            $tar_acc = AccountabilitySession::where("ac_com_id",$id)->first();
            LinkingMaster::where("lnk_id",$tar_acc->ac_link_id)
                ->update(["lnk_acknw"=>1]);
            
            return redirect("/coaching-session");
        }
        $focus = DB::select("
            SELECT 
                fc_id, fc_desc
            FROM
                elink_employee_directory.lnk_focus
            WHERE
                fc_status = 1
            ORDER BY fc_id ASC;
        ");
        $main_obj = DB::select("
            SELECT 
                lm.lnk_id,
                av.ac_com_id,
                lm.lnk_date,
                lm.lnk_linker,
                lm.lnk_type,
                lm.lnk_acknw,
                av.ac_focus,
                (SELECT 
                        CONCAT(ei.first_name, ' ', ei.last_name)
                    FROM
                        employee_info AS ei
                    WHERE
                        ei.id = lm.lnk_linker) AS lnk_linker_name,
                (SELECT 
                        ei.email
                    FROM
                        employee_info AS ei
                    WHERE
                        ei.id = lm.lnk_linker) AS lnk_linker_email,
                lm.lnk_linkee,
                (SELECT 
                        CONCAT(ei.first_name, ' ', ei.last_name)
                    FROM
                        employee_info AS ei
                    WHERE
                        ei.id = lm.lnk_linkee) AS lnk_linkee_name,
                (SELECT 
                        ei.email
                    FROM
                        employee_info AS ei
                    WHERE
                        ei.id = lm.lnk_linkee) AS lnk_linkee_email,
                av.ac_skill,
                av.ac_when_use,
                av.ac_why_use,
                av.ac_expectations,
                av.ac_expectation_date,
                av.ac_comments,
                av.ac_feedback,
                av.ac_how_use
            FROM
                accblty_conv AS av
                    LEFT JOIN
                linking_master AS lm ON av.ac_link_id = lm.lnk_id
            WHERE
                av.ac_com_id = '$id'
            LIMIT 1;
        ");
        $exp = $main_obj[0];
        $obj = [
            "lnk_date"              => $exp->lnk_date, 
            "lnk_linker"            => $exp->lnk_linker, 
            "lnk_linker_name"       => $exp->lnk_linker_name, 
            "lnk_linker_email"      => $exp->lnk_linker_email, 
            "lnk_linkee"            => $exp->lnk_linkee,
            "lnk_linkee_name"       => $exp->lnk_linkee_name, 
            "lnk_linkee_email"      => $exp->lnk_linkee_email,
            "lnk_acknw"             => $exp->lnk_acknw,
            "ac_com_id"             => $exp->ac_com_id,
            "lnk_type"              => $exp->lnk_type,
            "ac_focus"              => $exp->ac_focus,
            "ac_skill"              => $exp->ac_skill,
            "ac_when_use"           => $exp->ac_when_use,
            "ac_how_use"            => $exp->ac_how_use,
            "ac_why_use"            => $exp->ac_why_use,
            "ac_expectations"       => $exp->ac_expectations,
            "ac_expectation_date"   => $exp->ac_expectation_date,
            "ac_comments"           => $exp->ac_comments,
            "ac_feedback"           => $exp->ac_feedback,
            "flag"                  => 1,
            "update"                => 1,
            "management"            => $this->isManagement(),
            "sel_focus"             => $focus,
            "ackn_acc"              => $req->post("ackn_acc") ? 1 : 0,
            "view_accnt"            => $exp->lnk_acknw ? 1: 0
        ]; 
        if( $this->isManagement() && $obj['lnk_linker']  != $this->getActiveUser() && $obj['lnk_linkee'] !=  $this->getActiveUser() )
            $obj['view_accnt'] = 1;
        
        if( ($obj['lnk_linkee'] ==  $this->getActiveUser() && $obj['lnk_acknw'] == 0) || $obj['view_accnt'] ){
            return view("coaching.accst_acknw")->with('obj',$obj)->with("management",$this->isManagement());
        }
        
        if($obj['lnk_linker']  == $this->getActiveUser() && $obj['lnk_acknw'] == 0){
            return view("coaching.accst")->with('obj',$obj)->with("management",$this->isManagement());
        }else
            return "Restricted Access";
            
    }
    
    public function viewGTKY(Request $req,$id){
        if($this->isManagement()){
            if($req->post("update")){
                $object = [
                    "lnk_date"          => $req->post("lnk_date"), 
                    "lnk_linker"        => $req->post("lnk_linker"), 
                    "lnk_linker_name"   => $req->post("lnk_linker_name"), 
                    "lnk_linker_email"  => $req->post("lnk_linker_email"), 
                    "lnk_linkee"        => $req->post("lnk_linkee"), 
                    "lnk_linkee_name"   => $req->post("lnk_linkee_name"),
                    "lnk_linkee_email"  => $req->post("lnk_linkee_email"),
                    "lnk_type"          => $req->post("lnk_type"),
                    "gtk_link_id"       => $req->post("gtk_link_id"),
                    "gtk_com_num"       => $req->post("gtk_com_num"),
                    "gtk_address"       => $req->post("gtk_address"),
                    "gtk_bday"          => $req->post("gtk_bday"),
                    "gtk_bplace"        => $req->post("gtk_bplace"),
                    "gtk_mobile"        => $req->post("gtk_mobile"),
                    "gtk_email"         => $req->post("gtk_email"),
                    "gtk_civil_stat"    => $req->post("gtk_civil_stat"),
                    "gtk_fav_thing"     => $req->post("gtk_fav_thing"),
                    "gtk_fav_color"     => $req->post("gtk_fav_color"),
                    "gtk_fav_movie"     => $req->post("gtk_fav_movie"),
                    "gtk_fav_song"      => $req->post("gtk_fav_song"),
                    "gtk_fav_food"      => $req->post("gtk_fav_food"),
                    "gtk_allergic_food" => $req->post("gtk_allergic_food"),
                    "gtk_allergic_med"  => $req->post("gtk_allergic_med"),
                    "gtk_learn_style"   => $req->post("gtk_learn_style"),
                    "gtk_social_style"  => $req->post("gtk_social_style"),
                    "gtk_motivation"    => $req->post("gtk_motivation"),
                    "gtk_how_coached"   => $req->post("gtk_how_coached"),
                    "flag"              => $req->post("save_gtky_session"),
                    "gtk_strength"      => $req->post("gtk_strength"),
                    "gtk_improvement"   => $req->post("gtk_improvement"),
                    "gtk_goals"         => $req->post("gtk_goals"),
                    "gtk_others"        => $req->post("gtk_others"),
                    "update"            => 1
                ];
                if($this->verifyGTKY($req) == 1)
                    $this->processSaving($req);
            }else{
                // Process from Database
                $obj = DB::select("
                    SELECT 
                        *,
                        (SELECT 
                                CONCAT(ei.first_name, ' ', ei.last_name)
                            FROM
                                employee_info AS ei
                            WHERE
                                ei.id = lm.lnk_linker
                            LIMIT 1) AS lnk_linker_name,
                        (SELECT 
                                email
                            FROM
                                employee_info AS ei
                            WHERE
                                ei.id = lm.lnk_linker
                            LIMIT 1) AS lnk_linker_email,
                        (SELECT 
                                CONCAT(ei.first_name, ' ', ei.last_name)
                            FROM
                                employee_info AS ei
                            WHERE
                                ei.id = lm.lnk_linkee
                            LIMIT 1) AS lnk_linkee_name,
                        (SELECT 
                                email
                            FROM
                                employee_info AS ei
                            WHERE
                                ei.id = lm.lnk_linkee
                            LIMIT 1) AS lnk_linkee_email
                    FROM
                        gtky
                            LEFT JOIN
                        linking_master AS lm ON lm.lnk_id = gtky.gtk_link_id
                    WHERE
                        gtky.gtk_com_num = '$id'
                    LIMIT 1;
                ");
                $gtky = $obj[0];
                $object = [
                    "lnk_date"          => $gtky->lnk_date, 
                    "lnk_linker"        => $gtky->lnk_linker, 
                    "lnk_linker_name"   => $gtky->lnk_linker_name, 
                    "lnk_linker_email"  => $gtky->lnk_linker_email, 
                    "lnk_linkee"        => $gtky->lnk_linkee, 
                    "lnk_linkee_name"   => $gtky->lnk_linkee_name,
                    "lnk_linkee_email"  => $gtky->lnk_linkee_email,
                    "lnk_type"          => $gtky->lnk_type,
                    "gtk_link_id"       => $gtky->gtk_link_id,
                    "gtk_com_num"       => $gtky->gtk_com_num,
                    "gtk_address"       => $gtky->gtk_address,
                    "gtk_bday"          => $gtky->gtk_bday,
                    "gtk_bplace"        => $gtky->gtk_bplace,
                    "gtk_mobile"        => $gtky->gtk_mobile,
                    "gtk_email"         => $gtky->gtk_email,
                    "gtk_civil_stat"    => $gtky->gtk_civil_stat,
                    "gtk_fav_thing"     => $gtky->gtk_fav_thing,
                    "gtk_fav_color"     => $gtky->gtk_fav_color,
                    "gtk_fav_movie"     => $gtky->gtk_fav_movie,
                    "gtk_fav_song"      => $gtky->gtk_fav_song,
                    "gtk_fav_food"      => $gtky->gtk_fav_food,
                    "gtk_allergic_food" => $gtky->gtk_allergic_food,
                    "gtk_allergic_med"  => $gtky->gtk_allergic_med,
                    "gtk_learn_style"   => $gtky->gtk_learn_style,
                    "gtk_social_style"  => $gtky->gtk_social_style,
                    "gtk_motivation"    => $gtky->gtk_motivation,
                    "gtk_how_coached"   => $gtky->gtk_how_coached,
                    "flag"              => 1,
                    "gtk_strength"      => $gtky->gtk_strength,
                    "gtk_improvement"   => $gtky->gtk_improvement,
                    "gtk_goals"         => $gtky->gtk_goals,
                    "gtk_others"        => $gtky->gtk_others,
                    "update"            => 1
                ];
            }
            
            return view('coaching.gtky')
                ->with("obj",$object)
                ->with("management",$this->isManagement());
        }else{
	  $result = DB::table('linking_master')->where('lnk_id', $id)->update(['lnk_acknw' => 1]);
            return back();
	}
           // return "You have no access to perform this action";
    }
    
    public function viewGS(Request $req, $id){
        if($req->post("update_goal_setting_session")){
        /* Display Goal Setting from Post Values */
            $obj = [
                "lnk_date"          => $req->post("lnk_date"), 
                "lnk_linker"        => $req->post("lnk_linker"), 
                "lnk_linker_name"   => $req->post("lnk_linker_name"), 
                "lnk_linker_email"  => $req->post("lnk_linker_email"), 
                "lnk_linkee"        => $req->post("lnk_linkee"), 
                "lnk_linkee_name"   => $req->post("lnk_linkee_name"),
                "lnk_linkee_email"  => $req->post("lnk_linkee_email"),
                "lnk_type"          => $req->post("lnk_type"),
                "gs_com_id"         => $req->post("gs_com_id"),
                "gs_accmpl"         => $req->post("gs_accmpl"),
                "gs_metric_01"      => $req->post("gs_metric_01"),
                "gs_metric_02"      => $req->post("gs_metric_02"),
                "gs_metric_03"      => $req->post("gs_metric_03"),
                "gs_metric_04"      => $req->post("gs_metric_04"),
                "gs_metric_05"      => $req->post("gs_metric_05"),
                "gs_metric_06"      => $req->post("gs_metric_06"),
                "gs_metric_07"      => $req->post("gs_metric_07"),
                "gs_target_01"      => $req->post("gs_target_01"),
                "gs_target_02"      => $req->post("gs_target_02"),
                "gs_target_03"      => $req->post("gs_target_03"),
                "gs_target_04"      => $req->post("gs_target_04"),
                "gs_target_05"      => $req->post("gs_target_05"),
                "gs_target_06"      => $req->post("gs_target_06"),
                "gs_target_07"      => $req->post("gs_target_07"),
                "gs_prev_01"        => $req->post("gs_prev_01"),
                "gs_prev_02"        => $req->post("gs_prev_02"),
                "gs_prev_03"        => $req->post("gs_prev_03"),
                "gs_prev_04"        => $req->post("gs_prev_04"),
                "gs_prev_05"        => $req->post("gs_prev_05"),
                "gs_prev_06"        => $req->post("gs_prev_06"),
                "gs_prev_07"        => $req->post("gs_prev_07"),
                "gs_curr_01"        => $req->post("gs_curr_01"),
                "gs_curr_02"        => $req->post("gs_curr_02"),
                "gs_curr_03"        => $req->post("gs_curr_03"),
                "gs_curr_04"        => $req->post("gs_curr_04"),
                "gs_curr_05"        => $req->post("gs_curr_05"),
                "gs_curr_06"        => $req->post("gs_curr_06"),
                "gs_curr_07"        => $req->post("gs_curr_07"),
                "gs_tip"            => $req->post("gs_tip"),
                "gs_com"            => $req->post("gs_com"),
                "update"            => 1,
                "flag"              => $req->post("update_goal_setting_session"),
                "readonly"          => 0,
                "gs_feedback"       => "",
                "acknowledge"       => 0
            ];
            $this->processSaving($req);
        }else
        if($req->post("acknowledge_goal_setting_session")){
            GoalSetting::where("gs_com_id",$req->post("gs_com_id"))->update(["gs_feedback"=> $req->post("gs_feedback")]);
            $gs1 = GoalSetting::where("gs_com_id",$req->post("gs_com_id"))->first();
            LinkingMaster::where("lnk_id",$gs1->gs_link_id)->update(["lnk_acknw"=>1]);
            return redirect('/coaching-session');
        }
        else{
        /* Display goal setting session from the database */
            $main_obj = DB::select("
                SELECT 
                    *,
                    (SELECT 
                            CONCAT(ei.first_name, ' ', ei.last_name)
                        FROM
                            employee_info AS ei
                        WHERE
                            ei.id = lm.lnk_linker
                        LIMIT 1) AS lnk_linker_name,
                    (SELECT 
                            email
                        FROM
                            employee_info AS ei
                        WHERE
                            ei.id = lm.lnk_linker
                        LIMIT 1) AS lnk_linker_email,
                    (SELECT 
                            CONCAT(ei.first_name, ' ', ei.last_name)
                        FROM
                            employee_info AS ei
                        WHERE
                            ei.id = lm.lnk_linkee
                        LIMIT 1) AS lnk_linkee_name,
                    (SELECT 
                            email
                        FROM
                            employee_info AS ei
                        WHERE
                            ei.id = lm.lnk_linkee
                        LIMIT 1) AS lnk_linkee_email
                FROM
                    goal_setting AS gs
                        LEFT JOIN
                    linking_master AS lm ON gs.gs_link_id = lm.lnk_id
                WHERE
                    gs.gs_com_id = '$id'
                LIMIT 1;
            ");
            $gs = $main_obj[0];
            $obj = [
                "lnk_date"          => $gs->lnk_date, 
                "lnk_linker"        => $gs->lnk_linker, 
                "lnk_linker_name"   => $gs->lnk_linker_name, 
                "lnk_linker_email"  => $gs->lnk_linker_email, 
                "lnk_linkee"        => $gs->lnk_linkee, 
                "lnk_linkee_name"   => $gs->lnk_linkee_name,
                "lnk_linkee_email"  => $gs->lnk_linkee_email,
                "lnk_type"          => $gs->lnk_type,
                "gs_com_id"         => $gs->gs_com_id,
                "gs_accmpl"         => $gs->gs_accmpl,
                "gs_metric_01"      => $gs->gs_metric_01,
                "gs_metric_02"      => $gs->gs_metric_02,
                "gs_metric_03"      => $gs->gs_metric_03,
                "gs_metric_04"      => $gs->gs_metric_04,
                "gs_metric_05"      => $gs->gs_metric_05,
                "gs_metric_06"      => $gs->gs_metric_06,
                "gs_metric_07"      => $gs->gs_metric_07,
                "gs_target_01"      => $gs->gs_target_01,
                "gs_target_02"      => $gs->gs_target_02,
                "gs_target_03"      => $gs->gs_target_03,
                "gs_target_04"      => $gs->gs_target_04,
                "gs_target_05"      => $gs->gs_target_05,
                "gs_target_06"      => $gs->gs_target_06,
                "gs_target_07"      => $gs->gs_target_07,
                "gs_prev_01"        => $gs->gs_prev_01,
                "gs_prev_02"        => $gs->gs_prev_02,
                "gs_prev_03"        => $gs->gs_prev_03,
                "gs_prev_04"        => $gs->gs_prev_04,
                "gs_prev_05"        => $gs->gs_prev_05,
                "gs_prev_06"        => $gs->gs_prev_06,
                "gs_prev_07"        => $gs->gs_prev_07,
                "gs_curr_01"        => $gs->gs_curr_01,
                "gs_curr_02"        => $gs->gs_curr_02,
                "gs_curr_03"        => $gs->gs_curr_03,
                "gs_curr_04"        => $gs->gs_curr_04,
                "gs_curr_05"        => $gs->gs_curr_05,
                "gs_curr_06"        => $gs->gs_curr_06,
                "gs_curr_07"        => $gs->gs_curr_07,
                "gs_tip"            => $gs->gs_tip,
                "gs_com"            => $gs->gs_com,
                "update"            => 1,
                "flag"              => 1,
                "readonly"          => $gs->lnk_acknw,
                "gs_feedback"       => $gs->gs_feedback,
                "acknowledge"       => $gs->lnk_linkee == $this->getActiveUser() ? 1 : 0
            ];
        }//end of else part
        return view("coaching.gs_view")                
            ->with("obj",$obj)
            ->with("management",$this->isManagement());
    }
    
    public function viewSDA(Request $req, $id){
        $active_user = $this->getActiveUser();
        
        if($req->post("save_sda_linking")){
            /* Fill Data from $_POST */
            $obj = [
                "lnk_date"          => $req->post('lnk_date'), 
                "lnk_linker"        => $req->post('lnk_linker'), 
                "lnk_linker_name"   => $req->post('lnk_linker_name'), 
                "lnk_linker_email"  => $req->post('lnk_linker_email'), 
                "lnk_linkee"        => $req->post('lnk_linkee'),
                "lnk_linkee_name"   => $req->post('lnk_linkee_name'), 
                "lnk_linkee_email"  => $req->post('lnk_linkee_email'), 
                "lnk_type"          => $req->post('lnk_type'),
                "flag"              => 1,
                "update"            => 1,
                "management"        => $this->isManagement(),
                "sda_com_id"        => $req->post('sda_com_id'),
                "sda_type"          => $req->post('sda_type'),
                "sda_date_call"     => $req->post('sda_date_call'),
                "sda_call_sel"      => $req->post('sda_call_sel'),
                "sda_www_u_said"    => $req->post('sda_www_u_said'),
                "sda_www_i_said"    => $req->post('sda_www_i_said'),
                "sda_wcm_u_said"    => $req->post('sda_wcm_u_said'),
                "sda_wcm_i_said"    => $req->post('sda_wcm_i_said'),
                "sda_comments"      => $req->post('sda_comments')
            ];
            
            $this->processSaving($req);
            return redirect("/skill-dev-act/".$req->post('sda_com_id'));
        }else
        if($req->post("ack_sda_linking")){
            SkillsDevelopment::where("sda_com_id",$req->post("sda_com_id"))
                ->update(["sda_feedback" => $req->post("sda_feedback")]);
            
            $tar_sda = SkillsDevelopment::where("sda_com_id",$req->post("sda_com_id"))->first();
            LinkingMaster::where("lnk_id",$tar_sda->sda_lnk_id)
                ->update(["lnk_acknw"=>1]);
            
            return redirect("/coaching-session/");
        }
        else
        {
             /* Fill Data from Database */
            $main_obj = DB::select("
                SELECT 
                    lm.lnk_id,
                    lm.lnk_date,
                    lm.lnk_linker,
                    lm.lnk_type,
                    lm.lnk_acknw,
                    (SELECT 
                            CONCAT(ei.first_name, ' ', ei.last_name)
                        FROM
                            employee_info AS ei
                        WHERE
                            ei.id = lm.lnk_linker) AS lnk_linker_name,
                    (SELECT 
                            ei.email
                        FROM
                            employee_info AS ei
                        WHERE
                            ei.id = lm.lnk_linker) AS lnk_linker_email,
                    lm.lnk_linkee,
                    (SELECT 
                            CONCAT(ei.first_name, ' ', ei.last_name)
                        FROM
                            employee_info AS ei
                        WHERE
                            ei.id = lm.lnk_linkee) AS lnk_linkee_name,
                    (SELECT 
                            ei.email
                        FROM
                            employee_info AS ei
                        WHERE
                            ei.id = lm.lnk_linkee) AS lnk_linkee_email,
                    sda.sda_com_id,
                    sda.sda_type,
                    sda.sda_date_call,
                    sda.sda_call_sel,
                    sda.sda_www_u_said,
                    sda.sda_www_i_said,
                    sda.sda_wcm_u_said,
                    sda.sda_wcm_i_said,
                    sda.sda_comments,
                    sda.sda_feedback
                FROM
                    sda
                        LEFT JOIN
                    linking_master AS lm ON lm.lnk_id = sda.sda_lnk_id
                WHERE
                    sda.sda_com_id = '$id'
                LIMIT 1;
            ");
            $exp = $main_obj[0];
            $view_only =0;
            
            if( $req->post("view_only") || $exp->lnk_acknw ||  ($active_user != $exp->lnk_linker && $active_user !=  $exp->lnk_linkee) )
                $view_only = 1;
            
            $obj = [
                "lnk_date"          => $exp->lnk_date, 
                "lnk_linker"        => $exp->lnk_linker, 
                "lnk_linker_name"   => $exp->lnk_linker_name, 
                "lnk_linker_email"  => $exp->lnk_linker_email, 
                "lnk_linkee"        => $exp->lnk_linkee,
                "lnk_linkee_name"   => $exp->lnk_linkee_name, 
                "lnk_linkee_email"  => $exp->lnk_linkee_email, 
                "lnk_type"          => $exp->lnk_type,
                "flag"              => 1,
                "update"            => 1,
                "management"        => $this->isManagement(),
                "sda_com_id"        => $exp->sda_com_id,
                "sda_type"          => $exp->sda_type,
                "sda_date_call"     => $exp->sda_date_call,
                "sda_call_sel"      => $exp->sda_call_sel,
                "sda_www_u_said"    => $exp->sda_www_u_said,
                "sda_www_i_said"    => $exp->sda_www_i_said,
                "sda_wcm_u_said"    => $exp->sda_wcm_u_said,
                "sda_wcm_i_said"    => $exp->sda_wcm_i_said,
                "sda_comments"      => $exp->sda_comments,
                "sda_feedback"      => $exp->sda_feedback,
                "view_only"         => $view_only
            ];
            if($active_user == $exp->lnk_linkee || $obj['view_only']):
                return view('coaching.sda_ack')->with("obj",$obj)->with("management",$this->isManagement());
            endif;
        }//end query from database
    
        
        return view('coaching.sda')->with("obj",$obj)->with("management",$this->isManagement());
    }

    public function viewCE(Request $req, $id){
        $focus = DB::select("
            SELECT 
                fc_id, fc_desc
            FROM
                elink_employee_directory.lnk_focus
            WHERE
                fc_status = 1
            ORDER BY fc_id ASC;
        ");
        
         $main_obj =  DB::select("
                SELECT 
                    lm.lnk_id,
                    se.se_com_id,
                    lm.lnk_date,
                    lm.lnk_linker,
                    lm.lnk_acknw,
                    (SELECT 
                            CONCAT(ei.first_name, ' ', ei.last_name)
                        FROM
                            employee_info AS ei
                        WHERE
                            ei.id = lm.lnk_linker) AS lnk_linker_name,
                    (SELECT 
                            ei.email
                        FROM
                            employee_info AS ei
                        WHERE
                            ei.id = lm.lnk_linker) AS lnk_linker_email,
                    lm.lnk_type,
                    lm.lnk_linkee,
                    (SELECT 
                            CONCAT(ei.first_name, ' ', ei.last_name)
                        FROM
                            employee_info AS ei
                        WHERE
                            ei.id = lm.lnk_linkee) AS lnk_linkee_name,
                    (SELECT 
                            ei.email
                        FROM
                            employee_info AS ei
                        WHERE
                            ei.id = lm.lnk_linkee) AS lnk_linkee_email,
                    se.se_focus,
                    (SELECT 
                            fc_desc
                        FROM
                            lnk_focus
                        WHERE
                            fc_id = se.se_focus
                        LIMIT 1) AS focus_desc,
                    se.se_skill,
                    se.se_when_use,
                    se.se_how_use,
                    se.se_why_use,
                    se.se_expectations,
                    se.se_comments,
                    se.se_feedback
                FROM
                    setting_expectations AS se
                        LEFT JOIN
                    linking_master AS lm ON lm.lnk_id = se.se_link_id
                WHERE
                    se.se_com_id = '$id'
                LIMIT 1;
            ");
            $exp = $main_obj[0];
            $obj = [
                "lnk_date"          => $exp->lnk_date, 
                "lnk_linker"        => $exp->lnk_linker, 
                "lnk_linker_name"   => $exp->lnk_linker_name, 
                "lnk_linker_email"  => $exp->lnk_linker_email, 
                "lnk_type"          => $exp->lnk_type, 
                "se_com_id"         => $exp->se_com_id,
                "se_focus"          => $exp->se_focus, 
                "se_skill"          => $exp->se_skill, 
                "se_when_use"       => $exp->se_when_use,
                "se_how_use"        => $exp->se_how_use,
                "se_why_use"        => $exp->se_why_use,
                "se_expectations"   => $exp->se_expectations,
                "se_comments"       => $exp->se_comments, 
                "lnk_linkee"        => $exp->lnk_linkee,
                "lnk_linkee_name"   => $exp->lnk_linkee_name, 
                "lnk_linkee_email"  => $exp->lnk_linkee_email, 
                "flag"              => 1,
                "update"            => 1,
                "focus_desc"        => $exp->focus_desc,
                "se_feedback"       => $exp->se_feedback,
                "sel_focus"         => $focus,
                "management"        => $this->isManagement(),
                "linkee_listing"    => NULL
            ];
            
        if($req->post("save_ce_linking")){
            $obj = [
                "lnk_date"          => $req->post("lnk_date"), 
                "lnk_linker"        => $req->post("lnk_linker"), 
                "lnk_linker_name"   => $req->post("lnk_linker_name"), 
                "lnk_linker_email"  => $req->post("lnk_linker_email"), 
                "lnk_type"          => $req->post("lnk_type"), 
                "se_com_id"         => $req->post("se_com_id"), 
                "se_focus"          => $req->post("se_focus"), 
                "se_skill"          => $req->post("se_skill"), 
                "se_when_use"       => $req->post("se_when_use"),
                "se_how_use"        => $req->post("se_how_use"),
                "se_why_use"        => $req->post("se_why_use"),
                "se_expectations"   => $req->post("se_expectations"),
                "se_comments"       => $req->post("se_comments"), 
                "lnk_linkee"        => $req->post("lnk_linkee"), 
                "lnk_linkee_name"   => $req->post("lnk_linkee_name"),
                "flag"              => $req->post("save_ce_linking"),
                "update"            => 1,
                "sel_focus"         => $focus,
                "linkee_listing"    => NULL
            ];
            $this->processSaving($req);
            return redirect("/ce-expectation/$id");
        }else
        if($req->post("acknow_ce_linking")){
            CementingExpectations::where("se_com_id",$req->post("se_com_id"))
                ->update(["se_feedback" => $req->post("se_feedback")]);
            
            $this_ce = CementingExpectations::where("se_com_id",$req->post("se_com_id"))->first();
            
            LinkingMaster::where("lnk_id",$this_ce->se_link_id)
                ->update(["lnk_acknw" => 1]);
            
            return redirect("/coaching-session");
        }
         if($exp->lnk_linkee == $this->getActiveUser() && !$req->post("acknow_ce_linking") && !$exp->lnk_acknw){/* Staff Acknowledge Cementing Expectations */
            return view('coaching.se_ack')
                ->with("obj",$obj)->with("management",$this->isManagement());
        }else
        if($exp->lnk_acknw || $obj['lnk_linker'] != $this->getActiveUser() ){/* View Cementing Expectations for staff and management */
            return view('coaching.se_view')
                ->with("obj",$obj)->with("management",$this->isManagement());
        }
        else
            return view('coaching.se')
                ->with("obj",$obj)->with("management",$this->isManagement());
    }
    
    public function viewSB(Request $req, $id){
        $focus = DB::select("
            SELECT 
                fc_id, fc_desc
            FROM
                elink_employee_directory.lnk_focus
            WHERE
                fc_status = 1
            ORDER BY fc_id ASC;
        ");
        if($req->post("acknowledge_SB_linking")){
            SkillBuilding::where("sb_com_num",$id)->update(["sb_feedback"=>$req->post("sb_feedback")]);
            $tar_sb = SkillBuilding::where("sb_com_num",$id)->first();
            LinkingMaster::where("lnk_id",$tar_sb->sb_link_id)->update(["lnk_acknw"=>1]);
            
            return redirect("/coaching-session");
        }else
        if($req->post("update_SB_linking")){
        /* Instantiate from Post Vars */
            $array = [
                "lnk_date"          => $req->post("lnk_date"), 
                "lnk_linker"        => $req->post("lnk_linker"), 
                "lnk_linker_name"   => $req->post("lnk_linker_name"), 
                "lnk_linker_email"  => $req->post("lnk_linker_email"), 
                "lnk_linkee"        => $req->post("lnk_linkee"), 
                "lnk_linkee_name"   => $req->post("lnk_linkee_name"),
                "lnk_linkee_email"  => $req->post("lnk_linkee_email"),
                "lnk_type"          => $req->post("lnk_type"),
                "sb_com_num"        => $req->post("sb_com_num"),
                "sb_focus"          => $req->post("sb_focus"),
                "sb_skill"          => $req->post("sb_skill"),
                "sb_when_skill"     => $req->post("sb_when_skill"),
                "sb_how_skill"      => $req->post("sb_how_skill"),
                "sb_why_skill"      => $req->post("sb_why_skill"),
                "sb_takeaway"       => $req->post("sb_takeaway"),
                "sb_timeframe"      => $req->post("sb_timeframe"),
                "sb_feedback"       => $req->post("sb_feedback"),
                "update"            => 1,
                "flag"              => $req->post("update_SB_linking"),
                "sel_focus"         => $focus
            ];
            if($this->verifySB($req))
                $this->processSaving($req);
        }else{
        /* Instantiate from Database */
            $main_obj = DB::select("
                SELECT 
                    *,
                    (SELECT 
                            CONCAT(ei.first_name, ' ', ei.last_name)
                        FROM
                            employee_info AS ei
                        WHERE
                            ei.id = lm.lnk_linker
                        LIMIT 1) AS lnk_linker_name,
                    (SELECT 
                            ei.email
                        FROM
                            employee_info AS ei
                        WHERE
                            ei.id = lm.lnk_linker
                        LIMIT 1) AS lnk_linker_email,
                    (SELECT 
                            CONCAT(ei.first_name, ' ', ei.last_name)
                        FROM
                            employee_info AS ei
                        WHERE
                            ei.id = lm.lnk_linkee
                        LIMIT 1) AS lnk_linkee_name,
                    (SELECT 
                            ei.email
                        FROM
                            employee_info AS ei
                        WHERE
                            ei.id = lm.lnk_linkee
                        LIMIT 1) AS lnk_linkee_email
                FROM
                    linking_master AS lm
                        LEFT JOIN
                    skill_building AS sb ON sb.sb_link_id = lm.lnk_id
                WHERE
                    sb.sb_com_num = '$id'
                LIMIT 1;
            ");
            $o = $main_obj[0];
            $active_user = $this->getActiveUser();
            $array = [
                "lnk_date"          => $o->lnk_date, 
                "lnk_linker"        => $o->lnk_linker, 
                "lnk_linker_name"   => $o->lnk_linker_name, 
                "lnk_linker_email"  => $o->lnk_linker_email, 
                "lnk_linkee"        => $o->lnk_linkee, 
                "lnk_linkee_name"   => $o->lnk_linkee_name,
                "lnk_linkee_email"  => $o->lnk_linkee_email,
                "lnk_type"          => $o->lnk_type,
                "sb_com_num"        => $o->sb_com_num,
                "sb_focus"          => $o->sb_focus,
                "sb_skill"          => $o->sb_skill,
                "sb_when_skill"     => $o->sb_when_skill,
                "sb_how_skill"      => $o->sb_how_skill,
                "sb_why_skill"      => $o->sb_why_skill,
                "sb_takeaway"       => $o->sb_takeaway,
                "sb_timeframe"      => $o->sb_timeframe,
                "sb_feedback"       => $o->sb_feedback,
                "update"            => $active_user == $o->lnk_linker && $o->lnk_acknw == 0 ? 1 : 2,
                "acknowledge"       => $active_user == $o->lnk_linkee && $o->lnk_acknw == 0 ? 1 : 0,
                "flag"              => 1,
                "sel_focus"         => $focus,
            ];
        }
   
        return view("coaching.sb_view")                
        ->with("obj",$array)
        ->with("management",$this->isManagement());
    }
    
    public function viewQL(Request $req, $id){

        if($req->post("lnk_linkee") && $req->post("rf_focus") && $req->post("rf_comments") && $req->post("update_linking")):
            LinkingMaster::where('lnk_id',$id)
                ->update(['lnk_linkee' => $req->post("lnk_linkee")]);
            
            QuickLink::where('rf_lnk_id',$id)
                ->update(['rf_focus' => $req->post("rf_focus"),'rf_comments' => $req->post("rf_comments")]);
            
            return redirect("/quick-link/$id");
        elseif($req->post("acknowledge_linking") && $req->post("rf_feedback")):
            $this->acknowldedgeQL($req);
            return redirect('/coaching-session');
        else:
            $main_id = $this->getActiveUser();
            $main_obj = DB::select("
                SELECT 
                    lm.lnk_id,
                    lm.lnk_date,
                    lm.lnk_linker,
                    lm.lnk_linkee,
                    lm.lnk_acknw,
                    ql.rf_focus,
                    lf.fc_desc,
                    ql.rf_comments,
                    ql.rf_feedback
                FROM
                    linking_master AS lm
                        LEFT JOIN
                    quick_link AS ql ON ql.rf_lnk_id = lm.lnk_id
                        LEFT JOIN
                    lnk_focus AS lf ON lf.fc_id = ql.rf_focus
                WHERE
                    lm.lnk_type = 1 AND lm.lnk_id = $id
                LIMIT 1;
            ");
            $linkee = $main_obj[0]->lnk_linkee;
            if( $main_obj[0]->lnk_linker == $this->getActiveUser() && $main_obj[0]->lnk_acknw == 0 ){
                $main_names = DB::select("
                    SELECT 
                        id, first_name, last_name
                    FROM
                        elink_employee_directory.employee_info
                    WHERE
                        (supervisor_id = $main_id
                            OR manager_id = $main_id)
                            AND status = 1
                            AND deleted_at IS NULL
                    ORDER BY last_name ASC;");

                $focus = DB::select("
                    SELECT 
                        fc_id, fc_desc
                    FROM
                        elink_employee_directory.lnk_focus
                    WHERE
                        fc_status = 1
                    ORDER BY fc_id ASC;
                ");
                return view("coaching.quick_link_view")
                    ->with("names",$main_names)
                    ->with("obj",$main_obj[0])
                    ->with("focus",$focus)
                    ->with("lastVal",[
                        "lnk_linkee"    => $req->post("lnk_linkee"),
                        "rf_focus"      => $req->post("rf_focus"),
                        "rf_comments"   => $req->post("rf_comments"),
                        "flag"          => $req->post("update_linking")
                    ])
                    ->with("management",$this->isManagement());
            }else
            if( !$main_obj[0]->lnk_acknw && $main_obj[0]->lnk_linkee == $this->getActiveUser() ){
                /* Staff Side For Acknowledgement */
                $obj = DB::select("
                    SELECT 
                        lm.lnk_id, lm.lnk_acknw, lnk_linkee, lm.lnk_date, lf.fc_desc, ql.rf_comments
                    FROM
                        linking_master AS lm
                            LEFT JOIN
                        quick_link AS ql ON ql.rf_lnk_id = lm.lnk_id
                            LEFT JOIN
                        lnk_focus AS lf ON lf.fc_id = ql.rf_focus
                    WHERE
                        lm.lnk_id = $id AND lm.lnk_status = 1;
                ");
                if($obj[0]->lnk_linkee == Auth::user()->id && $obj[0]->lnk_acknw == 0)
                    return view("coaching.quick_link_staff")->with("info",$obj[0])->with("management",$this->isManagement());
                else
                   return "No Access for staff side forAcknowledgement()";
            }else
            if( (($main_obj[0]->lnk_linkee == $this->getActiveUser() || $main_obj[0]->lnk_linker == $this->getActiveUser()) && $main_obj[0]->lnk_acknw == 1) || $this->isManagement() ){
                    $getName = DB::select("
                        SELECT 
                            CONCAT(ei.first_name, ' ', ei.last_name) AS lnk_linkee_name
                        FROM
                            employee_info AS ei
                        WHERE
                            ei.id = $linkee;
                    ");
                    
                    return view("coaching.quick_link_view2")
                        ->with("info",$main_obj[0])
                        ->with("lnk_linkee_name",$getName[0]->lnk_linkee_name)
                        ->with("management",$this->isManagement());
                }
            else
                return "No Access or No Action in viewQL();";
        endif;
    }
    
    public function forAcknowledgement(){
        $linker = Auth::user()->id;
        $pending = DB::select("
            SELECT 
               (CASE
                   WHEN lm.lnk_type = 1 THEN lm.lnk_id
                   WHEN
                       lm.lnk_type = 2
                   THEN
                       (SELECT 
                               se.se_com_id
                           FROM
                               setting_expectations AS se
                           WHERE
                               se.se_link_id = lm.lnk_id
                           LIMIT 1)
                   WHEN
                       lm.lnk_type = 3
                   THEN
                       (SELECT 
                               av.ac_com_id
                           FROM
                               accblty_conv AS av
                           WHERE
                               av.ac_link_id = lm.lnk_id
                           LIMIT 1)
                   WHEN
                       lm.lnk_type = 4
                   THEN
                       (SELECT 
                               sda.sda_com_id
                           FROM
                               sda
                           WHERE
                               sda.sda_lnk_id = lm.lnk_id
                           LIMIT 1)
                   WHEN
                       lm.lnk_type = 6
                   THEN
                       (SELECT 
                               sb1.sb_com_num
                           FROM
                               skill_building AS sb1
                           WHERE
                               sb1.sb_link_id = lm.lnk_id
                           LIMIT 1)
                   WHEN
                       lm.lnk_type = 7
                   THEN
                       (SELECT 
                               gs.gs_com_id
                           FROM
                               goal_setting AS gs
                           WHERE
                               gs.gs_link_id = lm.lnk_id
                           LIMIT 1)
               END) AS lnk_id,
               lm.lnk_date,
               lt.lt_desc AS link_type,
               (SELECT 
                       CONCAT(ei.first_name, ' ', ei.last_name)
                   FROM
                       employee_info AS ei
                   WHERE
                       ei.id = lm.lnk_linker
                   LIMIT 1) AS linker,
               (SELECT 
                       CONCAT(ei.first_name, ' ', ei.last_name)
                   FROM
                       employee_info AS ei
                   WHERE
                       ei.id = lm.lnk_linkee
                   LIMIT 1) AS linkee,
               (CASE
                   WHEN
                       lm.lnk_type = 1
                   THEN
                       (SELECT 
                               lf.fc_desc
                           FROM
                               quick_link AS ql
                                   LEFT JOIN
                               lnk_focus AS lf ON lf.fc_id = ql.rf_focus
                           WHERE
                               ql.rf_lnk_id = lm.lnk_id
                           LIMIT 1)
                   WHEN
                       lm.lnk_type = 2
                   THEN
                       (SELECT 
                               lf.fc_desc
                           FROM
                               setting_expectations AS se
                                   LEFT JOIN
                               lnk_focus AS lf ON lf.fc_id = se.se_focus
                           WHERE
                               se.se_link_id = lm.lnk_id
                           LIMIT 1)
                   WHEN
                       lm.lnk_type = 3
                   THEN
                       (SELECT 
                               lf.fc_desc
                           FROM
                               accblty_conv AS av
                                   LEFT JOIN
                               lnk_focus AS lf ON av.ac_focus = lf.fc_id
                           WHERE
                               av.ac_link_id = lm.lnk_id
                           LIMIT 1)
                   WHEN
                       lm.lnk_type = 4
                   THEN
                       (SELECT 
                               CASE
                                       WHEN sda.sda_type = 1 THEN 'Call Listening Session'
                                       WHEN sda.sda_type = 2 THEN 'Mock Calls'
                                       ELSE 'Calibration Sessions'
                                   END
                           FROM
                               sda
                           WHERE
                               sda.sda_lnk_id = lm.lnk_id
                           LIMIT 1)
                   WHEN
                       lm.lnk_type = 6
                   THEN
                       (SELECT 
                               lf.fc_desc
                           FROM
                               skill_building AS sb2
                                   LEFT JOIN
                               lnk_focus AS lf ON sb2.sb_focus = lf.fc_id
                           WHERE
                               sb2.sb_link_id = lm.lnk_id
                           LIMIT 1)
                   WHEN
                       lm.lnk_type = 7
                   THEN
                       (SELECT 
                               gs.gs_com
                           FROM
                               goal_setting AS gs
                           WHERE
                               gs.gs_link_id = lm.lnk_id
                           LIMIT 1)
               END) AS focus,
               lt.lt_link
           FROM
               linking_master AS lm
                   LEFT JOIN
               linking_types AS lt ON lt.lt_id = lm.lnk_type
                   LEFT JOIN
               quick_link AS ql ON ql.rf_lnk_id = lm.lnk_id
           WHERE
               lm.lnk_acknw = 0 AND lm.lnk_status = 1
                   AND lm.lnk_linker = $linker
        ");
        
        return view("coaching.pending")->with("pending",$pending)->with("management",$this->isManagement());
    }
    
    public function mainCoaching(Request $req){
        if($req->post("lnk_type") && ($req->post("save_linking") || $req->post("save_ce_linking") || $req->post("save_sda_linking") || $req->post("save_ac_linking") || $req->post("save_gtky_session") || $req->post("save_SB_linking") || $req->post("save_goal_setting_session") )){
            if($this->verifyQuickLink($req) || $this->verifySE($req) || $this->verifySDA($req) || $this->verifyACC($req) || $this->verifyGTKY($req) || $this->verifySB($req) || $this->verifyGS($req)  )
                /* Create or Update Linking */
                return $this->processSaving($req);
            else
                return $this->processLinking($req);
        }
        else
        if($req->post("process_linking") && $req->post("lnk_type") && $req->post("lnk_linkee"))
            return $this->processLinking($req);
        else
        if($this->isManagement())
            return $this->viewManagement($req);
        else
            return $this->viewStaff ();
    }
    
    public function testLinking(){
        return $this->getLinking();
    }
    
    public function downloadLinking(){
        $writesheet = new Spreadsheet();
        $writer = IOFactory::createWriter($writesheet, "Xlsx");
        $sheet = $writesheet->getActiveSheet();
        $i = 1;
        $header = array("Date", "Employee Number", "Linkee","Linking Type","Linker","Focus","Comments","Status","Link");
        $sheet->fromArray([$header], NULL, 'A'.$i); 
        $i++;
        $list = $this->getLinking();
        foreach($list as $lk):
            $body = [
                date("F d, Y", strtotime($lk->lnk_date)),
                $lk->linkee_number,
                $lk->linkee,
                $lk->lt_desc,
                $lk->linker,
                $lk->focus,
                $lk->comments,
                $lk->status,
                $lk->link
            ];
            $sheet->fromArray([$body], NULL, 'A'.$i); 
            $i++;
        endforeach;
      
        ob_end_clean();
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="linking-report-'.date('mdY-His').'.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->setPreCalculateFormulas(false);
        $writer->setOffice2003Compatibility(true);
        $writer->save('php://output');
    }
    
    public function downloadLinking2(){
        $writesheet = new Spreadsheet();
        $writer = IOFactory::createWriter($writesheet, "Xlsx");
        $sheet = $writesheet->getActiveSheet();
        $i = 1;
       // $header = array("Date", "Employee Number", "Linkee","Linking Type","Linker","Focus","Status","Link");
        $header = array("Date", "Employee Number", "Linkee","Linking Type","Linker","Focus", "Comments","Status","Link");
        $sheet->fromArray([$header], NULL, 'A'.$i); 
        $i++;
        $list = $this->getLinking2();
        foreach($list as $lk):
            $body = [
                date("F d, Y", strtotime($lk->lnk_date)),
                $lk->linkee_number,
                $lk->linkee,
                $lk->lt_desc,
                $lk->linker,
                $lk->focus,
                //$lk->status,
                //$lk->link
            ];
            switch($lk->lnk_type):
                case 1:
                    $link_id = $lk->lnk_id;
                    $obj = DB::select("select * from quick_link where rf_lnk_id = $link_id limit 1;");
		if(count($obj) > 0){
                    $o = $obj[0];
		    array_push($body,$o->rf_comments ?? "");
		}
        //            array_push($body,$o->rf_comments,$o->rf_feedback);
               break;
                
                case 2:
                    $link_id = $lk->lnk_id;
                    $obj = DB::select("select * from setting_expectations where se_link_id = $link_id limit 1;");
		if(count($obj) > 0){
                   	 $o = $obj[0];
		 	array_push($body,$o->se_comments ?? "");
		}
        //            array_push($body,$o->se_skill,$o->se_when_use,$o->se_how_use,$o->se_why_use,$o->se_expectations,$o->se_timeframe,$o->se_comments,$o->se_feedback);
                break;
                
                case 3:
                    $link_id = $lk->lnk_id;
                    $obj = DB::select("SELECT * FROM elink_employee_directory.accblty_conv where ac_link_id = $link_id limit 1;");
		if(count($obj) > 0){
                    $o = $obj[0];
    		    array_push($body,$o->ac_comments ?? "");
		}
       //             array_push($body,$o->ac_skill,$o->ac_when_use,$o->ac_how_use,$o->ac_why_use,$o->ac_expectations,$o->ac_expectation_date,$o->ac_comments,$o->ac_feedback);
                break;
            
                case 4:
                    $link_id = $lk->lnk_id;
                    $obj = DB::select("SELECT * FROM sda where sda_lnk_id = $link_id limit 1;");
		if(count($obj) > 0){
                    $o = $obj[0];
 		    array_push($body,$o->sda_comments ?? "");
		}
       //             array_push($body,$o->sda_date_call,$o->sda_call_sel,$o->sda_www_u_said,$o->sda_www_i_said,$o->sda_wcm_u_said,$o->sda_wcm_i_said,$o->sda_take_away,$o->sda_timeframe,$o->sda_comments,$o->sda_feedback);
                    break;
                case 5:
                    $link_id = $lk->lnk_id;
                    $obj = DB::select("select * from gtky where gtk_link_id = $link_id limit 1;");
		if(count($obj) > 0){
                	 $o = $obj[0];
			 array_push($body,"");
		}
       //             array_push($body,$o->gtk_address,$o->gtk_bday,$o->gtk_bplace,$o->gtk_mobile,$o->gtk_email,$o->gtk_civil_stat,$o->gtk_fav_thing,$o->gtk_fav_color,$o->gtk_fav_movie,$o->gtk_fav_song,$o->gtk_fav_food,
       //                 $o->gtk_allergic_food,$o->gtk_allergic_med,$o->gtk_learn_style,$o->gtk_social_style,$o->gtk_motivation,$o->gtk_how_coached,$o->gtk_strength,$o->gtk_improvement,$o->gtk_goals,$o->gtk_others);
                break;
                case 6:
                    $link_id = $lk->lnk_id;
                    $obj = DB::select("select * from skill_building where sb_link_id = $link_id limit 1;");
			if(count($obj) > 0){
                    $o = $obj[0];
			array_push($body,$o->sb_feedback ?? "");
			}
     //               array_push($body,$o->sb_skill,$o->sb_when_skill,$o->sb_how_skill,$o->sb_why_skill,$o->sb_takeaway,$o->sb_timeframe,$o->sb_feedback);
                break;
                case 7:
                    $link_id = $lk->lnk_id;
                    $obj = DB::select("select * from goal_setting where gs_link_id = $link_id limit 1;");
		if(count($obj) > 0){
                    $o = $obj[0];
		  array_push($body, $o->gs_feedback ?? "");
		}
     //               array_push($body,$o->gs_accmpl,$o->gs_metric_01,$o->gs_metric_02,$o->gs_metric_03,$o->gs_metric_04,$o->gs_metric_05,
    //                   $o->gs_target_01, $o->gs_target_02, $o->gs_target_03, $o->gs_target_04, $o->gs_target_05,
    //                    $o->gs_prev_01, $o->gs_prev_02, $o->gs_prev_03, $o->gs_prev_04, $o->gs_prev_05,
    //                    $o->gs_curr_01, $o->gs_curr_02, $o->gs_curr_03, $o->gs_curr_04, $o->gs_curr_05,
    //                   $o->gs_tip, $o->gs_com, $o->gs_feedback
   //                    );
                break;
            endswitch;
	 array_push($body, $lk->status ?? "",$lk->link??"");
            $sheet->fromArray([$body], NULL, 'A'.$i); 
            $i++;
        endforeach;
      
        ob_end_clean();
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="linking-report-'.date('mdY-His').'.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->setPreCalculateFormulas(false);
        $writer->setOffice2003Compatibility(true);
        $writer->save('php://output');
    }
    
    
    private function getLinking2(){
        $sql = "";
        $user = $this->getActiveUser();
        /*
         * 3487 - Sir Brian
         * 2972 - Sir Dodge
         * 2797 - Madam Leah
         * 3422 - Madam Jane
         * 3581 - Madam GilFranz
         */
        if(!Auth::user()->isAdmin())
            $sql = "
                
                AND
                    lm.lnk_linker = $user
                        
            ";
        
        if($user == 3487 || $user == 2972 || $user == 2792 || $user == 3422 || $user == 3581)
            $sql = "";
        
        return DB::select("
            SELECT 
                lm.lnk_date,
                lm.lnk_id,
                lm.lnk_type,
                linkee_info.eid AS linkee_number,
                CONCAT(linkee_info.first_name,
                        ' ',
                        linkee_info.last_name) AS linkee,
                lt.lt_desc,
                GETEMPLOYEENAME(lm.lnk_linker) AS linker,
                LINKINGFOCUS(lm.lnk_id) AS focus,
                (CASE
                    WHEN lm.lnk_acknw = 0 THEN 'Pending'
                    ELSE 'Acknowledged'
                END) AS status,
                GETLINKINGLINK(lm.lnk_id) AS link
            FROM
                linking_master AS lm
                    LEFT JOIN
                linking_types AS lt ON lt.lt_id = lm.lnk_type
                    LEFT JOIN
                employee_info AS linkee_info ON linkee_info.id = lm.lnk_linkee
                WHERE lm.lnk_status = 1
                
                $sql

            ORDER BY lm.lnk_date DESC;
        ");
    }
    
    private function getLinking(){
        $sql = "";
        $user = $this->getActiveUser();
        if(!Auth::user()->isAdmin() && $user != 3487)
            $sql = "
                
                WHERE
                    lm.lnk_linker = $user
                        
            ";
        
        return DB::select("
            SELECT 
                lm.lnk_date,
                linkee_info.eid AS linkee_number,
                CONCAT(linkee_info.first_name,
                        ' ',
                        linkee_info.last_name) AS linkee,
                lt.lt_desc,
                GETEMPLOYEENAME(lm.lnk_linker) AS linker,
                LINKINGFOCUS(lm.lnk_id) AS focus,
                GETLINKCOMMENTS(lm.lnk_id) AS comments,
                (CASE
                    WHEN lm.lnk_acknw = 0 THEN 'Pending'
                    ELSE 'Acknowledged'
                END) AS status,
                GETLINKINGLINK(lm.lnk_id) AS link
            FROM
                linking_master AS lm
                    LEFT JOIN
                linking_types AS lt ON lt.lt_id = lm.lnk_type
                    LEFT JOIN
                employee_info AS linkee_info ON linkee_info.id = lm.lnk_linkee
                $sql
            ORDER BY lm.lnk_date DESC;
        ");
    }
    
    private function verifyGTKY($req){
        if($req->post("gtk_address") && $req->post("gtk_bday") && $req->post("gtk_bplace") && $req->post("gtk_mobile") && $req->post("gtk_email") && $req->post("gtk_civil_stat")
            && $req->post("gtk_fav_thing") && $req->post("gtk_fav_color") && $req->post("gtk_fav_movie") && $req->post("gtk_fav_song") && $req->post("gtk_allergic_food") &&
            $req->post("gtk_allergic_med") && $req->post("gtk_learn_style") && $req->post("gtk_social_style") && $req->post("gtk_motivation") && $req->post("gtk_how_coached") &&
            $req->post("gtk_strength") && $req->post("gtk_improvement") && $req->post("gtk_goals") && $req->post("gtk_others")
        )
            return 1;
        else
            return 0;
    }
    
    private function verifyACC($req){
        if(
            $req->post("ac_focus") &&
            $req->post("ac_skill") &&
            $req->post("ac_when_use") &&
            $req->post("ac_how_use") &&
            $req->post("ac_why_use") &&
            $req->post("ac_expectations") &&
            $req->post("ac_expectation_date") &&
            $req->post("ac_comments")
        )
            return 1;
        else
            return 0;
    }
    
    private function getActiveUser(){
        if(!$this->active_user)
            $this->active_user = Auth::user()->id;
        
        return $this->active_user;
    }
    
    private function verifySDA($req){
        if( $req->post("sda_type") &&
            $req->post("sda_date_call") &&
            $req->post("sda_call_sel") &&
            $req->post("sda_www_u_said") &&
            $req->post("sda_www_i_said") &&
            $req->post("sda_wcm_u_said") &&
            $req->post("sda_wcm_i_said") &&
            $req->post("sda_comments"))
            return 1;
        else
            return 0;
    }
    
    private function verifySE($req){

        if( $req->post("se_focus") && 
            $req->post("se_skill") && 
            $req->post("se_when_use") && 
            $req->post("se_how_use") && 
            $req->post("se_why_use") && 
            $req->post("se_expectations") && 
            $req->post("se_comments"))
            return 1;
        else
            return 0;
    }
    
    private function verifyGS($req){
        if( $req->post("gs_accmpl") &&
            $req->post("gs_metric_01") &&
            $req->post("gs_target_01") &&
            $req->post("gs_prev_01") &&
            $req->post("gs_curr_01") &&
            $req->post("gs_tip") &&
            $req->post("gs_com"))
            return 1;
        else
            return 0;
    }
    
    private function verifySB($req){
        if( $req->post("sb_focus") && 
            $req->post("sb_skill") &&
            $req->post("sb_when_skill") &&
            $req->post("sb_how_skill") &&   
            $req->post("sb_why_skill") &&
            $req->post("sb_takeaway") &&
            $req->post("sb_timeframe")
        )
            return 1;
        else
            return 0;
        
    }
    
    private function isManagement(){
	$linkees = DB::table('adtl_linkees')->where('adtl_linker', Auth::user()->id)->get();
	 $allowedUsers = [
            // add your allowed users here
            3655
        ];

        if (Auth::user()->usertype == 2 || Auth::user()->usertype == 3 || in_array(Auth::user()->id, $allowedUsers) || count($linkees) > 0)
            return 1;
        else 
            return 0;
    }
    
    private function acknowldedgeQL($res){
        LinkingMaster::where('lnk_id',$res->post("lnk_id"))
            ->update(["lnk_acknw" => 1]);
        QuickLink::where("rf_lnk_id",$res->post("lnk_id"))
            ->update(["rf_feedback" => $res->post("rf_feedback")]);
    }
    
    private function verifyQuickLink($req){
        if($req->post("rf_comments") && $req->post("rf_focus"))
            return 1;
        else
            return 0;
    }
    
    private function viewManagement($req){
    # Main Coaching Function
        $main_id = Auth::user()->id;
        /* original queries
        $main_names = DB::select("
            SELECT 
                id, first_name, last_name, email
            FROM
                elink_employee_directory.employee_info
            WHERE
                (supervisor_id = $main_id
                    OR manager_id = $main_id)
                    AND status = 1
                    AND deleted_at IS NULL
            ORDER BY last_name ASC;");
         * */
         $main_names = DB::select("
            SELECT 
                id, first_name, last_name, email
            FROM
                elink_employee_directory.employee_info AS ei,
                adtl_linkees AS al
            WHERE
                ((supervisor_id = $main_id
                    OR manager_id = $main_id)
                    AND status = 1
                    AND deleted_at IS NULL)
                    OR (al.adtl_linker = $main_id
                    AND ei.id = al.adtl_linkee)
            GROUP BY ei.id,ei.first_name,ei.last_name,ei.email
            ORDER BY last_name ASC;
        ");
        $lt_types = DB::select("
            SELECT 
                lt_id, lt_desc
            FROM
                linking_types
            WHERE
                lt_status = 1
            ORDER BY lt_order ASC;");
        return view('coaching.supervisor')
            ->with("management",$this->isManagement())
            ->with("names",$main_names)
            ->with("lt_types",$lt_types)
            ->with("lastVal",["lnk_type" => $req->post("lnk_type"), "lnk_linkee" => $req->post("lnk_linkee"), "lnk_linkee_name" => $req->post("lnk_linkee_name"), "lnk_linkee_email" => $req->post("lnk_linkee_email"), "flag" => $req->post("process_linking"), "lnk_linker_name" => $req->post("lnk_linker_name"), "lnk_linker_email" => $req->post("lnk_linker_email")]);
    }
    
    private function viewStaff(){
        $main_id = Auth::user()->id;
        $obj = DB::select("
            SELECT 
                (CASE
                    WHEN lm.lnk_type = 1 THEN lm.lnk_id
                    WHEN
                        lm.lnk_type = 2
                    THEN
                        (SELECT 
                                se_com_id
                            FROM
                                setting_expectations
                            WHERE
                                se_link_id = lm.lnk_id
                            LIMIT 1)
                    WHEN
                        lm.lnk_type = 3
                    THEN
                        (SELECT 
                                acc.ac_com_id
                            FROM
                                accblty_conv AS acc
                            WHERE
                                lm.lnk_id = acc.ac_link_id
                            LIMIT 1)
                    WHEN
                        lm.lnk_type = 4
                    THEN
                        (SELECT 
                                sda.sda_com_id
                            FROM
                                sda
                            WHERE
                                sda.sda_lnk_id = lm.lnk_id
                            LIMIT 1)
		    WHEN 
			lm.lnk_type = 5 
			    THEN lm.lnk_id
                    WHEN
                        lm.lnk_type = 6
                    THEN
                        (SELECT 
                                sb.sb_com_num
                            FROM
                                skill_building AS sb
                            WHERE
                                sb.sb_link_id = lm.lnk_id
                            LIMIT 1)
                    WHEN
                        lm.lnk_type = 7
                    THEN
                        (SELECT 
                                gs.gs_com_id
                            FROM
                                goal_setting AS gs
                            WHERE
                                gs.gs_link_id = lm.lnk_id
                            LIMIT 1)
                END) AS lnk_id,
                lm.lnk_date,
                lm.lnk_linker,
                (SELECT 
                        CONCAT(ei.first_name, ' ', ei.last_name)
                    FROM
                        employee_info AS ei
                    WHERE
                        ei.id = lm.lnk_linker
                    LIMIT 1) AS lnk_linker_name,
                lm.lnk_linkee,
                (SELECT 
                        CONCAT(ei.first_name, ' ', ei.last_name)
                    FROM
                        employee_info AS ei
                    WHERE
                        ei.id = lm.lnk_linkee
                    LIMIT 1) AS lnk_linkee_name,
                lm.lnk_type,
                lt.lt_desc AS link_type_desc,
                lt.lt_link,
                (CASE
                    WHEN
                        lm.lnk_type = 1
                    THEN
                        (SELECT 
                                lf.fc_desc
                            FROM
                                quick_link AS ql
                                    LEFT JOIN
                                lnk_focus AS lf ON lf.fc_id = ql.rf_focus
                            WHERE
                                ql.rf_lnk_id = lm.lnk_id
                            LIMIT 1)
                    WHEN
                        lm.lnk_type = 2
                    THEN
                        (SELECT 
                                lf.fc_desc
                            FROM
                                setting_expectations AS se
                                    LEFT JOIN
                                lnk_focus AS lf ON lf.fc_id = se.se_focus
                            WHERE
                                se.se_link_id = lm.lnk_id
                            LIMIT 1)
                    WHEN
                        lm.lnk_type = 3
                    THEN
                        (SELECT 
                                lf.fc_desc
                            FROM
                                accblty_conv AS av
                                    LEFT JOIN
                                lnk_focus AS lf ON av.ac_focus = lf.fc_id
                            WHERE
                                av.ac_link_id = lm.lnk_id
                            LIMIT 1)
                    WHEN
                        lm.lnk_type = 4
                    THEN
                        (SELECT 
                                CASE
                                        WHEN sda.sda_type = 1 THEN 'Call Listening Session'
                                        WHEN sda.sda_type = 2 THEN 'Mock Calls'
                                        WHEN sda.sda_type = 3 THEN 'Calibration Sessions'
                                    END
                            FROM
                                sda
                            WHERE
                                sda.sda_lnk_id = lm.lnk_id)
                    WHEN
                        lm.lnk_type = 6
                    THEN
                        (SELECT 
                                lf.fc_desc
                            FROM
                                skill_building AS sb
                                    LEFT JOIN
                                lnk_focus AS lf ON sb.sb_focus = lf.fc_id
                            WHERE
                                sb.sb_link_id = lm.lnk_id
                            LIMIT 1)
                    WHEN
                        lm.lnk_type = 7
                    THEN
                        (SELECT 
                                gs.gs_com
                            FROM
                                goal_setting AS gs
                            WHERE
                                gs.gs_link_id = lm.lnk_id
                            LIMIT 1)
                END) AS focus,
                lm.lnk_acknw
            FROM
                linking_master AS lm
                    LEFT JOIN
                linking_types AS lt ON lt.lt_id = lm.lnk_type
            WHERE
                lm.lnk_linkee = $main_id
                    AND lm.lnk_acknw = 0
                    AND lm.lnk_status = 1
            ORDER BY lm.lnk_id DESC;
        ");
        return view('coaching.staff')->with('linking',$obj)->with("management",$this->isManagement());
    }
    
    private function processLinking($req){
        $type = $req->post("lnk_type");
        $focus = DB::select("
            SELECT 
                fc_id, fc_desc
            FROM
                elink_employee_directory.lnk_focus
            WHERE
                fc_status = 1
            ORDER BY fc_id ASC;
        ");
        
        $linkee = $req->post("lnk_linkee");
        switch(intval($type)):
            case 1: 
                $list = DB::select("
                    SELECT 
                        lm.lnk_date,
                        (SELECT 
                                CONCAT(ei.first_name, ' ', ei.last_name)
                            FROM
                                employee_info AS ei
                            WHERE
                                ei.id = lm.lnk_linker
                            LIMIT 1) AS linker,
                        (SELECT 
                                lnk_focus.fc_desc
                            FROM
                                lnk_focus
                            WHERE
                                lnk_focus.fc_id = ql.rf_focus
                            LIMIT 1) AS rf_focus,
                        (CASE
                            WHEN lm.lnk_acknw = 1 THEN 'Acknowledged'
                            ELSE 'Pending'
                        END) AS status,
                        CONCAT('/quick-link/', lm.lnk_id) AS link
                    FROM
                        quick_link AS ql
                            LEFT JOIN
                        linking_master AS lm ON lm.lnk_id = ql.rf_lnk_id
                    WHERE
                        lm.lnk_linkee = $linkee
                    ORDER BY lm.lnk_date DESC;
                ");
                return view('coaching.quick_link')
                ->with("obj",[
                    "lnk_date" => $req->post("lnk_date"),
                    "lnk_linkee" => $linkee, 
                    "lnk_linkee_name" => $req->post("lnk_linkee_name"), 
                    "lnk_linkee_email" => $req->post("lnk_linkee_email"), 
                    "lnk_linker" => $req->post("lnk_linker"), 
                    "lnk_linker_name" => $req->post("lnk_linker_name"), 
                    "lnk_linker_email" => $req->post("lnk_linker_email"), 
                    "lnk_type" => $req->post("lnk_type"), 
                    "rf_focus" => $req->post("rf_focus"), 
                    "rf_comments" => $req->post("rf_comments"), 
                    "lnk_linkee" => $req->post("lnk_linkee"), 
                    "flag" => $req->post("save_linking"), 
                    "sel_focus" => $focus,
                    "linkee_listing" => $list
                ])
                ->with("management",$this->isManagement()); 
            break;
            case 2: 
                $list = DB::select("
                    SELECT 
                        lm.lnk_date,
                        (SELECT 
                            CONCAT(ei.first_name, ' ', ei.last_name)
                        FROM
                            employee_info AS ei
                        WHERE
                            ei.id = lm.lnk_linker
                        LIMIT 1) AS linker,
                        (SELECT 
                                lnk_focus.fc_desc
                            FROM
                                lnk_focus
                            WHERE
                                lnk_focus.fc_id = se.se_focus
                            LIMIT 1) AS se_focus,
                        (CASE
                            WHEN lm.lnk_acknw = 1 THEN 'Acknowledged'
                            ELSE 'Pending'
                        END) AS status,
                        CONCAT('/ce-expectation/', se.se_com_id) AS link
                    FROM
                        setting_expectations AS se
                            LEFT JOIN
                        linking_master AS lm ON lm.lnk_id = se.se_link_id
                    WHERE
                        lm.lnk_linkee = $linkee
                    ORDER BY lm.lnk_date DESC;
                ");
                return view('coaching.se')
                ->with("obj",[
                    "lnk_date"          => $req->post("lnk_date"), 
                    "lnk_linker"        => $req->post("lnk_linker"), 
                    "lnk_linker_name"   => $req->post("lnk_linker_name"), 
                    "lnk_linker_email"  => $req->post("lnk_linker_email"), 
                    "lnk_type"          => $req->post("lnk_type"), 
                    "se_focus"          => $req->post("se_focus"), 
                    "se_skill"          => $req->post("se_skill"), 
                    "se_when_use"       => $req->post("se_when_use"),
                    "se_how_use"        => $req->post("se_how_use"),
                    "se_why_use"        => $req->post("se_why_use"),
                    "se_expectations"   => $req->post("se_expectations"),
                    "se_comments"       => $req->post("se_comments"), 
                    "lnk_linkee"        => $req->post("lnk_linkee"), 
                    "lnk_linkee_name"   => $req->post("lnk_linkee_name"),
                    "lnk_linkee_email"  => $req->post("lnk_linkee_email"),
                    "flag"              => $req->post("save_ce_linking"), 
                    "sel_focus"         => $focus,
                    "update"            => 0,
                    "se_com_id"         => "",
                    "management"        => 1,
                    "linkee_listing"    => $list
                ])->with("management",1); 
            break;
            case 3: 
                $list = DB::select("
                    SELECT 
                        lm.lnk_date,
                        (SELECT 
                                CONCAT(ei.first_name, ' ', ei.last_name)
                            FROM
                                employee_info AS ei
                            WHERE
                                ei.id = lm.lnk_linker
                            LIMIT 1) AS linker,
                        (SELECT 
                                lnk_focus.fc_desc
                            FROM
                                lnk_focus
                            WHERE
                                lnk_focus.fc_id = av.ac_focus
                            LIMIT 1) AS ac_focus,
                        (CASE
                            WHEN lm.lnk_acknw = 1 THEN 'Acknowledged'
                            ELSE 'Pending'
                        END) AS status,
                        CONCAT('/acc-set/', av.ac_com_id) AS link
                    FROM
                        accblty_conv AS av
                            LEFT JOIN
                        linking_master AS lm ON lm.lnk_id = av.ac_link_id
                    WHERE
                        lm.lnk_linkee = $linkee
                    ORDER BY lm.lnk_date DESC;
                ");
                return view('coaching.accst')
                ->with("obj",[
                    "lnk_date"              => $req->post("lnk_date"), 
                    "lnk_linker"            => $req->post("lnk_linker"), 
                    "lnk_linker_name"       => $req->post("lnk_linker_name"), 
                    "lnk_linker_email"      => $req->post("lnk_linker_email"), 
                    "lnk_type"              => $req->post("lnk_type"), 
                    "ac_focus"              => $req->post("ac_focus"), 
                    "ac_skill"              => $req->post("ac_skill"), 
                    "ac_when_use"           => $req->post("ac_when_use"),
                    "ac_how_use"            => $req->post("ac_how_use"),
                    "ac_why_use"            => $req->post("ac_why_use"),
                    "ac_expectations"       => $req->post("ac_expectations"),
                    "ac_expectation_date"   => $req->post("ac_expectation_date"),
                    "ac_comments"           => $req->post("ac_comments"), 
                    "lnk_linkee"            => $req->post("lnk_linkee"), 
                    "lnk_linkee_name"       => $req->post("lnk_linkee_name"),
                    "lnk_linkee_email"      => $req->post("lnk_linkee_email"),
                    "flag"                  => $req->post("save_ac_linking"), 
                    "sel_focus"             => $focus,
                    "update"                => 0,
                    "ac_com_id"             => "",
                    "management"            => 1,
                    "linkee_listing"        => $list
                ])->with("management",1);     
            break;
            case 4: 
                $list = DB::select("
                    SELECT 
                        lm.lnk_date,
                        (SELECT 
                                CONCAT(ei.first_name, ' ', ei.last_name)
                            FROM
                                employee_info AS ei
                            WHERE
                                ei.id = lm.lnk_linker
                            LIMIT 1) AS linker,
                        (CASE
                            WHEN sda.sda_type = 1 THEN 'Call Listening Session'
                            WHEN sda.sda_type = 2 THEN 'Mock Calls'
                            WHEN sda.sda_type = 3 THEN 'Calibration Sessions'
                        END) AS focus,
                        (CASE
                            WHEN lm.lnk_acknw = 1 THEN 'Acknowledged'
                            ELSE 'Pending'
                        END) AS status,
                        CONCAT('/skill-dev-act/', sda.sda_com_id) AS link
                    FROM
                        sda
                            LEFT JOIN
                        linking_master AS lm ON lm.lnk_id = sda.sda_lnk_id
                    WHERE
                        lm.lnk_linkee = $linkee
                    ORDER BY lm.lnk_date DESC;
                ");
                return view('coaching.sda')
                ->with("obj",[
                    "lnk_date"          => $req->post("lnk_date"),
                    "lnk_type"          => $req->post("lnk_type"), 
                    "lnk_linker"        => $req->post("lnk_linker"), 
                    "lnk_linker_name"   => $req->post("lnk_linker_name"), 
                    "lnk_linker_email"  => $req->post("lnk_linker_email"), 
                    "lnk_linkee"        => $req->post("lnk_linkee"), 
                    "lnk_linkee_name"   => $req->post("lnk_linkee_name"),
                    "lnk_linkee_email"  => $req->post("lnk_linkee_email"),
                    "sda_com_id"        => "",
                    "sda_type"          => $req->post("sda_type"),
                    "sda_date_call"     => $req->post("sda_date_call"),
                    "sda_call_sel"      => $req->post("sda_call_sel"),
                    "sda_www_u_said"    => $req->post("sda_www_u_said"),
                    "sda_www_i_said"    => $req->post("sda_www_i_said"),
                    "sda_wcm_u_said"    => $req->post("sda_wcm_u_said"),
                    "sda_wcm_i_said"    => $req->post("sda_wcm_i_said"),
                    "sda_comments"      => $req->post("sda_comments"),
                    "update"            => 0,
                    "flag"              => $req->post("save_sda_linking"),
                    "management"        => $this->isManagement(),
                    "linkee_listing"    => $list,
                    "view_only"         => 0
                ])->with("management",$this->isManagement())
                ->with("management",1);
            break;
            case 5:
                return view('coaching.gtky')
                ->with("obj",[
                    "lnk_date"          => $req->post("lnk_date"), 
                    "lnk_linker"        => $req->post("lnk_linker"), 
                    "lnk_linker_name"   => $req->post("lnk_linker_name"), 
                    "lnk_linker_email"  => $req->post("lnk_linker_email"), 
                    "lnk_linkee"        => $req->post("lnk_linkee"), 
                    "lnk_linkee_name"   => $req->post("lnk_linkee_name"),
                    "lnk_linkee_email"  => $req->post("lnk_linkee_email"),
                    "lnk_type"          => $req->post("lnk_type"),
                    "gtk_link_id"       => $req->post("gtk_link_id"),
                    "gtk_com_num"       => $req->post("gtk_com_num"),
                    "gtk_emp_no"        => $req->post("gtk_emp_no"),
                    "gtk_address"       => $req->post("gtk_address"),
                    "gtk_bday"          => $req->post("gtk_bday"),
                    "gtk_bplace"        => $req->post("gtk_bplace"),
                    "gtk_mobile"        => $req->post("gtk_mobile"),
                    "gtk_email"         => $req->post("gtk_email"),
                    "gtk_civil_stat"    => $req->post("gtk_civil_stat"),
                    "gtk_fav_thing"     => $req->post("gtk_fav_thing"),
                    "gtk_fav_color"     => $req->post("gtk_fav_color"),
                    "gtk_fav_movie"     => $req->post("gtk_fav_movie"),
                    "gtk_fav_song"      => $req->post("gtk_fav_song"),
                    "gtk_fav_food"      => $req->post("gtk_fav_food"),
                    "gtk_allergic_food" => $req->post("gtk_allergic_food"),
                    "gtk_allergic_med"  => $req->post("gtk_allergic_med"),
                    "gtk_learn_style"   => $req->post("gtk_learn_style"),
                    "gtk_social_style"  => $req->post("gtk_social_style"),
                    "gtk_motivation"    => $req->post("gtk_motivation"),
                    "gtk_how_coached"   => $req->post("gtk_how_coached"),
                    "flag"              => $req->post("save_gtky_session"),
                    "gtk_strength"      => $req->post("gtk_strength"),
                    "gtk_improvement"   => $req->post("gtk_improvement"),
                    "gtk_goals"         => $req->post("gtk_goals"),
                    "gtk_others"        => $req->post("gtk_others"),
                    "update"            => 0
                ])
                ->with("management",$this->isManagement());
            break;
        
            case 6:
                $list = DB::select("
                    SELECT 
                        lm.lnk_date,
                        (SELECT 
                                CONCAT(ei.first_name, ' ', ei.last_name)
                            FROM
                                employee_info AS ei
                            WHERE
                                ei.id = lm.lnk_linker
                            LIMIT 1) AS linker,
                        (SELECT 
                                lnk_focus.fc_desc
                            FROM
                                lnk_focus
                            WHERE
                                lnk_focus.fc_id = sb.sb_focus
                            LIMIT 1) AS sb_focus,
                        (CASE
                            WHEN lm.lnk_acknw = 1 THEN 'Acknowledged'
                            ELSE 'Pending'
                        END) AS status,
                        CONCAT('/skill-building/', sb.sb_com_num,'?viewOnly=1') AS link
                    FROM
                        skill_building AS sb
                            LEFT JOIN
                        linking_master AS lm ON lm.lnk_id = sb.sb_link_id
                    WHERE
                        lm.lnk_linkee = $linkee
                    ORDER BY lm.lnk_date DESC;
                ");
                return view("coaching.sb")                
                ->with("obj",[
                    "lnk_date"          => $req->post("lnk_date"), 
                    "lnk_linker"        => $req->post("lnk_linker"), 
                    "lnk_linker_name"   => $req->post("lnk_linker_name"), 
                    "lnk_linker_email"  => $req->post("lnk_linker_email"), 
                    "lnk_linkee"        => $req->post("lnk_linkee"), 
                    "lnk_linkee_name"   => $req->post("lnk_linkee_name"),
                    "lnk_linkee_email"  => $req->post("lnk_linkee_email"),
                    "lnk_type"          => $req->post("lnk_type"),
                    "sb_com_num"        => $req->post("sb_com_num"),
                    "sb_focus"          => $req->post("sb_focus"),
                    "sb_skill"          => $req->post("sb_skill"),
                    "sb_when_skill"     => $req->post("sb_when_skill"),
                    "sb_how_skill"      => $req->post("sb_how_skill"),
                    "sb_why_skill"      => $req->post("sb_why_skill"),
                    "sb_takeaway"       => $req->post("sb_takeaway"),
                    "sb_timeframe"      => $req->post("sb_timeframe"),
                    "sb_feedback"       => $req->post("sb_feedback"),
                    "update"            => 0,
                    "flag"              => $req->post("save_SB_linking"),
                    "sel_focus"         => $focus,
                    "linkee_listing"    => $list,
                ])
                ->with("management",$this->isManagement());
            break;
        
            case 7:
                $list = DB::select("
                    SELECT 
                        lm.lnk_date,
                        (SELECT 
                                CONCAT(ei.first_name, ' ', ei.last_name)
                            FROM
                                employee_info AS ei
                            WHERE
                                ei.id = lm.lnk_linker
                            LIMIT 1) AS linker,
                        gs.gs_com,
                        (CASE
                            WHEN lm.lnk_acknw = 1 THEN 'Acknowledged'
                            ELSE 'Pending'
                        END) AS status,
                        CONCAT('/goal-setting/',
                                gs.gs_com_id,
                                '?viewOnly=1') AS link
                    FROM
                        goal_setting AS gs
                            LEFT JOIN
                        linking_master AS lm ON lm.lnk_id = gs.gs_link_id
                    WHERE
                        lm.lnk_linkee = $linkee
                    ORDER BY lm.lnk_date DESC;
                ");
                return view("coaching.gs")                
                ->with("obj",[
                    "lnk_date"          => $req->post("lnk_date"), 
                    "lnk_linker"        => $req->post("lnk_linker"), 
                    "lnk_linker_name"   => $req->post("lnk_linker_name"), 
                    "lnk_linker_email"  => $req->post("lnk_linker_email"), 
                    "lnk_linkee"        => $req->post("lnk_linkee"), 
                    "lnk_linkee_name"   => $req->post("lnk_linkee_name"),
                    "lnk_linkee_email"  => $req->post("lnk_linkee_email"),
                    "lnk_type"          => $req->post("lnk_type"),
                    "gs_com_id"         => $req->post("gs_com_id"),
                    "gs_accmpl"         => $req->post("gs_accmpl"),
                    "gs_metric_01"      => $req->post("gs_metric_01"),
                    "gs_metric_02"      => $req->post("gs_metric_02"),
                    "gs_metric_03"      => $req->post("gs_metric_03"),
                    "gs_metric_04"      => $req->post("gs_metric_04"),
                    "gs_metric_05"      => $req->post("gs_metric_05"),
                    "gs_metric_06"      => $req->post("gs_metric_06"),
                    "gs_metric_07"      => $req->post("gs_metric_07"),
                    "gs_target_01"      => $req->post("gs_target_01"),
                    "gs_target_02"      => $req->post("gs_target_02"),
                    "gs_target_03"      => $req->post("gs_target_03"),
                    "gs_target_04"      => $req->post("gs_target_04"),
                    "gs_target_05"      => $req->post("gs_target_05"),
                    "gs_target_06"      => $req->post("gs_target_06"),
                    "gs_target_07"      => $req->post("gs_target_07"),
                    "gs_prev_01"        => $req->post("gs_prev_01"),
                    "gs_prev_02"        => $req->post("gs_prev_02"),
                    "gs_prev_03"        => $req->post("gs_prev_03"),
                    "gs_prev_04"        => $req->post("gs_prev_04"),
                    "gs_prev_05"        => $req->post("gs_prev_05"),
                    "gs_prev_06"        => $req->post("gs_prev_06"),
                    "gs_prev_07"        => $req->post("gs_prev_07"),
                    "gs_curr_01"        => $req->post("gs_curr_01"),
                    "gs_curr_02"        => $req->post("gs_curr_02"),
                    "gs_curr_03"        => $req->post("gs_curr_03"),
                    "gs_curr_04"        => $req->post("gs_curr_04"),
                    "gs_curr_05"        => $req->post("gs_curr_05"),
                    "gs_curr_06"        => $req->post("gs_curr_06"),
                    "gs_curr_07"        => $req->post("gs_curr_07"),
                    "gs_tip"            => $req->post("gs_tip"),
                    "gs_com"            => $req->post("gs_com"),
                    "update"            => 0,
                    "flag"              => $req->post("save_goal_setting_session"),
                    "linkee_listing"    => $list,
                ])
                ->with("management",$this->isManagement());
            break;
        
            default: return "We Are Still Working In This Linking Session Type.";
        endswitch;
    }
    
    private function processSaving($req){
        $type = $req->post("lnk_type");
        switch(intval($type)):
            case 1: 
                $lm = new LinkingMaster();
                $lm->lnk_date = date("Y-m-d", strtotime($req->post("lnk_date")));
                $lm->lnk_linker = Auth::user()->id;
                $lm->lnk_linkee = $req->post("lnk_linkee");
                $lm->lnk_type = $req->post("lnk_type");
                $lm->lnk_acknw = 0;
                $lm->save();
                
                $ql = new QuickLink();
                $ql->rf_lnk_id = $lm->id;
                $ql->rf_focus = $req->post("rf_focus");
                $ql->rf_comments = $req->post("rf_comments");
                $ql->save();
                $obj =[
                    "lnk_linkee_name"   => $req->post("lnk_linkee_name"), /* linkee name */
                    "lnk_linkee_email"  => $req->post("lnk_linkee_email"), /* linkee email */
                    "linker_name"       => Auth::user()->first_name." ".Auth::user()->last_name,
                    "linker_email"      => Auth::user()->email,
                    "hash"              => $ql->id
                ];
                Mail::to([$obj['lnk_linkee_email'],$obj['linker_email']])->queue(new QuickLinkNotification($obj));
            break;
            case 2: 
                if($req->post("update")){
                    CementingExpectations::where("se_com_id",$req->post("se_com_id"))
                        ->update([
                            "se_focus"          => $req->post("se_focus"),
                            "se_skill"          => $req->post("se_skill"),
                            "se_when_use"       => $req->post("se_when_use"),
                            "se_how_use"        => $req->post("se_how_use"),
                            "se_why_use"        => $req->post("se_why_use"),
                            "se_expectations"   => $req->post("se_expectations"),
                            "se_comments"       => $req->post("se_comments"),
                        ]);
                }else{//create-start
                    $lm = new LinkingMaster();
                    $lm->lnk_date = date("Y-m-d", strtotime($req->post("lnk_date")));
                    $lm->lnk_linker = Auth::user()->id;
                    $lm->lnk_linkee = $req->post("lnk_linkee");
                    $lm->lnk_type = $req->post("lnk_type");
                    $lm->lnk_acknw = 0;
                    $lm->save();

                    $cm = new CementingExpectations();
                    $cm->se_com_id = sha1($lm->id);
                    $cm->se_link_id = $lm->id;
                    $cm->se_focus = $req->post("se_focus");
                    $cm->se_skill = $req->post("se_skill");
                    $cm->se_when_use = $req->post("se_when_use");
                    $cm->se_how_use = $req->post("se_how_use");
                    $cm->se_why_use = $req->post("se_why_use");
                    $cm->se_expectations = $req->post("se_expectations");
                    $cm->se_timeframe = "N\A";
                    $cm->se_comments = $req->post("se_comments");
                    $cm->save();

                    CementingExpectations::where("se_id",$cm->id)
                        ->update(["se_com_id" => sha1($cm->id)]);
                    
                    $se_id = CementingExpectations::where("se_id",$cm->id)->first();
                    
                    $obj =[
                        "lnk_linkee_name"   => $req->post("lnk_linkee_name"), /* linkee name */
                        "lnk_linkee_email"  => $req->post("lnk_linkee_email"), /* linkee email */
                        "linker_name"       => Auth::user()->first_name." ".Auth::user()->last_name,
                        "linker_email"      => Auth::user()->email,
                        "se_com_id"         => $se_id->se_com_id,
                        "hash"              => $cm->id
                    ];
                    Mail::to([$obj['lnk_linkee_email'],$obj['linker_email']])->queue(new CEMailNotification($obj));
                }//create-stop
            break;
            
            case 3: 
                if($req->post("update")){
                    AccountabilitySession::where("ac_com_id",$req->post("ac_com_id"))
                        ->update([
                            "ac_focus"              => $req->post("ac_focus"),
                            "ac_skill"              => $req->post("ac_skill"),
                            "ac_when_use"           => $req->post("ac_when_use"),
                            "ac_how_use"            => $req->post("ac_how_use"),
                            "ac_why_use"            => $req->post("ac_why_use"),
                            "ac_expectations"       => $req->post("ac_expectations"),
                            "ac_expectation_date"   => date("Y-m-d",strtotime($req->post("ac_expectation_date"))),
                            "ac_comments"           => $req->post("ac_comments")
                        ]);
                }else{
                    $lm = new LinkingMaster();
                    $lm->lnk_date = date("Y-m-d", strtotime($req->post("lnk_date")));
                    $lm->lnk_linker = Auth::user()->id;
                    $lm->lnk_linkee = $req->post("lnk_linkee");
                    $lm->lnk_type = $req->post("lnk_type");
                    $lm->lnk_acknw = 0;
                    $lm->save();

                    $acc = new AccountabilitySession();
                    $acc->ac_link_id = $lm->id;
                    $acc->ac_com_id = sha1($lm->id);
                    $acc->ac_focus = $req->post("ac_focus");
                    $acc->ac_skill = $req->post("ac_skill");
                    $acc->ac_when_use = $req->post("ac_when_use");
                    $acc->ac_why_use = $req->post("ac_why_use");
                    $acc->ac_how_use = $req->post("ac_how_use");
                    $acc->ac_expectations = $req->post("ac_expectations");
                    $acc->ac_expectation_date = date("Y-m-d", strtotime($req->post("ac_expectation_date")));
                    $acc->ac_comments = $req->post("ac_comments");
                    $acc->ac_feedback = "";
                    $acc->save();

                    $obj =[
                        "lnk_linkee_name"   => $req->post("lnk_linkee_name"), /* linkee name */
                        "lnk_linkee_email"  => $req->post("lnk_linkee_email"), /* linkee email */
                        "linker_name"       => Auth::user()->first_name." ".Auth::user()->last_name,
                        "linker_email"      => Auth::user()->email,
                        "ac_com_id"         => $acc->ac_com_id ,
                        "hash"              => $acc->id
                    ];
                    Mail::to([$obj['lnk_linkee_email'],$obj['linker_email']])->queue(new ACCMailNotification($obj));
                }

            break;
        
            case 4: 
                if($req->post("update")){
                    SkillsDevelopment::where("sda_com_id",$req->post("sda_com_id"))
                        ->update([
                            "sda_type"          => $req->post("sda_type"),
                            "sda_date_call"     => date("Y-m-d", strtotime($req->post("sda_date_call"))),
                            "sda_call_sel"      => $req->post("sda_call_sel"),
                            "sda_www_u_said"    => $req->post("sda_www_u_said"),
                            "sda_www_i_said"    => $req->post("sda_www_i_said"),
                            "sda_wcm_u_said"    => $req->post("sda_wcm_u_said"),
                            "sda_wcm_i_said"    => $req->post("sda_wcm_i_said"),
                            "sda_comments"      => $req->post("sda_comments")
                        ]);
                }else{//create-sda
                    $lm = new LinkingMaster();
                    $lm->lnk_date = date("Y-m-d", strtotime($req->post("lnk_date")));
                    $lm->lnk_linker = Auth::user()->id;
                    $lm->lnk_linkee = $req->post("lnk_linkee");
                    $lm->lnk_type = $req->post("lnk_type");
                    $lm->lnk_acknw = 0;
                    $lm->save();
                    
                    $sda = new SkillsDevelopment();
                    $sda->sda_lnk_id        = $lm->id;
                    $sda->sda_com_id        = sha1($lm->id);
                    $sda->sda_type          = $req->post("sda_type");
                    $sda->sda_date_call     = date("Y-m-d", strtotime($req->post("sda_date_call")));
                    $sda->sda_call_sel      = $req->post("sda_call_sel");
                    $sda->sda_www_u_said    = $req->post("sda_www_u_said");
                    $sda->sda_www_i_said    = $req->post("sda_www_i_said");
                    $sda->sda_wcm_u_said    = $req->post("sda_wcm_u_said");
                    $sda->sda_wcm_i_said    = $req->post("sda_wcm_i_said");
                    $sda->sda_comments      = $req->post("sda_comments");
                    $sda->sda_feedback      = "";
                    $sda->save();
                    
                    $obj =[
                        "lnk_linkee_name"   => $req->post("lnk_linkee_name"), /* linkee name */
                        "lnk_linkee_email"  => $req->post("lnk_linkee_email"), /* linkee email */
                        "linker_name"       => Auth::user()->first_name." ".Auth::user()->last_name,
                        "linker_email"      => Auth::user()->email,
                        "sda_com_id"        => $sda->sda_com_id ,
                        "hash"              => $sda->id
                    ];
                    Mail::to([$obj['lnk_linkee_email'],$obj['linker_email']])->queue(new SDAMailNotification($obj));
                }//end-create
            break;
            
            case 5:
                if($req->post("update")){
                    $id = $req->post("gtk_com_num");
                    $object = [
                        "gtk_address"       => $req->post("gtk_address"),
                        "gtk_bday"          => $req->post("gtk_bday"),
                        "gtk_bplace"        => $req->post("gtk_bplace"),
                        "gtk_mobile"        => $req->post("gtk_mobile"),
                        "gtk_email"         => $req->post("gtk_email"),
                        "gtk_civil_stat"    => $req->post("gtk_civil_stat"),
                        "gtk_fav_thing"     => $req->post("gtk_fav_thing"),
                        "gtk_fav_color"     => $req->post("gtk_fav_color"),
                        "gtk_fav_movie"     => $req->post("gtk_fav_movie"),
                        "gtk_fav_song"      => $req->post("gtk_fav_song"),
                        "gtk_fav_food"      => $req->post("gtk_fav_food"),
                        "gtk_allergic_food" => $req->post("gtk_allergic_food"),
                        "gtk_allergic_med"  => $req->post("gtk_allergic_med"),
                        "gtk_learn_style"   => $req->post("gtk_learn_style"),
                        "gtk_social_style"  => $req->post("gtk_social_style"),
                        "gtk_motivation"    => $req->post("gtk_motivation"),
                        "gtk_how_coached"   => $req->post("gtk_how_coached"),
                        "gtk_strength"      => $req->post("gtk_strength"),
                        "gtk_improvement"   => $req->post("gtk_improvement"),
                        "gtk_goals"         => $req->post("gtk_goals"),
                        "gtk_others"        => $req->post("gtk_others"),
                    ];
                    DB::table("gtky")->where("gtk_com_num",$id)->update($object);
                }else{ 
                    //Create Linking Master Session
                    $lm = new LinkingMaster();
                    $lm->lnk_date = date("Y-m-d", strtotime($req->post("lnk_date")));
                    $lm->lnk_linker = Auth::user()->id;
                    $lm->lnk_linkee = $req->post("lnk_linkee");
                    $lm->lnk_type = $req->post("lnk_type");
                    $lm->lnk_acknw = 1;
                    $lm->save();
                    
                    //Create GTKY or Save GTKY Session
                    DB::table("gtky")->insert([
                        "gtk_link_id"       => $lm->id,
                        "gtk_com_num"       => sha1($lm->id),
                        "gtk_address"       => $req->post("gtk_address"),
                        "gtk_bday"          => date("Y-m-d",strtotime($req->post("gtk_bday"))),
                        "gtk_bplace"        => $req->post("gtk_bplace"),
                        "gtk_mobile"        => $req->post("gtk_mobile"),
                        "gtk_email"         => $req->post("gtk_email"),
                        "gtk_civil_stat"    => $req->post("gtk_civil_stat"),
                        "gtk_fav_thing"     => $req->post("gtk_fav_thing"),
                        "gtk_fav_color"     => $req->post("gtk_fav_color"),
                        "gtk_fav_movie"     => $req->post("gtk_fav_movie"),
                        "gtk_fav_song"      => $req->post("gtk_fav_song"),
                        "gtk_fav_food"      => $req->post("gtk_fav_food"),
                        "gtk_allergic_food" => $req->post("gtk_allergic_food"),
                        "gtk_allergic_med"  => $req->post("gtk_allergic_med"),
                        "gtk_learn_style"   => $req->post("gtk_learn_style"),
                        "gtk_social_style"  => $req->post("gtk_social_style"),
                        "gtk_motivation"    => $req->post("gtk_motivation"),
                        "gtk_how_coached"   => $req->post("gtk_how_coached"),
                        "gtk_strength"      => $req->post("gtk_strength"),
                        "gtk_improvement"   => $req->post("gtk_improvement"),
                        "gtk_goals"         => $req->post("gtk_goals"),
                        "gtk_others"        => $req->post("gtk_others"),
                    ]);
                }
            break;
            
            case 6:
                if($req->post("update")){
                //Update Skill Building Session
                    SkillBuilding::where("sb_com_num",$req->post("sb_com_num"))->update([
                        "sb_focus"      => $req->post("sb_focus"),
                        "sb_skill"      => $req->post("sb_skill"),
                        "sb_when_skill" => $req->post("sb_when_skill"),
                        "sb_why_skill"  => $req->post("sb_why_skill"),
                        "sb_how_skill"  => $req->post("sb_how_skill"),
                        "sb_takeaway"   => $req->post("sb_takeaway"),
                        "sb_timeframe"  => $req->post("sb_timeframe")
                    ]);
                }else{
                //Save Skill Building Session
                    $lm = new LinkingMaster();
                    $lm->lnk_date = date("Y-m-d", strtotime($req->post("lnk_date")));
                    $lm->lnk_linker = Auth::user()->id;
                    $lm->lnk_linkee = $req->post("lnk_linkee");
                    $lm->lnk_type = $req->post("lnk_type");
                    $lm->lnk_acknw = 0;
                    $lm->save();
                    
                    $sb = new SkillBuilding();
                    $sb->sb_link_id = $lm->id;
                    $sb->sb_com_num = sha1($lm->id);
                    $sb->sb_focus = $req->post("sb_focus");
                    $sb->sb_skill = $req->post("sb_skill");
                    $sb->sb_when_skill = $req->post("sb_when_skill");
                    $sb->sb_why_skill = $req->post("sb_why_skill");
                    $sb->sb_how_skill = $req->post("sb_how_skill");
                    $sb->sb_takeaway = $req->post("sb_takeaway");
                    $sb->sb_timeframe = $req->post("sb_timeframe");
                    $sb->save();
                    
                    $obj =[
                        "lnk_linkee_name"   => $req->post("lnk_linkee_name"), /* linkee name */
                        "lnk_linkee_email"  => $req->post("lnk_linkee_email"), /* linkee email */
                        "linker_name"       => Auth::user()->first_name." ".Auth::user()->last_name,
                        "linker_email"      => Auth::user()->email,
                        "sb_com_num"        => $sb->sb_com_num ,
                        "hash"              => $sb->id
                    ];
                    Mail::to([$obj['lnk_linkee_email'],$obj['linker_email']])->queue(new SkillBuildingNotification($obj));
                }
            break;
            
            case 7:
                if($req->post("update")){
                /* Update Goal Setting Session */    
                    GoalSetting::where("gs_com_id",$req->post("gs_com_id"))->update([
                        "gs_accmpl"         => $req->post("gs_accmpl"),
                        "gs_metric_01"      => $req->post("gs_metric_01"),
                        "gs_metric_02"      => $req->post("gs_metric_02"),
                        "gs_metric_03"      => $req->post("gs_metric_03"),
                        "gs_metric_04"      => $req->post("gs_metric_04"),
                        "gs_metric_05"      => $req->post("gs_metric_05"),
                        "gs_metric_06"      => $req->post("gs_metric_06"),
                        "gs_metric_07"      => $req->post("gs_metric_07"),
                        "gs_target_01"      => $req->post("gs_target_01"),
                        "gs_target_02"      => $req->post("gs_target_02"),
                        "gs_target_03"      => $req->post("gs_target_03"),
                        "gs_target_04"      => $req->post("gs_target_04"),
                        "gs_target_05"      => $req->post("gs_target_05"),
                        "gs_target_06"      => $req->post("gs_target_06"),
                        "gs_target_07"      => $req->post("gs_target_07"),
                        "gs_prev_01"        => $req->post("gs_prev_01"),
                        "gs_prev_02"        => $req->post("gs_prev_02"),
                        "gs_prev_03"        => $req->post("gs_prev_03"),
                        "gs_prev_04"        => $req->post("gs_prev_04"),
                        "gs_prev_05"        => $req->post("gs_prev_05"),
                        "gs_prev_06"        => $req->post("gs_prev_06"),
                        "gs_prev_07"        => $req->post("gs_prev_07"),
                        "gs_curr_01"        => $req->post("gs_curr_01"),
                        "gs_curr_02"        => $req->post("gs_curr_02"),
                        "gs_curr_03"        => $req->post("gs_curr_03"),
                        "gs_curr_04"        => $req->post("gs_curr_04"),
                        "gs_curr_05"        => $req->post("gs_curr_05"),
                        "gs_curr_06"        => $req->post("gs_curr_06"),
                        "gs_curr_07"        => $req->post("gs_curr_07"),
                        "gs_tip"            => $req->post("gs_tip"),
                        "gs_com"            => $req->post("gs_com")
                    ]);
                }else{
                /* Create Goal Setting Session */
                    $lm = new LinkingMaster();
                    $lm->lnk_date = date("Y-m-d", strtotime($req->post("lnk_date")));
                    $lm->lnk_linker = Auth::user()->id;
                    $lm->lnk_linkee = $req->post("lnk_linkee");
                    $lm->lnk_type = $req->post("lnk_type");
                    $lm->lnk_acknw = 0;
                    $lm->save();
                    
                    $gs = new GoalSetting();
                    $gs->gs_link_id = $lm->id;
                    $gs->gs_com_id = sha1($lm->id);
                    $gs->gs_accmpl = $req->post("gs_accmpl");
                    $gs->gs_metric_01 = $req->post("gs_metric_01");
                    $gs->gs_metric_02 = $req->post("gs_metric_02");
                    $gs->gs_metric_03 = $req->post("gs_metric_03");
                    $gs->gs_metric_04 = $req->post("gs_metric_04");
                    $gs->gs_metric_05 = $req->post("gs_metric_05");
                    $gs->gs_metric_06 = $req->post("gs_metric_06");
                    $gs->gs_metric_07 = $req->post("gs_metric_07");
                    $gs->gs_target_01 = $req->post("gs_target_01");
                    $gs->gs_target_02 = $req->post("gs_target_02");
                    $gs->gs_target_03 = $req->post("gs_target_03");
                    $gs->gs_target_04 = $req->post("gs_target_04");
                    $gs->gs_target_05 = $req->post("gs_target_05");
                    $gs->gs_target_06 = $req->post("gs_target_06");
                    $gs->gs_target_07 = $req->post("gs_target_07");
                    $gs->gs_prev_01 = $req->post("gs_prev_01");
                    $gs->gs_prev_02 = $req->post("gs_prev_02");
                    $gs->gs_prev_03 = $req->post("gs_prev_03");
                    $gs->gs_prev_04 = $req->post("gs_prev_04");
                    $gs->gs_prev_05 = $req->post("gs_prev_05");
                    $gs->gs_prev_06 = $req->post("gs_prev_06");
                    $gs->gs_prev_07 = $req->post("gs_prev_07");
                    $gs->gs_curr_01 = $req->post("gs_curr_01");
                    $gs->gs_curr_02 = $req->post("gs_curr_02");
                    $gs->gs_curr_03 = $req->post("gs_curr_03");
                    $gs->gs_curr_04 = $req->post("gs_curr_04");
                    $gs->gs_curr_05 = $req->post("gs_curr_05");
                    $gs->gs_curr_06 = $req->post("gs_curr_06");
                    $gs->gs_curr_07 = $req->post("gs_curr_07");
                    $gs->gs_tip = $req->post("gs_tip");
                    $gs->gs_com = $req->post("gs_com");
                    $gs->save();
                    
                    $obj =[
                        "lnk_linkee_name"   => $req->post("lnk_linkee_name"), /* linkee name */
                        "lnk_linkee_email"  => $req->post("lnk_linkee_email"), /* linkee email */
                        "linker_name"       => Auth::user()->first_name." ".Auth::user()->last_name,
                        "linker_email"      => Auth::user()->email,
                        "gs_com_id"         => $gs->gs_com_id,
                        "hash"              => $gs->id
                    ];
                    Mail::to([$obj['lnk_linkee_email'],$obj['linker_email']])->queue(new \App\Mail\GoalSettingNotification($obj));
                }
            break;
        endswitch;
        
        return redirect('/coaching-session');
    }
    
}//end-class
