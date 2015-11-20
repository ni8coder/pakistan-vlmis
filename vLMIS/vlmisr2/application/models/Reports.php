<?php

/**
 * Model_Reports
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    Logistics Management Information System for Vaccines
 * @subpackage Inventory Management
 * @author     Ajmal Hussain <ajmaleyetii@gmail.com>
 * @version    2
 */
class Model_Reports extends Model_Base {

    private $_table;
    public $wh_id;
    public $loc_id;

    public function __construct() {
        parent::__construct();
        // $this->_table = $this->_em->getRepository('Reports');
    }

    function getAllMonths() {
        $str_sql = $this->_em->createQueryBuilder()
                ->select("DATE_FORMAT(reporting_start_date,'%Y-%m-%d') as MaxDate")
                ->from("Model_WarehousesData")
                ->where("pk_id =  $this->wh_id")
                ->groupBy("MaxDate")
                ->orderBy("MaxDate ASC");
        $row = $str_sql->fetchArray();
        return $row;
    }

    function getAllMonths2() {
        $str_sql = $this->_em->createQueryBuilder()
                ->select("DATE_FORMAT(reporting_start_date,'%Y-%m-%d') as MaxDate")
                ->from("Model_HfDataMaster")
                ->where("pk_id =  $this->wh_id")
                ->groupBy("MaxDate")
                ->orderBy("MaxDate ASC");
        $row = $str_sql->fetchArray();
        return $row;
    }

    function getAllLogMonths2() {
        $str_sql = $this->_em->createQueryBuilder()
                ->select("DATE_FORMAT(vaccinationDate,'%Y-%m-%d') as MaxDate")
                ->from("Model_LogBook")
                ->where("pk_id =  $this->wh_id")
                ->groupBy("MaxDate")
                ->orderBy("MaxDate ASC");
        $row = $str_sql->fetchArray();
        return $row;
    }

    public function getLastReportDate() {
        if ($this->form_values['wh_id'] != 'null') {
            $d = Zend_Registry::get('first_month');

            $str_sql = $this->_em->createQueryBuilder()
                    ->select("IF(max(wd.reportingStartDate) > 0,max(wd.reportingStartDate), 0) as MaxDate")
                    ->from("WarehousesData", "wd")
                    ->where("wd.warehouse = " . $this->form_values['wh_id']);
            // ->andWhere("wd.issueBalance > 0");
            $row = $str_sql->getQuery()->getResult();

            if ($row[0]['MaxDate'] == 0) {
                return $d;
            } else {
                return $row[0]['MaxDate'];
            }
        }
    }

    public function getLastReportDate2() {
        if ($this->form_values['wh_id'] != 'null') {
            //     $d = Zend_Registry::get('first_month');
            // $warehouse = $this->_em->getRepository("Warehouses")->find($this->form_values['wh_id']);
            $this->form_values['wh_id'] = $this->form_values['wh_id'];
            $starting_date = $this->getStartingDate();
            // $starting_date = $warehouse;


            if ($starting_date == '2015-01-01 00:00:00' || $starting_date == '0000-00-00 00:00:00' || $starting_date == '') {
                $d = '2015-04-01';
            } else {
                $d = $starting_date;
            }

            $str_sql = $this->_em->createQueryBuilder()
                    ->select("IF(max(wd.reportingStartDate) > 0,max(wd.reportingStartDate), 0) as MaxDate")
                    ->from("HfDataMaster", "wd")
                    ->where("wd.warehouse = " . $this->form_values['wh_id']);

            $row = $str_sql->getQuery()->getResult();

            if ($row[0]['MaxDate'] == 0) {
                return $d;
            } else {
                return $row[0]['MaxDate'];
            }
        }
    }

    public function getStartingDate() {
        $str_sql = $this->_em->createQueryBuilder()
                ->select("w.startingOn as starting_date")
                ->from("Warehouses", "w")
                ->where("w.pkId = " . $this->form_values['wh_id']);
        //echo $str_sql->getQuery()->getSql();
        // ->andWhere("wd.issueBalance > 0");
        $row = $str_sql->getQuery()->getResult();


        return App_Controller_Functions::dateToDbFormat($row[0]['starting_date']);
    }

