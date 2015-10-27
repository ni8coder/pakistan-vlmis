<?php

/**
 * Model_CampaignData
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    Logistics Management Information System for Vaccines
 * @subpackage Campaigns
 * @author     Ajmal Hussain <ajmaleyetii@gmail.com>
 * @version    2
 */
class Model_CampaignData extends Model_Base {

    private $_table;

    public function __construct() {
        parent::__construct();
        $this->_table = $this->_em->getRepository('CampaignData');
    }

    public function getSummaryHistory() {
        /* $str_qry = "
          ";
          $row = $this->_em->getConnection()->prepare($str_qry);
          $row->execute();
          return $row->fetchAll();
         */
        $arr_data = array(
            'a' => '90',
            'b' => '7',
            'c' => '3'
        );
        return $arr_data;
    }

    public function getCoverageReport() {
        $str_qry = "SELECT
                    locations.location_name,
                    SUM(campaign_data.daily_target) AS daily_target,
                    SUM(campaign_data.teams_reported) AS teams_reported,
                    SUM(campaign_data.target_age_six_months) AS target_age_six_months,
                    SUM(campaign_data.target_age_sixty_months) AS target_age_sixty_months,
                    SUM(campaign_data.record_not_accessible) AS record_not_accessible,
                    SUM(campaign_data.record_refusal) AS record_refusal,
                    SUM(campaign_data.coverage_not_accessible) AS coverage_not_accessible,
                    SUM(campaign_data.refusal_covered) AS refusal_covered,
                    SUM(campaign_data.coverage_mobile_children) AS coverage_mobile_children,
                    SUM(campaign_data.total_coverage) AS total_coverage
            FROM
                campaign_data
            INNER JOIN locations ON campaign_data.district_id = locations.pk_id
            INNER JOIN campaigns ON campaign_data.campaign_id = campaigns.pk_id
            WHERE 1=1";

        if (!empty($this->form_values['combo1_add']) || !empty($this->form_values['year'])) {
            if (!empty($this->form_values['combo1_add'])) {
                $str_qry .= " AND locations.province_id = " . $this->form_values['combo1_add'];
            }
            if (!empty($this->form_values['year'])) {
                $str_qry .= " AND YEAR(campaigns.date_from) = '" . $this->form_values['year'] . "'";
            }
        }

        $str_qry .= "
            GROUP BY
                campaign_data.district_id
                    ";
        // echo $str_qry;
        $row = $this->_em->getConnection()->prepare($str_qry);
        $row->execute();
        return $row->fetchAll();
    }

    public function getCampaignDetail() {
        $str_qry = "SELECT
                        Province.location_name AS Province,
                        District.location_name AS District,
                        Tehsil.location_name AS Tehsil,
                        UC.location_name AS UC,
                        SUM(campaign_data.daily_target) AS daily_target,
                        SUM(campaign_data.teams_reported) AS teams_reported,
                        SUM(campaign_data.target_age_six_months) AS target_age_six_months,
                        SUM(campaign_data.target_age_sixty_months) AS target_age_sixty_months,
                        SUM(campaign_data.record_not_accessible) AS record_not_accessible,
                        SUM(campaign_data.record_refusal) AS record_refusal,
                        SUM(campaign_data.coverage_not_accessible) AS coverage_not_accessible,
                        SUM(campaign_data.refusal_covered) AS refusal_covered,
                        SUM(campaign_data.coverage_mobile_children) AS coverage_mobile_children,
                        SUM(campaign_data.total_coverage) AS total_coverage
                FROM
                    locations AS Province
                INNER JOIN locations AS District ON Province.pk_id = District.province_id
                INNER JOIN locations AS Tehsil ON District.pk_id = Tehsil.parent_id
                INNER JOIN locations AS UC ON Tehsil.pk_id = UC.parent_id
                INNER JOIN warehouses ON UC.pk_id = warehouses.location_id
                INNER JOIN campaign_data ON warehouses.pk_id = campaign_data.warehouse_id
                INNER JOIN campaigns ON campaign_data.campaign_id = campaigns.pk_id
                WHERE
                    warehouses.stakeholder_id = '" . Model_Stakeholders::CAMPAIGN . "' 
                    and warehouses.status = 1 ";
        if (!empty($this->form_values['combo1_add'])) {
            $str_qry .= " AND Province.pk_id = " . $this->form_values['combo1_add'];
        }
        if (!empty($this->form_values['combo2_add'])) {
            $str_qry .= " AND District.pk_id = " . $this->form_values['combo2_add'];
        }
        if (!empty($this->form_values['year'])) {
            $str_qry .= " AND YEAR(campaigns.date_from) = '" . $this->form_values['year'] . "'";
        }
        if (!empty($this->form_values['campaign_id'])) {
            $str_qry .= " AND campaigns.pk_id = " . $this->form_values['campaign_id'];
        }

        $str_qry .= "
                GROUP BY
                    UC.pk_id
                    ";
        $row = $this->_em->getConnection()->prepare($str_qry);
        $row->execute();
        return $row->fetchAll();
    }

