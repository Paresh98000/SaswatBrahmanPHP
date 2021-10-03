<?php

/**
 * @package Extension_UM_Paresh
 * @version 0.0.1
 */
/*
Plugin Name: Extension UM By Paresh
Plugin URI: 
Description: This is a extension to Ultimate Member Plugin, it needs to Ultimate Member shouldbe already installed.
Author: Paresh Sharma
Version: 0.0.1
Author URI: 
*/

function installtion(){
    require_once('install.php');
    install();
    // install_data();
}

register_activation_hook(__FILE__,"installtion");
register_activation_hook(__FILE__,"set_options_on_activation");
register_deactivation_hook(__FILE__,"unset_options_on_activation");

function ext_ps_admin_notice(){
    $flag_UM_installed = false;
    $admin_notice = '<div class="error">
                            <h3><b>Extension UM By Paresh</b></h3>
                            <p>Ultemate Memeber Plugin is not installed please install it to continue. You can download it from <a target="_blank" href="https://wordpress.org/plugins/ultimate-member/">wordpress-org</a>.</p>
                    </div>';

    if(class_exists('UM'))
        $flag_UM_installed = true;
    if(!$flag_UM_installed)
        echo $admin_notice;
}

function set_options_on_activation()
{
    try{
        UM()->options()->update('profile_tab_calendar',1);
        UM()->options()->update('profile_tab_service_provider',1);
        UM()->options()->update('profile_tab_schedule',1);
        UM()->options()->update('profile_tab_appointment',1);
    }
    catch(Exception $ex){
    }
}
add_action('admin_notices','ext_ps_admin_notice');

function unset_options_on_activation()
{
    try
    {
        UM()->options()->update('profile_tab_calendar',0);
        UM()->options()->update('profile_tab_service_provider',0);
        UM()->options()->update('profile_tab_schedule',0);
        UM()->options()->update('profile_tab_appointment',0);
    }
    catch(Exception $ex)
    {
        
    }
    
}

function extension1_um_paresh($arg)
{
    $flag_to_display_calendar = false;
    $flag_to_display_service = false;
    $flag_to_display_appointment = false;
    $flag_to_display_schedule = false;

    try{
        if(isset(UM()->user()->profile['role']))
            $role = UM()->user()->profile['role'];
        else return $arg;

        if(isset($role))
            if($role=='um_brahman')
                $flag_to_display_calendar = true;
            else if($role=='um_service_provider')
                $flag_to_display_service = true;
            else if($role=='um_yajman')
                $flag_to_display_appointment = true;

        // show schedule if he is brahman or service_provider
        $flag_to_display_schedule = $flag_to_display_service || $flag_to_display_calendar;

        if($flag_to_display_calendar)
            $arg['calendar'] = array(
                'name' => __( 'Calendar', 'ultimate-member' ),
                'icon' => 'um-faicon-calendar'
            );

        if($flag_to_display_service)
            $arg['service_provider'] = array(
                'name' => __( 'Service', 'ultimate-member' ),
                'icon' => 'um-icon-settings'
            );

        if($flag_to_display_schedule)
            $arg['schedule'] = array(
                'name' => __( 'Schedule', 'ultimate-member' ),
                'icon' => 'um-icon-ios-alarm'
            );

        if($flag_to_display_appointment)
            $arg['appointment'] = array(
                'name' => __( 'Appointment', 'ultimate-member' ),
                'icon' => 'um-icon-android-calendar'
            );
    }
    catch(Exception $ex){

    }
    return $arg;
}
add_action('um_profile_tabs','extension1_um_paresh');

