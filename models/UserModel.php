<?php
require_once dirname(__DIR__) .'/config.php';

class UserModel {
    private $app_conn;

    public function __construct($app_conn) {
        $this->app_conn = $app_conn;
    }

    // 抽取学校匹配逻辑为函数
    public function checkMatch($userInfo, $optionsInfo) {
        foreach ($optionsInfo as $key => $value) {
            if (is_array($value)) { // 如果是数组
                foreach ($value as $sub_key => $sub_value) {
                    if ($userInfo === $sub_key) {
                        return false; // 找到匹配
                    }
                }
            } else { // 如果是键值对
                if ($userInfo === $key) {
                    return false; // 找到匹配
                }
            }
        }
        return true; // 没有匹配
    }

    public function getUserByPhoneOrEmail($country_code = null, $phone_number = null, $email = null, $user_id = null) {
        $sql = "SELECT * FROM users WHERE ";
        $conditions = [];
        $params = [];
        $types = '';

        if ($country_code && $phone_number) {
            $conditions[] = "country_code = ? AND phone_number = ?";
            $params[] = $country_code;
            $params[] = $phone_number;
            $types .= 'ss';
        }
        
        if ($email) {
            $conditions[] = "email = ?";
            $params[] = $email;
            $types .= 's';
        }
        
        if ($user_id) {
            $conditions[] = "id = ?";
            $params[] = intval($user_id);
            $types .= 'i';
        }

        if (empty($conditions)) {
            return ['rs' => 0, 'message' => '未提供查询条件', "error"=> ""];
        }
        
        $sql .= implode(" OR ", $conditions);
        
        try {
            $stmt = $this->app_conn->prepare($sql);
            if ($stmt === false) {
                return ['rs' => -3, 'message' => '数据库准备语句失败', "error" => $this->app_conn->error];
            }

            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
            global $options;
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                $user["is_other_school"] = $this->checkMatch($user['school'], $options['schools']);
                $user["is_other_city"] = $this->checkMatch($user['city'], $options['cities']);
                $user["is_other_country"] = $this->checkMatch($user['country'], $options['countries']);
                $user["is_other_grade"] = $this->checkMatch($user['grade'], $options['grades']);
                $user["is_other_gender"] = $this->checkMatch($user['gender'], $options['genders']);

                return ['rs' => 1, 'data' => $user];
            } else {
                return ['rs' => -1, 'message' => '用户未找到', "error"=>""];
            }
        } catch (Exception $e) {
            return ['rs' => -2, 'message' => '数据库查询失败', "error" => $e->getMessage()];
        }
    }

} 