    public function getCoverageCatchUp() {
        $str_qry = "SELECT
                    Province.location_name AS Province,
                    District.location_name AS District,
                    Tehsil.location_name AS Tehsil,
                    UC.location_name AS UC,
                    SUM(campaign_data.daily_target) - SUM(campaign_data.total_coverage) AS stillMissedChilds,
                    SUM(campaign_data.record_not_accessible)  - SUM(campaign_data.coverage_not_accessible) AS stillNA,
                    SUM(campaign_data.record_refusal) - SUM(campaign_data.refusal_covered) AS stillRefusal,
                    Sum(campaign_data.coverage_not_accessible) AS coverage_not_accessible,
                    Sum(campaign_data.refusal_covered) AS refusal_covered,
                    SUM(IF(campaign_data.campaign_day >= (DATEDIFF(campaigns.date_to, campaigns.date_from) + 1), campaign_data.total_coverage, 0)) AS catchUPCoverage
                FROM
                    locations AS Province
                INNER JOIN locations AS District ON Province.pk_id = District.province_id
                INNER JOIN locations AS Tehsil ON District.pk_id = Tehsil.parent_id
                INNER JOIN locations AS UC ON Tehsil.pk_id = UC.parent_id
                INNER JOIN warehouses ON UC.pk_id = warehouses.location_id
                INNER JOIN campaign_data ON warehouses.pk_id = campaign_data.warehouse_id
                INNER JOIN campaigns ON campaign_data.campaign_id = campaigns.pk_id
                WHERE
                    warehouses.stakeholder_id = '" . Model_Stakeholders::CAMPAIGN . "' and warehouses.status = 1";

        if (!empty($this->form_values['combo1_add'])) {
            $str_qry .= " AND Province.pk_id = " . $this->form_values['combo1_add'];
        }
        if (!empty($this->form_values['combo2_add'])) {
            $str_qry .= " AND District.pk_id = " . $this->form_values['combo2_add'];
        }
        if (!empty($this->form_values['year'])) {
            $str_qry .= " AND YEAR(campaigns.date_from) = '" . $this->form_values['year'] . "'";
        }

        $str_qry .= "
                GROUP BY
                    UC.pk_id
                    ";

        $row = $this->_em->getConnection()->prepare($str_qry);
        $row->execute();
        return $row->fetchAll();
    }