// Calendar
function ext_ps_profile_calendar_content(){

    $Calendar_Festival_List = "ex_ps_calendar_festivals";
	$Relation_Calendar_Festival_List = "ex_ps_calendar_relation";
    
    $userid = 0;
    $tmp = UM()->user()->id;

    $is_myprofile = um_is_myprofile();

    if($tmp != null)
        if($tmp>0)
            $userid = $tmp;

    // Show content

    $view_table = "";

    $view_table .= "<div class='ex_ps_container'>
                        <select onchange='changeMonth();' value='0' name='guj_month_ui' id='ex_ps_guj_mon_ui' required>
                            <option value='1'>Chaitra</option>
                            <option value='2'>Vaisakh</option>
                            <option value='3'>Jyeshth</option>
                            <option value='4'>Ashad</option>
                            <option value='5'>Shravan</option>
                            <option value='6'>Bhadrapad</option>
                            <option value='7'>Aaso</option>
                            <option value='8'>Kartak</option>
                            <option value='9'>Magshar</option>
                            <option value='10'>Posh</option>
                            <option value='11'>Magh</option>
                            <option value='12'>Phagan</option>
                    </select> <br /> <br />";
    $view_table .= "<table><thead><tr>
                        <th>Festival Name</td>
                        <th>Festival Date</td>
                        <th>Festival Day</td>";
    if($is_myprofile)
        $view_table .= "<th>Action</th>";
    $view_table .= "</tr></thead><tbody>";
    // Fetch Data
    global $wpdb;
    
    $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}$Calendar_Festival_List c join {$wpdb->prefix}$Relation_Calendar_Festival_List r On c.id = r.festivalid Where r.userid = $userid", OBJECT );
    if(count($results)>0)
        foreach($results as $row){
            $f_d = date_create($row->festival_date);
            $f_d_str = date_format($f_d,'d-m-Y');
            $f_d_day = date_format($f_d,'l');
            $view_table .= "
                <tr class='ex_ps_row_$row->guj_month'>
                    <td id='ex_ps_col_f_n_$row->id'>{$row->festival_name}</td>
                    <td id='ex_ps_col_f_d_$row->id'>$f_d_str</td>
                    <td>$f_d_day</td>
                    <td id='ex_ps_col_g_m_$row->id' hidden>$row->guj_month</td>
                ";
                if($is_myprofile)
                    $view_table .= "<td><button id='ex_ps_edit_button' onclick='call_ex_ps_edit($row->id);'>Edit</button></td>";
                $view_table .= "</tr>";
    }
    else
    {
        $colspan = 3;
        if($is_myprofile)
            $colspan = 4;
        $view_table .= "
            <tr>
                <td colspan='$colspan'>No Data Found</td>
            </tr>
        ";
    }
    
    $view_table .= "</tbody></table></div>";

    $script = '<script>
                    ui_disabled = false;
                    ex_ps_submit = document.getElementById("ex_ps_submit");
                    
                    jQuery(ex_ps_submit).on("click",function(e) {
                        if(ui_disabled)
                        {
                            alert("Please wait processing");
                            return;
                        }

                        ex_ps_f_n = document.getElementById("ex_ps_f_n").value;
                        ex_ps_f_d = document.getElementById("ex_ps_f_d").value;
                        ex_ps_guj_mon = document.getElementById("ex_ps_guj_mon").value;
                        try{ex_ps_f_c = document.getElementById("ex_ps_f_c").value;}catch(ex){console.log(ex.toString()); ex_ps_f_c="";}
                        ex_ps_userid = ###USER_ID###;
                        ex_ps_festival_id = document.getElementById("ex_ps_f_id").value;
                        error = "";

                        if(ex_ps_f_n == "")
                            error = "Please enter valid festval name";

                        if(ex_ps_f_d == "")
                            error += "\nPlease enter valid festival date";

                        //if(ex_ps_f_c == "")
                        //    error += "\nPlease enter valid festival category";

                        if(ex_ps_guj_mon == "")
                            error += "\nPlease enter valid gujarati month";

                        if(ex_ps_userid == 0)
                            error = "Please Login";

                        if(ex_ps_festival_id == "")
                            ex_ps_festival_id = "0";

                        if(error != ""){
                            alert(error);
                            return;
                        }
                        data_ex_ps = {"festival_name":ex_ps_f_n,"festival_date":ex_ps_f_d,"ex_ps_guj_mon":ex_ps_guj_mon,"festival_category":ex_ps_f_c,"ex_ps_userid":ex_ps_userid,"festival_id":ex_ps_festival_id};
                        wp.ajax.send( "ex_ps_js_update_calendar_list", {
                            data: data_ex_ps,
                            success: function (data) {
                                ui_disabled = false;
                                ex_ps_f_id = document.getElementById("ex_ps_f_id");
                                if(ex_ps_f_id.value == "0")
                                {
                                    document.getElementById("ex_ps_msg_success").style.display = "inline";
                                    document.getElementById("ex_ps_msg_update_success").style.display = "none";
                                }
                                else
                                {
                                    document.getElementById("ex_ps_msg_success").style.display = "none";
                                    document.getElementById("ex_ps_msg_update_success").style.display = "inline";
                                }
                                ex_ps_f_id.value = "0";
                                document.getElementById("ex_ps_msg_error").style.display ="none";
                                ex_ps_submit = document.getElementById("ex_ps_submit");
                                ex_ps_submit.innerText = "Add";
                            },
                            error: function (data) {
                                ui_disabled = false;
                                document.getElementById("ex_ps_msg_success").style.display = "none";
                                document.getElementById("ex_ps_msg_error").style.display ="inline";
                                document.getElementById("ex_ps_msg_update_success").style.display = "none";
                            }
                        });
                        ui_disabled = true;
                    });
                    function call_ex_ps_edit(arg){
                        ex_ps_f_n = document.getElementById("ex_ps_f_n");
                        ex_ps_f_d = document.getElementById("ex_ps_f_d");
                        ex_ps_guj_mon = document.getElementById("ex_ps_guj_mon");
                        ex_ps_festival_id = document.getElementById("ex_ps_f_id");

                        fest_name = document.getElementById("ex_ps_col_f_n_"+arg).innerText;
                        fest_date = document.getElementById("ex_ps_col_f_d_"+arg).innerText;
                        fest_guj_month = document.getElementById("ex_ps_col_g_m_"+arg).innerText;

                        ex_ps_f_n.value = fest_name;
                        ex_ps_f_d.value = fest_date.substr(6,4) + "-" + fest_date.substr(3,2) + "-" + fest_date.substr(0,2);
                        ex_ps_festival_id.value = arg + "";
                        ex_ps_guj_mon.value = fest_guj_month;
                        
                        ex_ps_submit = document.getElementById("ex_ps_submit");
                        ex_ps_submit.innerText = "Update";
                        document.getElementById("ex_ps_msg_success").style.display = "none";
                        document.getElementById("ex_ps_msg_error").style.display ="none";

                        ex_ps_f_n.focus();
                    }

                    function changeMonth(){
                        month = document.getElementById("ex_ps_guj_mon_ui").value;
                        style = "none";
                        for(i=1;i<=12;i++){
                            if(i==month)
                                style = "table-row";
                            else
                                style = "none";

                            elements = document.getElementsByClassName("ex_ps_row_"+i);

                            for(j=0;j<elements.length;j++){
                                elements[j].style.display = style;
                            }
                        }
                    }

                    function initialize(){
                        for(i=1;i<=12;i++){
                            elements = document.getElementsByClassName("ex_ps_row_"+i);

                            if(elements.length>0)
                            {
                                document.getElementById("ex_ps_guj_mon_ui").value = i;
                                break;
                            }
                        }
                        changeMonth();
                    }
                    jQuery(document).ready(function() {initialize();});
                </script>
                ';

    // update variables
    $script = str_replace('###USER_ID###',$userid,$script);

    if(!$is_myprofile)
    {   
        echo '<h2>Festival List</h2>'.$view_table.$script; 
        return;
    }

    $output_str = '
        <style>
            .ex_ps_container{
                font-family:Verdana;
                text-size:30px;
                padding-top:10px;
            }

            .ex_ps_container input[type=text]{
                width:200px;
                display:inline !important;
            }

            .ex_ps_container button{
                font-size:15px;
            }

            .ex_ps_user_container #ex_ps_msg_success,#ex_ps_msg_update_success{
                color:green;
            }

            .ex_ps_user_container #ex_ps_msg_error{
                color:red;
            }
        </style>
        <div class="ex_ps_container" style="border:solid black 1px; padding:10px;">
            <div>
                <h3>Edit</h3>
                <input type="text" name="festival_name" id="ex_ps_f_n" placeholder="enter festivalname" required/>
                <input type="date" name="festival_date" id="ex_ps_f_d" required/>
                <select name="guj_month" id="ex_ps_guj_mon" required>
                    <option value="1">Chaitra</option>
                    <option value="2">Vaisakh</option>
                    <option value="3">Jyeshth</option>
                    <option value="4">Ashad</option>
                    <option value="5">Shravan</option>
                    <option value="6">Bhadrapad</option>
                    <option value="7">Aaso</option>
                    <option value="8">Kartak</option>
                    <option value="9">Magshar</option>
                    <option value="10">Posh</option>
                    <option value="11">Magh</option>
                    <option value="12">Phagan</option>
                </select>
                <!-- <input type="text" name="festival_category" id="ex_ps_f_c" placeholder="category (Shradpaksh)" hidden/> -->
                <input style="display:none !important;" type="text" name="festival_id" value="0" id="ex_ps_f_id"/>
            </div>
            <div class="ex_ps_user_container">
                <button name="festival_submit" id="ex_ps_submit" >Add</button>
                <span id="ex_ps_msg_success" hidden>Added Successfully. <a href=""><i><u>Refresh Page</u></i></a></span>
                <span id="ex_ps_msg_update_success" hidden>Updated Successfully. <a href=""><i><u>Refresh Page</u></i></a></span>
                <span id="ex_ps_msg_error" hidden>Something Went Wrong!</span>
            </div>
        </div>';

    echo '<h2>Festival List</h2>'.$output_str.$view_table.$script;
}
add_action('um_profile_content_calendar','ext_ps_profile_calendar_content');

