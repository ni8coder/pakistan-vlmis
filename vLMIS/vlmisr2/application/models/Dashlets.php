<?php

/**
 * Model_Dashlets
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    Logistics Management Information System for Vaccines
 * @subpackage Dashboards
 * @author     Ajmal Hussain <ajmaleyetii@gmail.com>
 * @author     Muhammad Waqas Azeem <waqaszaeemcs06@gmail.com>
 * @version    1
 */
class Model_Dashlets extends Model_Base {

    private $_table;

    public function __construct() {
        parent::__construct();
    }

    public function stockStatus() {
        switch ($this->form_values['level']) {
            case 1:
                $where = ' stakeholders.geo_level_id = 1 ';
                break;
            case 2:
                $where = ' stakeholders.geo_level_id = 2 ';
                $where .= " AND warehouses.location_id = '" . $this->form_values['prov_id'] . "'";
                break;
            case 6:
                $where = ' stakeholders.geo_level_id = 4 ';
                $where .= " AND warehouses.location_id = '" . $this->form_values['loc_id'] . "'";
                break;
        }

        $str_sql = "SELECT
                            item_pack_sizes.item_name,
                            Sum(warehouses_data.opening_balance) AS OB,
                            Sum(warehouses_data.received_balance) AS Rcv,
                            Sum(warehouses_data.issue_balance) AS Issue,
                            Sum(warehouses_data.closing_balance) AS CB
                    FROM
                    warehouses
                    INNER JOIN stakeholders ON warehouses.stakeholder_office_id = stakeholders.pk_id
                    INNER JOIN warehouses_data ON warehouses_data.warehouse_id = warehouses.pk_id
                    INNER JOIN item_pack_sizes ON warehouses_data.item_pack_size_id = item_pack_sizes.pk_id
                    WHERE
                    $where AND DATE_FORMAT(warehouses_data.reporting_start_date, '%Y-%m') = '" . $this->form_values['date'] . "'
                    and warehouses.status = 1   
                    GROUP BY
                    warehouses_data.item_pack_size_id";
        $row = $this->_em->getConnection()->prepare($str_sql);
        $row->execute();
        return $row->fetchAll();
    }

    public function stockIssues() {
        $date = $this->form_values['date'];
        $str_sql = "SELECT
                    item_pack_sizes.item_name,
                    SUM(ABS(stock_detail.quantity)) AS Qty,
                    warehouses.warehouse_name,
                    stock_master.transaction_date
            FROM
                    stock_batch
            INNER JOIN stock_detail ON stock_detail.stock_batch_id = stock_batch.pk_id
            INNER JOIN stock_master ON stock_detail.stock_master_id = stock_master.pk_id
            INNER JOIN item_pack_sizes ON stock_batch.item_pack_size_id = item_pack_sizes.pk_id
            INNER JOIN warehouses ON stock_master.to_warehouse_id = warehouses.pk_id
            INNER JOIN stakeholders ON warehouses.stakeholder_office_id = stakeholders.pk_id
            WHERE
            stock_master.transaction_type_id = 2
            and warehouses.status = 1
            AND DATE_FORMAT(stock_master.transaction_date, '%Y-%m') = $date 
            AND stakeholders.geo_level_id = 2
            GROUP BY
                    stock_batch.item_pack_size_id,
                    warehouses.pk_id";
        $row = $this->_em->getConnection()->prepare($str_sql);
        $row->execute();
        return $row->fetchAll();
    }