    public function getLogLastReportDate2() {
        if ($this->form_values['wh_id'] != 'null') {
            //   $d = Zend_Registry::get('first_month');
            $d = '2015-04-01';

            $str_sql = $this->_em->createQueryBuilder()
                    ->select("IF(max(wd.vaccinationDate) > 0,max(wd.vaccinationDate), 0) as MaxDate")
                    ->from("LogBook", "wd")
                    ->where("wd.warehouse = " . $this->form_values['wh_id']);
            // ->andWhere("wd.issueBalance > 0");
            $row = $str_sql->getQuery()->getResult();

            if ($row[0]['MaxDate'] == 0) {
                return $d;
            } else {
                return $row[0]['MaxDate'];
            }
        }
    }

    public function getLastCreatedDate() {
        if ($this->form_values['wh_id'] != 'null') {
            $d = Zend_Registry::get('first_month');

            $str_sql = $this->_em->createQueryBuilder()
                    ->select("IF(max(wd.createdDate) > 0,max(wd.createdDate), 0) as MaxDate")
                    ->from("WarehousesData", "wd")
                    ->where("wd.warehouse = " . $this->form_values['wh_id']);

            // echo $str_sql->getQuery()->getSql();
            $row = $str_sql->getQuery()->getResult();
            //echo $row[0]['MaxDate'];
            if ($row[0]['MaxDate'] != 0) {
                return $row[0]['MaxDate'];
            } else {
                return false;
            }
        }
    }

    public function getLastCreatedDate2() {
        if ($this->form_values['wh_id'] != 'null') {
            $d = Zend_Registry::get('first_month');

            $str_sql = $this->_em->createQueryBuilder()
                    ->select("IF(max(wd.createdDate) > 0,max(wd.createdDate), 0) as MaxDate")
                    ->from("HfDataMaster", "wd")
                    ->where("wd.warehouse = " . $this->form_values['wh_id']);

            // echo $str_sql->getQuery()->getSql();
            $row = $str_sql->getQuery()->getResult();
            //echo $row[0]['MaxDate'];
            if ($row[0]['MaxDate'] != 0) {
                return $row[0]['MaxDate'];
            } else {
                return false;
            }
        }
    }

    public function getLastLogCreatedDate2() {
        if ($this->form_values['wh_id'] != 'null') {
            $d = Zend_Registry::get('first_month');

            $str_sql = $this->_em->createQueryBuilder()
                    ->select("IF(max(wd.createdDate) > 0,max(wd.createdDate), 0) as MaxDate")
                    ->from("LogBook", "wd")
                    ->where("wd.warehouse = " . $this->form_values['wh_id']);

            // echo $str_sql->getQuery()->getSql();
            $row = $str_sql->getQuery()->getResult();
            //echo $row[0]['MaxDate'];
            if ($row[0]['MaxDate'] != 0) {
                return $row[0]['MaxDate'];
            } else {
                return false;
            }
        }
    }

    public function getLastModifiedDate() {
        if ($this->form_values['wh_id'] != 'null') {
            $d = Zend_Registry::get('first_month');

            $str_sql = $this->_em->createQueryBuilder()
                    ->select("IF(max(wd.modifiedDate) > 0,max(wd.modifiedDate), 0) as MaxDate")
                    ->from("WarehousesData", "wd")
                    ->where("wd.warehouse = " . $this->form_values['wh_id']);
//echo $str_sql->getQuery()->getSql();
//exit;
            $row = $str_sql->getQuery()->getResult();

            if ($row[0]['MaxDate'] != 0) {
                return $row[0]['MaxDate'];
            } else {
                return false;
            }
        }
    }

    public function getPendingReportMonth() {
        $LRM = $this->GetLastReportDate();
        $today = date("Y-m-01");
        $today_dt = new DateTime($today);
        $new_month_dt = new DateTime($LRM);

        if ($new_month_dt < $today_dt) {
            return $this->addDate($LRM, 1)->format('Y-m-d');
        } else {
            return "";
        }
    }