// --AJAX
function ex_ps_js_update_calendar_list($args){
    // error wp_send_json_error( __( 'Wrong request.', 'ultimate-member' ) );
    // success wp_send_json_success( array( 'answer' => $answer ) );
    $userid = $festival_guj_month = $festival_date = $festival_name = $festival_id = "" ;
    $festival_category = "";

    $is_update = false;

    if(isset($_REQUEST["festival_name"]))
        $festival_name = $_REQUEST['festival_name'];

    if(isset($_REQUEST["festival_date"]))
        $festival_date = $_REQUEST['festival_date'];

    if(isset($_REQUEST["ex_ps_guj_mon"]))
        $festival_guj_month = $_REQUEST['ex_ps_guj_mon'];

    if(isset($_REQUEST['ex_ps_userid']))
        $userid = $_REQUEST['ex_ps_userid'];

    if(isset($_REQUEST['festival_id']))
        $festival_id = $_REQUEST['festival_id'];

    if(isset($_REQUEST['festival_category']))
        $festival_category = $_REQUEST['festival_category'];

    // Festival Category
    if(isset($_REQUEST['ex_ps_f_c']))
        $festival_category = $_REQUEST['ex_ps_f_c'];

    $is_update = strlen(trim($festival_id)) > 0 && trim($festival_id) != '0';

    $Calendar_Festival_List = "ex_ps_calendar_festivals";
    $Relation_Calendar_Festival_List = "ex_ps_calendar_relation";

    global $wpdb;

    $table_name = $wpdb->prefix . $Calendar_Festival_List;

    if($is_update)
    {
        $flag = $wpdb->update(
            $table_name,
            array(
                'festival_date' => $festival_date, 
                'festival_name' => $festival_name, 
                'guj_month' => $festival_guj_month,
                'festival_category' => $festival_category
            ),
            array('id'=>$festival_id),
            array('%s','%s','%d'),
            array('%d')
        );
        if($flag == false)
            wp_send_json_error( array( 'error'=>'festival not updated','success' => false) );    
        wp_send_json_success( array( 'success' => true));
    }

    $flag = $wpdb->insert( 
        $table_name,
        array(
            'festival_date' => $festival_date, 
            'festival_name' => $festival_name, 
            'guj_month' => $festival_guj_month, 
            'festival_category' => $festival_category
        ),
        array('%s','%s','%d','%s')
    );

    if(!$flag)
        wp_send_json_error( array( 'error'=>'festival not saved','success' => false) );    

    $festival_id = $wpdb->insert_id;

    $table_name = $wpdb->prefix . $Relation_Calendar_Festival_List;

    $flag = $wpdb->insert( 
        $table_name,
        array(
            'festivalid' => $festival_id, 
            'userid' => $userid, 
        )
    );

    if(!$flag)
        wp_send_json_error( array( 'error'=>'festival not saved with user','success' => false) );

    wp_send_json_success( array( 'success' => true));
}
add_action('wp_ajax_ex_ps_js_update_calendar_list','ex_ps_js_update_calendar_list');