    public function getLQASReport() {
        $form_values = $this->form_values;

        $level = $form_values['level'];
        $str_qry = '';

        switch ($level) {
            case 1:
                $where = '';
                if (!empty($form_values['campaign_id'])) {
                    $where = "WHERE
                      campaign_lqas_data.campaign_id = " . $form_values['campaign_id'] . "";
                }
                $str_qry = "SELECT
                    B.pk_id,
                    B.location_name,
                    SUM(IF(A.unvaccinated <= 3,1,0)) as passed,
                    SUM(IF(A.unvaccinated >= 4 AND A.unvaccinated <= 8,1,0)) as intermediate,
                    SUM(IF(A.unvaccinated >= 9,1,0)) as failed,
                    ROUND(A.checked/60) as lots_assessed,
                    ROUND(((A.checked-A.unvaccinated)/A.checked)*100) as vaccinatedPer,
                    ROUND((A.total_coverage/A.daily_target)*100) as rptDataVaccinatedPer
                   FROM
                    (
                     SELECT
                     Province.pk_id,
                     Sum(campaign_lqas_data.checked) AS checked,
                     SUM(campaign_lqas_data.unvaccinated) AS unvaccinated,
                     campaign_data.daily_target,
                     campaign_data.total_coverage
                     FROM
                     campaign_lqas_data
                     INNER JOIN campaign_data ON campaign_lqas_data.campaign_id = campaign_data.pk_id
                     INNER JOIN locations AS District ON campaign_lqas_data.district_id = District.pk_id
                     INNER JOIN locations AS Province ON District.parent_id = Province.pk_id
                     $where
                     GROUP BY
                      Province.pk_id
                    ) A
                   RIGHT JOIN (
                   SELECT
                    locations.pk_id,
                    locations.location_name
                   FROM
                    locations
                   WHERE
                    locations.geo_level_id = 2
                   ) B
                   ON A.pk_id = B.pk_id
                   GROUP BY 
                   B.pk_id";
                break;
            case 2:
                $where = '';
                if (!empty($form_values['campaign_id'])) {
                    $arr_where[] = "campaign_lqas_data.campaign_id = " . $form_values['campaign_id'] . "";
                }
                if (!empty($form_values['province_id'])) {
                    $arr_where[] = "Province.pk_id = " . $form_values['province_id'] . "";
                    $prov_where = " AND locations.province_id = " . $form_values['province_id'] . "";
                }
                if (is_array($arr_where)) {
                    $where = " WHERE " . implode(" AND ", $arr_where);
                }

                $str_qry = "SELECT
                    B.pk_id,
                    B.location_name,
                    SUM(IF(A.unvaccinated <= 3,1,0)) as passed,
                    SUM(IF(A.unvaccinated >= 4 AND A.unvaccinated <= 8,1,0)) as intermediate,
                    SUM(IF(A.unvaccinated >= 9,1,0)) as failed,
                    ROUND(A.checked/60) as lots_assessed,
                    ROUND(((A.checked-A.unvaccinated)/A.checked)*100) as vaccinatedPer,
                    ROUND((A.total_coverage/A.daily_target)*100) as rptDataVaccinatedPer
                   FROM
                    (
                     SELECT
                      District.pk_id,
                      Sum(campaign_lqas_data.checked) AS checked,
                      SUM(campaign_lqas_data.unvaccinated) AS unvaccinated,
                      campaign_data.daily_target,
                      campaign_data.total_coverage
                     FROM
                      campaign_lqas_data
                     INNER JOIN campaign_data ON campaign_lqas_data.campaign_id = campaign_data.pk_id
                     INNER JOIN locations AS District ON campaign_lqas_data.district_id = District.pk_id
                     INNER JOIN locations AS Province ON District.parent_id = Province.pk_id                     
                     " . $where . "
                     GROUP BY
                      District.pk_id
                    ) A
                   RIGHT JOIN (
                   SELECT
                    locations.pk_id,
                    locations.location_name
                   FROM
                    locations
                   WHERE
                    locations.geo_level_id = 4
                    
                    $prov_where
                   ) B
                   ON A.pk_id = B.pk_id
                   GROUP BY 
                   B.pk_id";
                break;
        }

        //echo $str_qry;
        //exit;

        $row = $this->_em->getConnection()->prepare($str_qry);
        $row->execute();
        return $row->fetchAll();
    }