    /* public function getLast3Months() {
      $str_sql = $this->_em->createQueryBuilder()
      ->select("DATE_FORMAT(wd.reportingStartDate,'%Y-%m-%d') as MaxDate")
      ->from("WarehousesData", "wd")
      ->where("wd.warehouse =  " . $this->form_values['wh_id']);

      /* if ($this->form_values['wh_id'] == 427) {
      $str_sql->andWhere("DATE_FORMAT(wd.reportingStartDate,'%Y-%m') BETWEEN '2013-10' AND '2013-12'");
      } */
    /* if (in_array($this->form_values['wh_id'], array(3910,1851,3915,3922,3890,1921,240,3921,3911,3920,3912,4368,3916,1859,251,1902,3914,3913,3871,2010,4380,4381,8805,4382,8804,4383,8799,8798,8797,8792,8791,8790,8789,8788,8787,1986,1972,4350,4358,4366,4367,4369,4371,4372,4373,4505,4374,4375,4377,4378,1970,4379,1979,3909,1069,1428,1107,2408,2404,1443,1487,1570,1587,1600,1634,2244,454,438,1694,2196,2191,2172,782,1422,2648,1054,1043,2594,2173,1323,2540,889,888,875,2510,2505,2495,2489,2484,1367,2456,1735,2548,322,3887,3888,3889,3891,3892,3893,3895,3896,3897,3899,3900,3901,3902,3903,3904,3906,3907,3886,3885,3571,3572,3573,3604,2114,3632,3638,3639,3869,3870,3872,3873,3875,3877,3879,3881,3883,3908))) {
      $str_sql->andWhere("DATE_FORMAT(wd.reportingStartDate,'%Y-%m') BETWEEN '2014-01' AND '".date("Y-m")."'");
      } else if (in_array($this->form_values['wh_id'], array(2033,3819, 3817, 3831))) {
      $str_sql->andWhere("DATE_FORMAT(wd.reportingStartDate,'%Y-%m') BETWEEN '2014-06' AND '".date("Y-m")."'");
      } else if (in_array($this->form_values['wh_id'], array(3826, 3818, 3813, 3807, 3808, 3810, 3809, 3841, 3844, 3830, 3842, 4107, 3849, 3850, 3851, 3852, 3855))) {
      $str_sql->andWhere("DATE_FORMAT(wd.reportingStartDate,'%Y-%m') BETWEEN '2014-04' AND '".date("Y-m")."'");
      } else if (in_array($this->form_values['wh_id'], array(1157, 1523, 1768, 2227, 2649, 954, 474, 1775, 1776, 2501, 2429, 427, 2430, 2514, 2322, 2502))) {
      $str_sql->andWhere("DATE_FORMAT(wd.reportingStartDate,'%Y-%m') BETWEEN '2014-10' AND '".date("Y-m")."'");
      }
      //->andWhere("wd.issueBalance > 0")
      $str_sql->groupBy("wd.reportingStartDate")
      ->orderBy("wd.reportingStartDate", "DESC");
      //echo $str_sql->getQuery()->getSql();

      if (in_array($this->form_values['wh_id'], array(1497, 2491))) {
      $row = $str_sql->getQuery()->getResult();
      } elseif (in_array($this->form_values['wh_id'], array(2167, 1520, 2303, 2340, 2339, 1063))) {
      $row = $str_sql->getQuery()->setMaxResults(4)->getResult();
      } elseif (in_array($this->form_values['wh_id'], array(1776, 2430, 2514, 2502))) {
      $row = $str_sql->getQuery()->setMaxResults(12)->getResult();
      } elseif ($this->form_values['wh_id'] == 427) {
      $row = $str_sql->getQuery()->setMaxResults(15)->getResult();
      } elseif (in_array($this->form_values['wh_id'], array(2033,3910,1851,3915,3922,3890,1921,240,3921,3911,3920,3912,4368,3916,1859,251,1902,3914,3913,3871,2010,4380,4381,8805,4382,8804,4383,8799,8798,8797,8792,8791,8790,8789,8788,8787,1986,1972,4350,4358,4366,4367,4369,4371,4372,4373,4505,4374,4375,4377,4378,1970,4379,1979,3909,1069,1428,1107,2408,2404,1443,1487,1570,1587,1600,1634,2244,454,438,1694,2196,2191,2172,782,1422,2648,1054,1043,2594,2173,1323,2540,889,888,875,2510,2505,2495,2489,2484,1367,2456,1735,2548,322,3887,3888,3889,3891,3892,3893,3895,3896,3897,3899,3900,3901,3902,3903,3904,3906,3907,3886,3885,3571,3572,3573,3604,2114,3632,3638,3639,3869,3870,3872,3873,3875,3877,3879,3881,3883,3908))) {
      $row = $str_sql->getQuery()->getResult();
      } elseif (in_array($this->form_values['wh_id'], array(1157, 1523, 1768, 2227, 2649, 954, 474, 1775, 1776, 2501, 2429, 427, 2430, 2514, 2322, 2502, 3819, 3817, 3831))) {
      $row = $str_sql->getQuery()->getResult();
      } else if (in_array($this->form_values['wh_id'], array(3826, 3818, 3813, 3807, 3808, 3810, 3809, 3841, 3844, 3830, 3842, 4107, 3849, 3850, 3851, 3852, 3855))) {
      $row = $str_sql->getQuery()->getResult();
      } else {
      $row = $str_sql->getQuery()->setMaxResults(3)->getResult();
      }
      return $row;
      } */