    public function reportingRate() {
        $months = $this->form_values;
        $loc_id = $this->_identity->getLocationId();
        $data_arr = array();
        $sub_sql = "SELECT
                            warehouses.pk_id,
                            warehouses.warehouse_name 
                            FROM
                                    warehouse_users 
                            INNER JOIN warehouses  ON warehouse_users.warehouse_id = warehouses.pk_id
                            INNER JOIN users  ON warehouse_users.user_id = users.pk_id
                            WHERE
                            users.pk_id = " . $this->_user_id . " and warehouses.status = 1";
        $row_sub = $this->_em->getConnection()->prepare($sub_sql);
        $row_sub->execute();
        $sub_sub = $row_sub->fetchAll();
        foreach ($sub_sub as $sub_rs) {

            foreach ($months as $months_rs) {
                $str_sql_sub = "SELECT
                       warehouses_data.warehouse_id
                       FROM
                       warehouses_data 
                       WHERE
                       DATE_FORMAT(warehouses_data.reporting_start_date, '%Y-%m') = '$months_rs'
                       AND warehouses_data.warehouse_id  = " . $sub_rs['pk_id'] . "
                       GROUP BY
                       warehouses_data.warehouse_id
                       UNION 
                       SELECT
                       hf_data_master.warehouse_id
                       FROM
                       hf_data_master 
                       WHERE
                       DATE_FORMAT(hf_data_master.reporting_start_date, '%Y-%m') = '$months_rs'
                       AND hf_data_master.warehouse_id  = " . $sub_rs['pk_id'] . "
                       GROUP BY
                       hf_data_master.warehouse_id";

                //echo $str_sql_sub."<br>";
                $row_r = $this->_em->getConnection()->prepare($str_sql_sub);
                $row_r->execute();
                //echo "<hr>".count($row_r->fetchAll())."<hr>";
                if (count($row_r->fetchAll()) > 0) {
                    $data_arr[$sub_rs['warehouse_name']][$sub_rs['pk_id']][$months_rs] = 'R';
                } else {
                    $data_arr[$sub_rs['warehouse_name']][$sub_rs['pk_id']][$months_rs] = 'NR';
                }
            }
        }
        return $data_arr;
        //exit;
//         $str_sql = "SELECT
//                            B.pk_id,
//                            B.warehouse_name AS HF,
//                            COALESCE(A.month1, NULL, 'NR') AS month1,
//                            COALESCE(A.month2, NULL, 'NR') AS month2,
//                            COALESCE(A.month3, NULL, 'NR') AS month3,
//                            COALESCE(A.month4, NULL, 'NR') AS month4,
//                            COALESCE(A.month5, NULL, 'NR') AS month5,
//                            COALESCE(A.month6, NULL, 'NR') AS month6
//                    FROM
//                            (
//                                    SELECT
//                                            warehouses.pk_id,
//                                            warehouses.warehouse_name,
//                                            DATE_FORMAT(warehouses_data.reporting_start_date, '%Y-%m'),
//                                            IF(DATE_FORMAT(warehouses_data.reporting_start_date, '%Y-%m') = '" . $months[5] . "', 'R', 'NR') AS month1,
//                                            IF(DATE_FORMAT(warehouses_data.reporting_start_date, '%Y-%m') = '" . $months[4] . "', 'R', 'NR') AS month2,
//                                            IF(DATE_FORMAT(warehouses_data.reporting_start_date, '%Y-%m') = '" . $months[3] . "', 'R', 'NR') AS month3,
//                                            IF(DATE_FORMAT(warehouses_data.reporting_start_date, '%Y-%m') = '" . $months[2] . "', 'R', 'NR') AS month4,
//                                            IF(DATE_FORMAT(warehouses_data.reporting_start_date, '%Y-%m') = '" . $months[1] . "', 'R', 'NR') AS month5,
//                                            IF(DATE_FORMAT(warehouses_data.reporting_start_date, '%Y-%m') = '" . $months[0] . "', 'R', 'NR') AS month6
//                                    FROM
//                                            warehouses
//                                    INNER JOIN stakeholders ON stakeholders.pk_id = warehouses.stakeholder_office_id
//                                    INNER JOIN warehouses_data ON warehouses.pk_id = warehouses_data.warehouse_id
//                                    WHERE
//                                            stakeholders.geo_level_id = 6
//                                    AND warehouses.location_id = $loc_id
//                                    AND warehouses_data.reporting_start_date BETWEEN '" . $months[5] . "-01'
//                                    AND '" . $months[0] . "-31'
//                                    GROUP BY
//                                            warehouses_data.warehouse_id
//                            ) A
//                    RIGHT JOIN (
//                            SELECT
//                            warehouses.pk_id,
//                            warehouses.warehouse_name 
//                            FROM
//                                    warehouse_users 
//                            INNER JOIN warehouses  ON warehouse_users.warehouse_id = warehouses.pk_id
//                            INNER JOIN users  ON warehouse_users.user_id = users.pk_id
//                            WHERE
//                       users.pk_id = " . $this->_user_id . "
//                    ) B ON A.pk_id = B.pk_id";
//
//        $row = $this->_em->getConnection()->prepare($str_sql);
//        $row->execute();
//        return $row->fetchAll();
    }

    public function campaignVaccines() {
        $loc_id = $this->form_values['loc_id'];
        $prov_id = $this->form_values['prov_id'];
        $camp_id = $this->form_values['camp'];
        $level = $this->form_values['level'];

        if (empty($camp_id)) {
            return false;
        }

        switch ($level) {
            case 1:
                $where = "";
                break;
            case 2:
                $where = "and locations.province_id=" . $prov_id;
                break;
            case 6:
                $where = "and locations.pk_id=" . $loc_id;
                break;
        }

        $str_sql = "SELECT
            locations.location_name,item_pack_sizes.item_name,
            round(ifnull(Sum(campaign_targets.daily_target),0)/item_pack_sizes.number_of_doses) as vials_required,
            round(ifnull(Sum(campaign_data.vials_used), 0)) AS vials_used
            FROM
            campaign_targets
            INNER JOIN warehouses ON campaign_targets.warehouse_id = warehouses.pk_id
            INNER JOIN locations ON locations.pk_id = warehouses.district_id
            INNER JOIN campaign_item_pack_sizes ON campaign_targets.campaign_id = campaign_item_pack_sizes.campaign_id
            INNER JOIN item_pack_sizes ON item_pack_sizes.pk_id = campaign_item_pack_sizes.item_pack_size_id
            INNER JOIN campaign_data ON campaign_targets.pk_id = campaign_data.campaign_target_id            
            where campaign_targets.campaign_id=$camp_id and warehouses.status = 1 "
                . "$where
            GROUP BY warehouses.district_id,campaign_targets.campaign_id,item_pack_sizes.pk_id";

        $row = $this->_em->getConnection()->prepare($str_sql);
        $row->execute();
        return $row->fetchAll();
    }

}