// Appointment
function ext_ps_profile_appointment_content(){
    echo '<h2>Appointments</h2>';
}
add_action('um_profile_content_appointment','ext_ps_profile_appointment_content');

// --AJAX
function ex_ps_js_update_appointment_list($args){
    // error wp_send_json_error( __( 'Wrong request.', 'ultimate-member' ) );
    // success wp_send_json_success( array( 'answer' => $answer ) );
    $flag=true;

    if(!$flag)
        wp_send_json_error( array( 'error'=>'appointment error','success' => false) );

    wp_send_json_success( array( 'success' => true));
}
add_action('wp_ajax_ex_ps_js_update_appointment_list','ex_ps_js_update_appointment_list');

// Service Provider
function ext_ps_profile_service_provider_content(){

    echo '<div><h3>Services</h3></div>';

    $Service = 'ex_ps_service'; // service info
	$Service_Type = 'ex_ps_service_type';
	$Relation_Service = "ex_ps_service_relation"; // author of service

    $is_myprofile = um_is_myprofile();

    $style = '

            <style>
                div.ex_ps_col{
                    float:left;
                    min-width:200px;
                    min-height:150px;
                    background:lightgray;
                    margin:1px;
                    padding:5px;
                    border:solid black 1px;
                    border-radius:5px;
                }

                div.ex_ps_service{
                    padding:5px;
                    margin:3px;
                    border: solid black 1px;
                }

                div.ex_ps_service input[type=text]{
                    width:auto;
                }

                div.ex_ps_card_box{
                    padding:3px;
                    margin:3px;
                }
                span#ex_ps_service_success_add{
                    color:green;
                }
                span#ex_ps_service_success_update{
                    color:green;
                }
                span#ex_ps_service_success_error{
                    color:red;
                }
                #ex_ps_service_id{
                    display: none !important;
                }
            </style>

                ';

    $card_data = "";
    
    global $wpdb;

    // Select Query From Database
    $results = $wpdb->get_results( "
        SELECT 
        s.id As 'id',
        s.service_title As 'service_title',
        s.service_details As 'service_details',
        s.service_type As 'service_type',
        t.service_type_name As 'service_type_name' 
        FROM 
        {$wpdb->prefix}$Service s 
        JOIN {$wpdb->prefix}$Service_Type t 
        ON s.service_type = t.id
                                    ",
                OBJECT );
    
    foreach($results as $row){
        $card_data .= "
        
        <div class='ex_ps_col'>
            <h5 id='ex_ps_col_s_n_$row->id'>$row->service_title</h5>
            <h7 id='ex_ps_col_s_t_$row->id'>$row->service_type_name</h7>
            <p id='ex_ps_col_s_d_$row->id'>$row->service_details</p>
            <span id='ex_ps_col_s_t_i_$row->id' hidden>$row->service_type</span>
            <button id='ex_ps_service_edit' onclick='ex_ps_editService($row->id);'>Edit</button>
        </div>                  

                        ";
    }

    // view of other users
    $client_view = "

    <div class='ex_ps_card_box'>
        $card_data
    </div>

                    ";
    
    $s_type_options = "";

    $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}$Service_Type", OBJECT );

    foreach($results as $row){
        $s_type_options .= "<option value='$row->id'>$row->service_type_name</option>\n";
    }

    // view for owner user
    $owner_view = "
    
    <div class='ex_ps_service'>
        <h3>Edit</h3>
        <div>
            <span id='ex_ps_service_success_add' hidden> Service Added Successfully <a href=''> <i><u> Refresh page </u></i> </a> </span>
            <span id='ex_ps_service_success_update' hidden> Service Updated Successfully <a href=''> <i><u> Refresh page </u></i> </a> </span>
            <span id='ex_ps_service_error' hidden> Service Added Successfully </span>
        </div>
        <div>
            <br />
            <input type='text' name='ex_ps_service_title' id='ex_ps_service_title' placeholder='Title' required />
            <input type='number' name='ex_ps_service_id' id='ex_ps_service_id' />
            <br />
            <select name='ex_ps_service_type' id='ex_ps_service_type' required>
                <option value='0'>Service Type</option>
                $s_type_options
            </select>
            <br /> <br />
            <textarea name='ex_ps_service_description' id='ex_ps_service_description' placeholder='Description'></textarea>
        </div>
        <div>
            <br />
            <button onclick='ex_ps_service_submit();' id='ex_ps_service_submit'>Add</button>
        </div>
    </div>
                    ";

    $script = "
    
    <script>
        function ex_ps_service_submit(){
            ex_ps_service_name = document.getElementById('ex_ps_service_title').value;
            ex_ps_service_type = document.getElementById('ex_ps_service_type').value;
            ex_ps_service_desc = document.getElementById('ex_ps_service_description').value;
            ex_ps_service_id = document.getElementById('ex_ps_service_id').value;
            error = '';

            if(ex_ps_service_name == '')
                error += 'Enter valid title\\n';

            if(ex_ps_service_type == '' || ex_ps_service_type == '0')
                error += 'Enter valid service type\\n';

            if(ex_ps_service_desc == '')
                error += 'Enter valid Service Description\\n';

            if(error != ''){
                alert('Please fill up form correctly\\n'+error);
                return;
            }

            data_ex_ps = {'service_title':ex_ps_service_name,'service_type':ex_ps_service_type,'service_description':ex_ps_service_desc,'service_id':ex_ps_service_id};
            wp.ajax.send( 'ex_ps_js_update_service_list', {
                data: data_ex_ps,
                success: function (data) {
                    //ui_disabled = false;
                    ex_ps_f_id = document.getElementById('ex_ps_service_id');
                    if(ex_ps_f_id.value == 0)
                    {
                        document.getElementById('ex_ps_service_success_add').style.display = 'inline';
                        document.getElementById('ex_ps_msg_update_success').style.display = 'none';
                    }
                    else
                    {
                        document.getElementById('ex_ps_service_success_add').style.display = 'none';
                        document.getElementById('ex_ps_service_success_update').style.display = 'inline';
                        document.getElementById('ex_ps_service_submit').innerText = 'Add';
                        ex_ps_f_id.value = 0;
                    }
                    document.getElementById('ex_ps_msg_error').style.display ='none';
                },
                error: function (data) {
                    //ui_disabled = false;
                    document.getElementById('ex_ps_service_success_add').style.display = 'none';
                    document.getElementById('ex_ps_service_success_error').style.display ='inline';
                    document.getElementById('ex_ps_service_success_update').style.display = 'none';
                }
            });
        }

        function ex_ps_editService(arg){
            ex_ps_service_name = document.getElementById('ex_ps_service_title');
            ex_ps_service_type = document.getElementById('ex_ps_service_type');
            ex_ps_service_desc = document.getElementById('ex_ps_service_description');
            ex_ps_service_id = document.getElementById('ex_ps_service_id');

            name = document.getElementById('ex_ps_col_s_n_'+arg).innerText;
            service_type = document.getElementById('ex_ps_col_s_t_i_'+arg).innerText;
            service_desc = document.getElementById('ex_ps_col_s_d_'+arg).innerText;
            service_id = arg;

            ex_ps_service_name.value = name;
            ex_ps_service_type.value = service_type;
            ex_ps_service_desc.value = service_desc;
            ex_ps_service_id.value = service_id;

            button = document.getElementById('ex_ps_service_submit');
            button.innerText = 'Update';
        }

    </script>

                ";

    echo $style;

    if($is_myprofile)
        echo $owner_view.$client_view;
    else
        echo $client_view;

    echo $script;
}
add_action('um_profile_content_service_provider','ext_ps_profile_service_provider_content');