    public function getLast3Months() {

        $warehouse = $this->_em->getRepository("Warehouses")->find($this->form_values['wh_id']);

        $str_sql = $this->_em->createQueryBuilder()
                ->select("DATE_FORMAT(wd.reportingStartDate,'%Y-%m-%d') as MaxDate")
                ->from("WarehousesData", "wd")
                ->where("wd.warehouse =  " . $this->form_values['wh_id']);

        $from_date = $warehouse->getFromEdit();
        $starting_date = $warehouse->getStartingOn();
        //$uptill_date = $warehouse->getWorkingUptill();
        $to_date = new DateTime("NOW");

        if (isset($starting_date) && checkdate($starting_date->format("m"), $starting_date->format("d"), $starting_date->format("Y"))) {
            $diff1 = $from_date->diff($starting_date);
            if ($diff1->format('%R%a') < 0) {
                $from_date = $starting_date;
            }
        }

        /* if (isset($uptill_date) && checkdate($uptill_date->format("m"), $uptill_date->format("d"), $uptill_date->format("Y"))) {
          $diff2 = $to_date->diff($uptill_date);
          if ($diff2->format('%R%a') < 0) {
          $to_date = $uptill_date;
          }
          } */

        if (isset($from_date) && $from_date != null && $from_date != '0000-00-00 00:00:00') {
            $str_sql->andWhere("DATE_FORMAT(wd.reportingStartDate,'%Y-%m') BETWEEN '" . $from_date->format("Y-m") . "' AND '" . $to_date->format("Y-m") . "'");
        }

        $str_sql->groupBy("wd.reportingStartDate")
                ->orderBy("wd.reportingStartDate", "DESC");

        if (isset($from_date) && checkdate($from_date->format("m"), $from_date->format("d"), $from_date->format("Y"))) {
            $row = $str_sql->getQuery()->getResult();
        } else {
            $row = $str_sql->getQuery()->setMaxResults(3)->getResult();
        }

        return $row;
    }

    public function getLast3Months2() {

        $warehouse = $this->_em->getRepository("Warehouses")->find($this->form_values['wh_id']);

        $str_sql = $this->_em->createQueryBuilder()
                ->select("DATE_FORMAT(wd.reportingStartDate,'%Y-%m-%d') as MaxDate")
                ->from("HfDataMaster", "wd")
                ->where("wd.warehouse =  " . $this->form_values['wh_id']);

        $from_date = $warehouse->getFromEdit();
        $starting_date = $warehouse->getStartingOn();
        //$uptill_date = $warehouse->getWorkingUptill();
        $to_date = new DateTime("NOW");

        if (isset($starting_date) && checkdate($starting_date->format("m"), $starting_date->format("d"), $starting_date->format("Y"))) {
            $diff1 = $from_date->diff($starting_date);
            if ($diff1->format('%R%a') < 0) {
                $from_date = $starting_date;
            }
        }

        /* if (isset($uptill_date) && checkdate($uptill_date->format("m"), $uptill_date->format("d"), $uptill_date->format("Y"))) {
          $diff2 = $to_date->diff($uptill_date);
          if ($diff2->format('%R%a') < 0) {
          $to_date = $uptill_date;
          }
          } */

        if (isset($from_date) && $from_date != null && $from_date != '0000-00-00 00:00:00') {
            $str_sql->andWhere("DATE_FORMAT(wd.reportingStartDate,'%Y-%m') BETWEEN '" . $from_date->format("Y-m") . "' AND '" . $to_date->format("Y-m") . "'");
        }

        $str_sql->groupBy("wd.reportingStartDate")
                ->orderBy("wd.reportingStartDate", "DESC");
        // echo $str_sql->getQuery()->getSql();
        if (isset($from_date) && checkdate($from_date->format("m"), $from_date->format("d"), $from_date->format("Y"))) {
            $row = $str_sql->getQuery()->getResult();
        } else {
            $row = $str_sql->getQuery()->setMaxResults(6)->getResult();
        }

        return $row;
    }

