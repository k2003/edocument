<?php
/**
 * @filesource modules/index/models/editprofile.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Editprofile;

use \Kotchasan\Http\Request;
use \Gcms\Login;
use \Kotchasan\Language;

/**
 * module=editprofile
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{

  /**
   * อ่านข้อมูลสมาชิกที่ $id
   *
   * @param int $id
   * @return array|bool คืนค่าข้อมูล array ไม่พบคืนค่า false
   */
  public static function get($id)
  {
    if (!empty($id)) {
      // query ข้อมูลที่เลือก
      $user = static::create()->db()->createQuery()
        ->from('user')
        ->where(array('id', $id))
        ->toArray()
        ->first();
      if ($user) {
        // permission
        $user['permission'] = empty($user['permission']) ? array() : trim(explode(',', $user['permission']), " \t\n\r\0\x0B,");
        return $user;
      }
    }
    return false;
  }

  /**
   * บันทึกข้อมูล (editprofile.php)
   *
   * @param Request $request
   */
  public function submit(Request $request)
  {
    $ret = array();
    // session, token, สมาชิก และไม่ใช่สมาชิกตัวอย่าง
    if ($request->initSession() && $request->isSafe() && $login = Login::isMember()) {
      if (Login::notDemoMode($login)) {
        // รับค่าจากการ POST
        $save = array(
          'name' => $request->post('register_name')->topic(),
          'sex' => $request->post('register_sex')->topic(),
          'phone' => $request->post('register_phone')->topic(),
          'id_card' => $request->post('register_id_card')->number(),
          'address' => $request->post('register_address')->topic(),
          'provinceID' => $request->post('register_provinceID')->number(),
          'zipcode' => $request->post('register_zipcode')->number(),
          'status' => $request->post('register_status')->toInt()
        );
        $permission = $request->post('register_permission', array())->topic();
        // Model
        $model = new \Kotchasan\Model;
        // ชื่อตาราง user
        $table_user = $model->getTableName('user');
        // database connection
        $db = $model->db();
        // ตรวจสอบค่าที่ส่งมา
        $user = self::get($request->post('register_id')->toInt());
        if ($user) {
          // ตัวเอง ไม่สามารถอัปเดท status ได้
          if ($login['id'] == $user['id']) {
            unset($save['status']);
          }
          if (Login::isAdmin()) {
            // แอดมิน อัปเดท permission ได้
            $save['permission'] = empty($permission) ? '' : ','.implode(',', $permission).',';
          } elseif ($login['id'] != $user['id']) {
            // ไม่ใช่แอดมินแก้ไขได้แค่ตัวเองเท่านั้น
            $user = null;
          } else {
            // ไม่ใช่แอดมินและไม่ใช่ตัวเอง ไม่สามารถอัปเดท status ได้
            unset($save['status']);
          }
        }
        if ($user) {
          $save['username'] = $request->post('register_username', $user['username'])->username();
          if ($user['active'] == 1 && $save['username'] == '') {
            // สามารถเข้าระบบได้ และ ไม่ได้กรอก username
            $ret['ret_register_username'] = 'Please fill in';
          } elseif ($save['name'] == '') {
            // ไม่ได้กรอก ชื่อ
            $ret['ret_register_name'] = 'Please fill in';
          } else {
            // ตรวจสอบค่าที่ส่งมา
            $requirePassword = false;
            // ตรวจสอบ username ซ้ำ
            if ($save['username'] != '') {
              $search = $db->first($table_user, array('username', $save['username']));
              if ($search !== false && $user['id'] != $search->id) {
                // มี username อยู่ก่อนแล้ว
                $ret['ret_register_username'] = Language::replace('This :name already exist', array(':name' => Language::get('Email')));
              } else {
                $requirePassword = $user['username'] !== $save['username'];
              }
              // password
              $password = $request->post('register_password')->password();
              $repassword = $request->post('register_repassword')->password();
            }
            if (!empty($password) || !empty($repassword)) {
              if (mb_strlen($password) < 4) {
                // รหัสผ่านต้องไม่น้อยกว่า 4 ตัวอักษร
                $ret['ret_register_password'] = 'this';
              } elseif ($repassword != $password) {
                // ถ้าต้องการเปลี่ยนรหัสผ่าน กรุณากรอกรหัสผ่านสองช่องให้ตรงกัน
                $ret['ret_register_repassword'] = 'this';
              } else {
                $requirePassword = false;
              }
            }
            // มีการเปลี่ยน username ต้องการรหัสผ่าน
            if (empty($ret) && $requirePassword) {
              $ret['ret_register_password'] = 'this';
            }
            // บันทึก
            if (empty($ret)) {
              // แก้ไข
              if (!empty($password)) {
                $save['salt'] = uniqid();
                $save['password'] = sha1($password.$save['salt']);
              }
              // แก้ไข
              $db->update($table_user, $user['id'], $save);
              if ($login['id'] == $user['id']) {
                // ตัวเอง อัปเดทข้อมูลการ login
                $save['permission'] = $permission;
                $save['password'] = $password;
                $_SESSION['login'] = \Kotchasan\ArrayTool::merge($_SESSION['login'], $save);
                // reload หน้าเว็บ
                $ret['location'] = 'reload';
              } else {
                // ไปหน้าเดิม แสดงรายการ
                $ret['location'] = $request->getUri()->postBack('index.php', array('module' => 'member', 'id' => null));
              }
              // คืนค่า
              $ret['alert'] = Language::get('Saved successfully');
              // เคลียร์
              $request->removeToken();
            }
          }
        } else {
          // ไม่พบข้อมูลที่แก้ไข หรือ ไม่มีสิทธิ์
          $ret['alert'] = Language::get('not a registered user');
        }
      }
    }
    if (empty($ret)) {
      $ret['alert'] = Language::get('Unable to complete the transaction');
    }
    // คืนค่าเป็น JSON
    echo json_encode($ret);
  }
}