// --AJAX
function ex_ps_js_update_service_list($args){
    
    $Service = 'ex_ps_service'; // service info
	$Service_Type = 'ex_ps_service_type';
	$Relation_Service = "ex_ps_service_relation"; // author of service

    $flag=false;

    $is_update = false;

    $service_id = isset($_REQUEST['service_id']) ? $_REQUEST['service_id'] : 0;
    $service_title = $_REQUEST['service_title'];
    $service_type = $_REQUEST['service_type'];
    $service_desc = $_REQUEST['service_description'];

    $is_update = $service_id != 0 && $service_id != "";

    global $wpdb;

    $table_name = $wpdb->prefix . $Service;

    if($is_update)
    {
        $flag = $wpdb->update(
            $table_name,
            array(
                'service_title' => $service_title, 
                'service_details' => $service_desc, 
                'service_type' => $service_type,
            ),
            array('id'=>$service_id),
            array('%s','%s','%d'),
            array('%d')
        );
        if($flag == false)
            wp_send_json_error( array( 'error'=>'service not updated','success' => false,'update'=>$is_update) );    
        wp_send_json_success( array( 'success' => true,'update'=>$is_update));
    }
    else
    {
        $flag = $wpdb->insert( 
            $table_name,
            array(
                'service_title' => $service_title, 
                'service_type' => $service_type, 
                'service_details' => $service_desc, 
            ),
            array('%s','%d','%s')
        );

        if(!$flag)
            wp_send_json_error( array( 'error'=>"service list error",'success' => false,'update'=>$is_update) );

        wp_send_json_success( array( 'success' => true,'update'=>$is_update,'update'=>$is_update));
    }
}
add_action('wp_ajax_ex_ps_js_update_service_list','ex_ps_js_update_service_list');