    public function getLogLast3Months2() {

        $warehouse_id = $this->form_values['wh_id'];

        $str_qry = "SELECT
                   DATE_FORMAT(wd.vaccination_date,'%Y-%m-%d') as MaxDate
                    FROM
                    log_book as wd
                    where
                    wd.warehouse_id =  $warehouse_id
                    GROUP BY DATE_FORMAT(wd.vaccination_date,'%Y-%m')
                    ORDER BY DATE_FORMAT(wd.vaccination_date,'%Y-%m') DESC"
                . " LIMIT 3";




        $this->_em = Zend_Registry::get('doctrine');
        $row1 = $this->_em->getConnection()->prepare($str_qry);
        $row1->execute();


        $row = $row1->fetchAll();

        // $row = date("Y-m-d");

        return $row;
    }

    public function getPreviousMonthReportDate($thismonth) {
        $new_date_temp = $this->addDate($thismonth, - 1);
        return $new_date_temp->format('Y-m-d');
    }

    public function addDate($date_str, $months) {
        $date = new DateTime($date_str);
        $start_day = $date->format('j');

        $date->modify("+{$months} month");
        $end_day = $date->format('j');

        if ($start_day != $end_day) {
            $date->modify('last day of last month');
        }
        return $date;
    }

    public function last3monthsUpdate() {
        $month_to_show = array('2013-05', '2013-06', '2013-07', '2013-08', '2013-09', '2013-10', '2013-11', '2013-12');

        $wh_Id = $this->form_values['wh_id'];
        $loc_Id = $this->form_values['loc_id'];
        // Show last three months for which date is entered
        $all_months = $this->getAllMonths();
        echo '<p>';
        $allmonths = '';
        for ($i = 0; $i < sizeof($all_months); $i++) {
            $l3m_dt = new DateTime($all_months[$i]);
            if (isset($_SESSION['dataentry_date']) && ($_SESSION['dataentry_date'] == $l3m_dt->format('Y-m-d'))) {
                $style = 'font-weight:bold';
            }
            if ($l3m_dt->format('Y-m-d') >= '2013-05-01') {
                $monthArr[] = $l3m_dt->format('Y-m');
                $do_3months = "Z" . base64_encode($wh_Id . '|' . $loc_Id . '|' . $l3m_dt->format('Y-m-') . '01|0');
                $allmonths .= "<a style='" . $style . "' href=monthly-consumption?do=" . $do_3months . ">" . $l3m_dt->format('M-y') . "</a> | ";
            }
        }

        $arrDiff = array_diff($month_to_show, $monthArr);

        foreach ($arrDiff as $val) {
            $do_3months = "Z" . base64_encode($wh_Id . '|' . $loc_Id . '|' . $val . '-01|1');
            $allmonths .= "<a style='" . $style . "' href=monthly-consumption?do=" . $do_3months . ">" . date('M-y', strtotime($val . '-01')) . "</a> | ";
        }

        echo substr($allmonths, 0, -2);
        echo '</p>';
    }

    public function last3monthsUpdate2() {
        $month_to_show = array('2013-05', '2013-06', '2013-07', '2013-08', '2013-09', '2013-10', '2013-11', '2013-12');

        $wh_Id = $this->form_values['wh_id'];
        $loc_Id = $this->form_values['loc_id'];
        // Show last three months for which date is entered
        $all_months = $this->getAllMonths2();
        echo '<p>';
        $allmonths = '';
        for ($i = 0; $i < sizeof($all_months); $i++) {
            $l3m_dt = new DateTime($all_months[$i]);
            if (isset($_SESSION['dataentry_date']) && ($_SESSION['dataentry_date'] == $l3m_dt->format('Y-m-d'))) {
                $style = 'font-weight:bold';
            }
            if ($l3m_dt->format('Y-m-d') >= '2013-05-01') {
                $monthArr[] = $l3m_dt->format('Y-m');
                $do_3months = "Z" . base64_encode($wh_Id . '|' . $loc_Id . '|' . $l3m_dt->format('Y-m-') . '01|0');
                $allmonths .= "<a style='" . $style . "' href=monthly-consumption2?do=" . $do_3months . ">" . $l3m_dt->format('M-y') . "</a> | ";
            }
        }

        $arrDiff = array_diff($month_to_show, $monthArr);

        foreach ($arrDiff as $val) {
            $do_3months = "Z" . base64_encode($wh_Id . '|' . $loc_Id . '|' . $val . '-01|1');
            $allmonths .= "<a style='" . $style . "' href=monthly-consumption2?do=" . $do_3months . ">" . date('M-y', strtotime($val . '-01')) . "</a> | ";
        }

        echo substr($allmonths, 0, -2);

        echo '</p>';
    }