    public function getCoverageMissedChildren() {
        $select = $joins = $where = $order = $group = "";
        if (!empty($this->form_values['office'])) {
            $office = $this->form_values['office'];
        } else {
            return;
        }

        if (!empty($this->form_values['campaign'])) {
            $where .= " AND campaign_data.campaign_id = " . $this->form_values['campaign'] . "";
        }
        if ($office == 1) {
            $select = "campaign_data.district_id,Province.pk_id,Province.location_name,";
            $joins = "INNER JOIN locations AS District ON campaign_data.district_id = District.pk_id
		INNER JOIN locations AS Province ON District.province_id = Province.pk_id";
            $group = "GROUP BY Province.pk_id";
            $order = "ORDER BY Province.pk_id";
        } elseif ($office == 2) {
            $select = "campaign_data.district_id AS pk_id, District.location_name,";
            if (!empty($this->form_values['combo1'])) {
                $where = " AND District.province_id = " . $this->form_values['combo1'];
            }
            $joins = "INNER JOIN locations AS District ON campaign_data.district_id = District.pk_id";
            $group = "GROUP BY campaign_data.district_id";
            $order = "ORDER BY District.location_name";
        } elseif ($office == 6) {
            $select = "warehouses.pk_id,warehouses.warehouse_name as location_name, campaign_data.warehouse_id,";
            if (!empty($this->form_values['combo2'])) {
                $where = " AND campaign_data.district_id = " . $this->form_values['combo2'];
            }
            $joins = "INNER JOIN warehouses ON campaign_data.warehouse_id = warehouses.pk_id";
            $group = "GROUP BY campaign_data.warehouse_id";
            $order = "ORDER BY warehouses.warehouse_name";
        }

        $str_qry = "SELECT
                    A.pk_id,
                    A.location_name,
                    A.totalTarget,
                    A.totalCoverage,
                    ROUND((A.totalCoverage / A.totalTarget) * 100, 1) AS coveragePer,
                    A.NA,
                    ROUND((A.NA/ A.totalTarget) * 100, 1)  AS NAPer,
                    A.refusal,
                    ROUND((A.refusal/ A.totalTarget) * 100, 1)  AS refusalPer,
                    A.NA + A.refusal AS total,
                    ROUND(((A.NA + A.refusal) / A.totalTarget) * 100, 1) AS totalPer 
                FROM
                    (
                    SELECT
                        $select 
                        Sum(campaign_data.daily_target) AS totalTarget,
                        Sum(campaign_data.total_coverage) AS totalCoverage,
                        Sum(campaign_data.record_not_accessible) AS NA,
                        Sum(campaign_data.record_refusal) AS refusal                        
                    FROM
                        campaign_data
                        $joins
                    WHERE 1=1
                    $where
                    $group
                    $order
                    ) A ";
        //die($str_qry);

        $row = $this->_em->getConnection()->prepare($str_qry);
        $row->execute();
        return $row->fetchAll();
    }