// Schedule
function ext_ps_profile_schedule_content(){
    echo '<h2>Schedule</h2>';
}
add_action('um_profile_content_schedule','ext_ps_profile_schedule_content');

// -- AJAX
function ex_ps_js_update_schedule_list($args){
    // error wp_send_json_error( __( 'Wrong request.', 'ultimate-member' ) );
    // success wp_send_json_success( array( 'answer' => $answer ) );
    $flag=true;

    if(!$flag)
        wp_send_json_error( array( 'error'=>'schedule list error','success' => false) );

    wp_send_json_success( array( 'success' => true));
}
add_action('wp_ajax_ex_ps_js_update_schedule_list','ex_ps_js_update_schedule_list');

function ex_ps_mobile_login(){
    wp_send_json_success( array( 'success' => true));
}
add_action('wp_ajax_nopriv_ex_ps_mobile_login','ex_ps_mobile_login');

// Admin Page
function ext_ps_my_admin_page_contents() {
    ?>
        <h1>
            <?php esc_html_e( 'Welcome to my custom admin page.', 'my-plugin-textdomain' ); ?>
        </h1>
    <?php
}

function ext_ps_my_admin_menu() {
    add_menu_page(
        'Ext PS', // Page Title 
        'Ext PS', // Menu Title
        'manage_options', // capability
        'Ext-PS', // menu-slug
        'ext_ps_my_admin_page_contents',
        'dashicons-schedule',
        3
    );
}
add_action( 'admin_menu', 'ext_ps_my_admin_menu' );

// allow origin in cross orgin
function ex_ps_allow_origin($origin,$allowed_origin){
    return true;
}
add_action('allowed_http_origin','ex_ps_allow_origin',10,2);

?>