    public function last3monthsLogUpdate2() {
        $month_to_show = array('2013-05', '2013-06', '2013-07', '2013-08', '2013-09', '2013-10', '2013-11', '2013-12');

        $wh_Id = $this->form_values['wh_id'];
        $loc_Id = $this->form_values['loc_id'];
        // Show last three months for which date is entered
        $all_months = $this->getAllLogMonths2();
        echo '<p>';
        $allmonths = '';
        for ($i = 0; $i < sizeof($all_months); $i++) {
            $l3m_dt = new DateTime($all_months[$i]);
            if (isset($_SESSION['dataentry_date']) && ($_SESSION['dataentry_date'] == $l3m_dt->format('Y-m-d'))) {
                $style = 'font-weight:bold';
            }
            if ($l3m_dt->format('Y-m-d') >= '2013-05-01') {
                $monthArr[] = $l3m_dt->format('Y-m');
                $do_3months = "Z" . base64_encode($wh_Id . '|' . $loc_Id . '|' . $l3m_dt->format('Y-m-') . '01|0');
                $allmonths .= "<a style='" . $style . "' href=log-book?do=" . $do_3months . ">" . $l3m_dt->format('M-y') . "</a> | ";
            }
        }

        $arrDiff = array_diff($month_to_show, $monthArr);

        foreach ($arrDiff as $val) {
            $do_3months = "Z" . base64_encode($wh_Id . '|' . $loc_Id . '|' . $val . '-01|1');
            $allmonths .= "<a style='" . $style . "' href=log-book?do=" . $do_3months . ">" . date('M-y', strtotime($val . '-01')) . "</a> | ";
        }

        echo substr($allmonths, 0, -2);

        echo '</p>';
    }

    public function last3months() {
        $wh_Id = $this->form_values['wh_id'];
        $loc_Id = $this->form_values['loc_id'];

        $last_report_date = $this->getLastReportDate();

        $last_3months = $this->getLast3Months();


        //$last_3months = array_reverse($last_3months);
        for ($i = 0; $i < sizeof($last_3months); $i++) {
            $L3M_dt = new DateTime($last_3months[$i]['MaxDate']);
            $dataMonthArr[] = $L3M_dt->format('Y-m-d');
        }

        if (isset($dataMonthArr)) {
            foreach ($dataMonthArr as $mon) {
                //$mon = $date->format( "Y-m-d" );
                $L3M_dt = new DateTime($mon);
                $do3Months = "Z" . base64_encode($wh_Id . '|' . $mon . '|0');
                $rows = $this->_em->getRepository('WarehousesDataDraft')->findBy(array('warehouse' => $wh_Id, 'reportingStartDate' => $L3M_dt->format("Y-m-d")));
                if (count($rows) > 0) {
                    $months[] = "<a href=monthly-consumption?do=" . $do3Months . " class='btn btn-xs green'>" . $L3M_dt->format('M-y') . " (Draft)</a>";
                } else {
                    $months[] = "<a href=monthly-consumption?do=" . $do3Months . " class='btn btn-xs green'>" . $L3M_dt->format('M-y') . "</a>";
                }
            }
            $months = array_reverse($months);
        }

        $L3M_dt = new DateTime("last day of previous month");
        if (substr($last_report_date, 0, 7) < $L3M_dt->format('Y-m')) {
            $L3M_dt = new DateTime($last_report_date . "-01");
            $L3M_dt->modify("+1 month");

            // Check if exist in draft
            $rows = $this->_em->getRepository('WarehousesDataDraft')->findBy(array('warehouse' => $wh_Id, 'reportingStartDate' => $L3M_dt->format("Y-m-d")));
            if (count($rows) > 0) {
                $do3Months = "Z" . base64_encode($wh_Id . '|' . $L3M_dt->format("Y-m-d") . '|0'); // It should be 0 in case of new report as well
                $months[] = "<a href=monthly-consumption?do=" . $do3Months . " class='btn btn-xs blue' >Add " . $L3M_dt->format('M-y') . " Report (Draft)</a>";
            } else {
                $do3Months = "Z" . base64_encode($wh_Id . '|' . $L3M_dt->format("Y-m-d") . '|1');
                $months[] = "<a href=monthly-consumption?do=" . $do3Months . " class='btn btn-xs blue' >Add " . $L3M_dt->format('M-y') . " Report</a>";
            }
        }
        echo implode('', $months);
    }