    public function getStatusNidsReport() {
        $office = $this->form_values['office'];
        if (!empty($this->form_values['campaign'])) {
            $where = "WHERE
                campaign_data.campaign_id = '" . $this->form_values['campaign'] . "'";
        } else {
            $where = "";
        }

        if (!empty($office)) {
            $office = $this->form_values['office'];
        } else {
            $office = 1;
        }
        //  App_Controller_Functions::pr($this->form_values);
        //  $office =2;
        if ($office == 1) {
            $str_qry = "SELECT
	B.location_name,
	SUM(A.totalTarget) AS totalTarget,
	SUM(A.totalCoverage) AS totalCoverage,
	ROUND((SUM(A.totalCoverage) / SUM(A.totalTarget) * 100), 1) AS coveragePer,
	COUNT(A.union_council_id) as noOfUc,
	SUM(IF(A.coveragePer < 30, 1, 0)) AS thiryPer,
	SUM(IF(A.coveragePer >= 30 AND A.coveragePer < 50, 1, 0)) AS fityPer,
	SUM(IF(A.coveragePer >= 50 AND A.coveragePer < 70, 1, 0)) AS seventyPer,
	SUM(IF(A.coveragePer >= 70 AND A.coveragePer < 90, 1, 0)) AS nintyPer
FROM
	(
		SELECT
			campaign_data.union_council_id,
			campaign_data.district_id,
			SUM(campaign_data.daily_target) AS totalTarget,
			SUM(campaign_data.total_coverage) AS totalCoverage,
			ROUND((SUM(campaign_data.total_coverage) / SUM(campaign_data.daily_target)) * 100, 1) AS coveragePer
		FROM
			campaign_data
		$where
		GROUP BY
			campaign_data.union_council_id
	) A
JOIN (
	SELECT
		Province.location_name,
		locations.pk_id,
		Province.pk_id AS ProvinceId
	FROM
		locations
	INNER JOIN locations AS Province ON locations.province_id = Province.pk_id
	WHERE
		locations.geo_level_id = 4
)B
ON A.district_id = B.pk_id
GROUP BY B.ProvinceId ";
        } else if ($office == 2) {
            $str_qry = "SELECT
	B.location_name,
	SUM(A.totalTarget) AS totalTarget,
	SUM(A.totalCoverage) AS totalCoverage,
	ROUND((SUM(A.totalCoverage) / SUM(A.totalTarget) * 100), 1) AS coveragePer,
	COUNT(A.union_council_id) as noOfUc,
	SUM(IF(A.coveragePer < 30, 1, 0)) AS thiryPer,
	SUM(IF(A.coveragePer >= 30 AND A.coveragePer < 50, 1, 0)) AS fityPer,
	SUM(IF(A.coveragePer >= 50 AND A.coveragePer < 70, 1, 0)) AS seventyPer,
	SUM(IF(A.coveragePer >= 70 AND A.coveragePer < 90, 1, 0)) AS nintyPer
FROM
	(
		SELECT
			campaign_data.union_council_id,
			campaign_data.district_id,
			SUM(campaign_data.daily_target) AS totalTarget,
			SUM(campaign_data.total_coverage) AS totalCoverage,
			ROUND((SUM(campaign_data.total_coverage) / SUM(campaign_data.daily_target)) * 100, 1) AS coveragePer
		FROM
			campaign_data
		$where
		GROUP BY
			campaign_data.district_id
	) A
JOIN (
SELECT
	locations.pk_id,
	locations.location_name
FROM
	locations
WHERE
	locations.geo_level_id = 4
        and
  locations.province_id = '" . $this->form_values['combo1'] . "'
)B
ON A.district_id = B.pk_id
GROUP BY A.district_id";
        } else {
            $str_qry = "";
        }
        $row = $this->_em->getConnection()->prepare($str_qry);
        $row->execute();
        return $row->fetchAll();
    }

    public function getUnderPerformingDistricts() {
        $form_values = $this->form_values;

        if (!empty($form_values['campaign_id'])) {
            $arr_where[] = "campaign_data.campaign_id = " . $form_values['campaign_id'] . "";
        }
        if (!empty($form_values['combo1'])) {

            $arr_where[] = "locations.province_id = " . $form_values['combo1'] . "";
        }

        if (is_array($arr_where)) {
            $where = " WHERE " . implode(" AND ", $arr_where);
        }

        $str_qry = "SELECT
                    locations.pk_id,
                    locations.location_name,
                    Sum(campaign_data.daily_target) AS totalTarget,
                    Sum(campaign_data.total_coverage) AS vaccinated,
                    ROUND((SUM(campaign_data.total_coverage) / SUM(campaign_data.daily_target)) * 100) AS vaccinatedPer,
                    campaign_data.campaign_id
                    FROM
                            campaign_data
                    INNER JOIN locations ON campaign_data.district_id = locations.pk_id
                    
                    " . $where . "
                    GROUP BY
                            locations.pk_id";


        $row = $this->_em->getConnection()->prepare($str_qry);
        $row->execute();
        return $row->fetchAll();
    }

