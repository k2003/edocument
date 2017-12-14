# ระบบงานสารบรรณอีเล็คทรอนิคส์ (E-Document)
ระบบงานสารบรรณอีเล็คทรอนิคส์ หรือ E-Document สร้างจากคชสารเว็บเฟรมเวิร์คโดยใช้โปรเจ็ค Personnel มาพัฒนาต่อ

จุดประสงค์หลักของโปรเจ็คนี้เพื่อทดสอบการพัฒนาโปรเจ็คในรูปแบบโมดูล ซึ่งตัวโปรเจ็คหลักจริงๆจะประกอบด้วยหลายๆโมดูล
นำมาใช้งานร่วมกันในระบบ ยกตัวอย่างเช่นโปรเจ็ค Repair ก็สามารถนำมาติดตั้งใช้งานร่วมกันกับโปรเจ็คนี้ได้

รายละเอียดเพิ่มเติม https://www.kotchasan.com/index.php?module=knowledge&id=88

## ความต้องการของระบบ
* PHP 5.3 ขึ้นไป
* ext-mbstring
* PDO Mysql

## การติดตั้งและการอัปเกรด
1. ให้อัปโหลดโค้ดทั้งหมดจากที่ดาวน์โหลด ขึ้นไปบน Server
2. ในกรณีที่มีการติดตั้งใหม่ ให้เรียกตัวติดตั้ง http://domain.tld/install/ และดำเนินการตามขั้นตอนการติดตั้งจนกว่าจะเสร็จสิ้น
3. ลบไดเร็คทอรี่ install/ ออก
4. ในกรณีเป็นการอัปเกรด ให้ตรวจสอบว่ามีฟิลด์ salt ในตาราง xxx_user (เปลี่ยน xxx เป็น prefix ของเว็บที่ใช้งานอยู่)  หรือไม่ ถ้าไม่มีให้เพิ่มฟิลด์ salt ชนิด VARCHAR(32) ลงในตาราง xxx_user และรันคำสั่ง SQL ```UPDATE xxx_user SET salt=username``` ที่ phpMyAdmin

## การใช้งาน
* เข้าระบบเป็นผู้ดูแลระบบ : ```admin@localhost``` และ Password : ```admin```
* เข้าระบบเป็นเจ้าหน้าที่ : ```demo@localhost``` และ Password : ```demo```

## ข้อตกลงการนำไปใช้งาน
* สามารถนำไปใช้งานส่วนตัวได้
* สามารถพัฒนาต่อยอดได้
* มีข้อสงสัยสามารถสอบถามได้ที่บอร์ดของคชสาร https://www.kotchasan.com
* ต้องการให้ผู้เขียนพัฒนาเพิ่มเติม ติดต่อผู้เขียนได้โดยตรง (อาจมีค่าใช้จ่าย)
* ผู้เขียนไม่รับผิดชอบข้อผิดพลาดใดๆในการใช้งาน
* ห้ามขาย ถ้าต้องการนำไปพัฒนาต่อเพื่อขายให้ติดต่อผู้เขียนก่อน (เพื่อบริจาค)

## หากต้องการสนับสนุนผู้เขียน สามารถบริจาคช่วยเหลือค่า Server ได้ที่
```
ธนาคาร กสิกรไทย สาขากาญจนบุรี
เลขที่บัญชี 221-2-78341-5
ชื่อบัญชี กรกฎ วิริยะ
```