    public function last3months2() {
        $wh_Id = $this->form_values['wh_id'];
        $loc_Id = $this->form_values['loc_id'];

        $last_report_date = $this->getLastReportDate2();

        $last_3months = $this->getLast3Months2();
        //$last_3months = array_reverse($last_3months);
        for ($i = 0; $i < sizeof($last_3months); $i++) {
            $L3M_dt = new DateTime($last_3months[$i]['MaxDate']);
            $dataMonthArr[] = $L3M_dt->format('Y-m-d');
        }

        if (isset($dataMonthArr)) {
            foreach ($dataMonthArr as $mon) {

                //$mon = $date->format( "Y-m-d" );
                $L3M_dt = new DateTime($mon);
                $do3Months = "Z" . base64_encode($wh_Id . '|' . $mon . '|0');
                $rows = $this->_em->getRepository('HfDataMasterDraft')->findBy(array('warehouse' => $wh_Id, 'reportingStartDate' => $L3M_dt->format("Y-m-d")));
                if (count($rows) > 0) {
                    $months[] = "<a href=monthly-consumption2?do=" . $do3Months . " class='btn btn-xs green'>" . $L3M_dt->format('M-y') . " (Draft)</a>";
                } else {
                    $months[] = "<a href=monthly-consumption2?do=" . $do3Months . " class='btn btn-xs green'>" . $L3M_dt->format('M-y') . "</a>";
                }
            }
            $months = array_reverse($months);
        }

        $L3M_dt = new DateTime("last day of previous month");
        if (substr($last_report_date, 0, 7) < $L3M_dt->format('Y-m')) {
            $L3M_dt = new DateTime($last_report_date . "-01");
            $L3M_dt->modify("+1 month");

            // Check if exist in draft
            $rows = $this->_em->getRepository('HfDataMasterDraft')->findBy(array('warehouse' => $wh_Id, 'reportingStartDate' => $L3M_dt->format("Y-m-d")));
            if (count($rows) > 0) {
                $do3Months = "Z" . base64_encode($wh_Id . '|' . $L3M_dt->format("Y-m-d") . '|0'); // It should be 0 in case of new report as well
                $months[] = "<a href=monthly-consumption2?do=" . $do3Months . " class='btn btn-xs blue' >Add " . $L3M_dt->format('M-y') . " Report (Draft)</a>";
            } else {
                $do3Months = "Z" . base64_encode($wh_Id . '|' . $L3M_dt->format("Y-m-d") . '|1');
                $months[] = "<a href=monthly-consumption2?do=" . $do3Months . " class='btn btn-xs blue' >Add " . $L3M_dt->format('M-y') . " Report</a>";
            }
        }
        echo implode('', $months);
    }