    public function getUnderPerformingDistrictsSummary() {
        $form_values = $this->form_values;

        if (!empty($form_values['campaign_id'])) {
            $arr_where = "where campaign_data.campaign_id = " . $form_values['campaign_id'] . "";
        }
        if (!empty($form_values['campaign_id'])) {
            $arr_where_d = "where campaign_districts.campaign_id = " . $form_values['campaign_id'] . "";
        }



        $str_qry = "SELECT
	B.location_name,
	SUM(A.reported) AS reported,
	SUM(IF(A.coveragePer < 90, 1, 0)) AS less90PerCoverage
FROM
	(
		SELECT
			campaign_data.district_id,
			locations.province_id,
			COUNT(DISTINCT campaign_data.district_id) AS reported,
			ROUND((SUM(campaign_data.total_coverage) / SUM(campaign_data.daily_target)) * 100) AS coveragePer
		FROM
			campaign_data
		INNER JOIN locations ON campaign_data.district_id = locations.pk_id
		
			" . $arr_where . "
		GROUP BY
			locations.pk_id
	) A
RIGHT JOIN (
SELECT DISTINCT
	Province.pk_id,
	Province.location_name
FROM
	campaign_districts
INNER JOIN locations AS District ON campaign_districts.district_id = District.pk_id
INNER JOIN locations AS Province ON District.province_id = Province.pk_id
" . $arr_where_d . "
) B
ON A.province_id = B.pk_id
GROUP BY
	A.province_id";


        $row = $this->_em->getConnection()->prepare($str_qry);
        $row->execute();
        return $row->fetchAll();
    }

    public function getUnderPerformingUcs() {
        $form_values = $this->form_values;
        // App_Controller_Functions::pr($form_values);
        if (!empty($form_values['campaign_id'])) {
            $arr_where[] = "campaign_data.campaign_id = " . $form_values['campaign_id'] . "";
        }
        if (!empty($form_values['combo1'])) {

            $arr_where[] = "districts.province_id = " . $form_values['combo1'] . "";
        }
        if (!empty($form_values['combo2'])) {
            $arr_where[] = "campaign_data.district_id = " . $form_values['combo2'] . "";
        }

        $arr_where[] = "warehouses.status = 1";
        if (is_array($arr_where)) {
            $where = " WHERE " . implode(" AND ", $arr_where);
        }

        $str_qry = "SELECT
         districts.location_name,
                warehouses.warehouse_name,
                SUM(campaign_data.daily_target) AS totalTarget,
                SUM(campaign_data.total_coverage) AS vaccinated,
                ROUND((SUM(campaign_data.total_coverage) / SUM(campaign_data.daily_target)) * 100) AS vaccinatedPer
        FROM
                campaign_data
        INNER JOIN warehouses ON campaign_data.warehouse_id = warehouses.pk_id
        INNER JOIN locations as districts ON campaign_data.district_id = districts.pk_id
        " . $where . "
        GROUP BY
                campaign_data.warehouse_id";


        $row = $this->_em->getConnection()->prepare($str_qry);
        $row->execute();
        return $row->fetchAll();
    }

    public function getVaccineUtilizationAndWastage() {

        $form_values = $this->form_values;

        if (!empty($form_values['campaign_id'])) {
            $arr_where = "where campaign_data.campaign_id = " . $form_values['campaign_id'] . "";
        }




        $str_qry = "SELECT
	Province.pk_id,
	Province.location_name,
	SUM(campaign_data.vials_given) AS vials_given,
	SUM(campaign_data.vials_used) AS vials_used,
	SUM(campaign_data.vials_returned) AS vials_returned,
	SUM(campaign_data.vials_expired) AS vials_expired
FROM
campaign_data
INNER JOIN locations AS District ON campaign_data.district_id = District.pk_id
INNER JOIN locations AS Province ON District.province_id = Province.pk_id
$arr_where
GROUP BY
Province.pk_id
";


        $row = $this->_em->getConnection()->prepare($str_qry);
        $row->execute();
        return $row->fetchAll();
    }

}