    public function lastLog3months2() {
        $wh_Id = $this->form_values['wh_id'];
        $loc_Id = $this->form_values['loc_id'];

        $last_report_date = $this->getLogLastReportDate2();

        $last_3months = $this->getLogLast3Months2();
        //$last_3months = array_reverse($last_3months);
        for ($i = 0; $i < sizeof($last_3months); $i++) {
            $L3M_dt = new DateTime($last_3months[$i]['MaxDate']);
            $dataMonthArr[] = $L3M_dt->format('Y-m-d');
        }
//
//        if (isset($dataMonthArr)) {
//            foreach ($dataMonthArr as $mon) {
//                //$mon = $date->format( "Y-m-d" );
//                $L3M_dt = new DateTime($mon);
//                $do3Months = "Z" . base64_encode($wh_Id . '|' . $mon . '|0');
//                $rows = $this->_em->getRepository('LogBook')->findBy(array('warehouse' => $wh_Id, 'vaccinationDate' => $L3M_dt->format("Y-m-d")));
//                if (count($rows) > 0) {
//                    $months[] = "<a href=log-book-add?do=" . $do3Months . " class='btn btn-xs green'>" . $L3M_dt->format('M-y') . "</a>";
//                } else {
//                    $months[] = "<a href=log-book-add?do=" . $do3Months . " class='btn btn-xs green'>" . $L3M_dt->format('M-y') . "</a>";
//                }
//            }
//            $months = array_reverse($months);
//        }
//
//        $L3M_dt = new DateTime();
//        // $curr_date =  date("Y-m-d");
//        /// echo $curr_date;
//        echo $L3M_dt->format('Y-m');
//        echo substr($last_report_date, 0, 7);
//        exit;
//
//        if (substr($last_report_date, 0, 7) < $L3M_dt->format('Y-m')) {
//            $L3M_dt = new DateTime($last_report_date . "-01");
//            $L3M_dt->modify("+1 month");
//
//            // Check if exist in draft
//            $rows = $this->_em->getRepository('LogBook')->findBy(array('warehouse' => $wh_Id, 'vaccinationDate' => $L3M_dt->format("Y-m-d")));
//            if (count($rows) > 0) {
//                $do3Months = "Z" . base64_encode($wh_Id . '|' . $L3M_dt->format("Y-m-d") . '|0'); // It should be 0 in case of new report as well
//                $months[] = "<a href=log-book-add?do=" . $do3Months . " class='btn btn-xs blue' >Add " . $L3M_dt->format('M-y') . " </a>";
//            } else {
//                $do3Months = "Z" . base64_encode($wh_Id . '|' . $L3M_dt->format("Y-m-d") . '|1');
//                $months[] = "<a href=log-book-add?do=" . $do3Months . " class='btn btn-xs blue' >Add " . $L3M_dt->format('M-y') . " </a>";
//            }
//        }
//        echo implode('', $months);

        $end_date = date('Y') . '-' . date('m') . '-01';
//exit;
        $end_date = date('Y-m-d', strtotime("-1 days", strtotime("+1 month", strtotime($end_date))));
        //  $start_date = date('Y-m-d', strtotime("-364 days", strtotime($end_date)));
        $start_date = '2015-05-01';
        // Start date and End date
        $begin = new DateTime($start_date);
        $end = new DateTime($end_date);
        $diff = $begin->diff($end);
        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod($begin, $interval, $end);
        foreach ($period as $date) {

//echo 
            $log_book = new Model_LogBook();
            $rows = $log_book->getLogBook($wh_Id, $date->format("Y-m"));
            if (count($rows) > 0) {
                // echo $wh_Id;
                // echo $date->format("Y-m-d");
                $do3Months = "Z" . base64_encode($wh_Id . '|' . $date->format("Y-m-d") . '|0');
                $months[] = "<a href=log-book-add?do=" . $do3Months . " class='btn btn-xs green'>" . $date->format('M-y') . "</a>";
            } else {
                // echo $wh_Id;
                // echo $date->format("Y-m-d");
                $do3Months = "Z" . base64_encode($wh_Id . '|' . $date->format("Y-m-d") . '|1');
                $months[] = "<a href=log-book-add?do=" . $do3Months . " class='btn btn-xs blue' >Add " . $date->format('M-y') . " </a>";
            }

            // $str_date = $date->format("Y-m");
        }

        echo implode('', $months);
    }

    public function getMaxReportDate() {
        $str_sql = "SELECT IFNULL(month(MAX(reporting_start_date)),month(SYSDATE())) AS report_month , IFNULL(year(MAX(reporting_start_date)),year(SYSDATE())) AS report_year FROM warehouses_data";
        $row = $this->_em->getConnection()->prepare($str_sql);
        $row->execute();
        return $row->fetchAll();
    }

    public function getIndicators() {
        $str_sql = $this->_em->createQueryBuilder()
                ->select("r.reportId, r.reportTitle")
                ->from("Reports", "r")
                ->where("r.reportType = 1");
        $row = $str_sql->getQuery()->getResult();
        return $row;
    }

}
